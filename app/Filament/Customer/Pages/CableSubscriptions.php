<?php

namespace App\Filament\Customer\Pages;

use Carbon\Carbon;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Jobs\ProcessCable;

use App\Models\Beneficiary;
use App\Models\Transaction;
use App\Models\MobileAirtime;
use App\Models\CableSubscription;
use App\Models\TransactionPuller;
use App\Models\PaymentIntegration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Actions\Action;


class CableSubscriptions extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-tv';

    protected static string $view = 'filament.customer.pages.cable-subscriptions';

    protected static ?string $navigationLabel = "Cable Subs";
    protected static ?int $navigationSort = 4;  
    public $polling = false;
    public $testMesg = "Earlier...";

    private $customer_name = "nill";




    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill();
    }

    public $ngn = "₦";

    public function form(Form $form): Form
    {
        $user_balance = number_format(auth()->user()->balance,2);
        $ngn = "₦";

        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        Select::make('cableType')
                            ->options([
                                'startimes' => 'StarTimes',
                                'dstv' => 'DSTV',
                                'gotv' => 'GOTV',
                            ])
                        
                            ->required()
                            ->live(),

                        Select::make('cablePlan')
                                ->options(function(Callable $get){
                                  
                                                $cable_options = CableSubscription::where('service_id', $get('cableType'))->where('active_status',true)->pluck('name','api_code');
                                    
                                            return $cable_options;
                                      
                                })
                                ->live()
                                ->preload()
                                ->searchable()
                                ->afterStateUpdated(function($state, Set $set){
                                    

                                    $cable_db = CableSubscription::where('api_code',$state)->where('active_status',true);
                                    $cable_price = $cable_db->first()->price;
                                    $cable_id = $cable_db->first()->id;

                                    
                                    $user_package = auth()->user()->package;


                                    $cable_charges = 0;

                                    //Determine the charges....
                                    
                                    switch($user_package){

                                        case 'primary':
                                            $cable_charges = CableSubscription::find($cable_id)->primary_charges;
                                        break;

                                        case 'agent':
                                            $cable_charges = CableSubscription::find($cable_id)->agent_charges;
                                        break;

                                        case 'special':
                                            $cable_charges = CableSubscription::find($cable_id)->special_charges;
                                        break;

                                        case 'api':
                                            $cable_charges = CableSubscription::find($cable_id)->api_charges;
                                        break;



                                    }

                                   // dd($cable_price);
                                   $set('total', number_format($cable_price,2));
                                   $set('charges', number_format($cable_charges,2));

                                  // dd($cable_charges);


                                })
                                ->required(),

                                Select::make('subscription_type')
                                    ->options([
                                        'change' => 'BOUQUET CHANGE',
                                        'renew' => 'BOUQUET RENEWAL',
                                    ])
                                    ->searchable()
                                    ->required(function(Callable $get){
                                        if($get('cableType') == 'dstv' || $get('cableType') == 'gotv'){
                                            return true;
                                        }
                                    })
                                    ->visible(function(Callable $get){
                                        if($get('cableType') == 'dstv' || $get('cableType') == 'gotv'){
                                            return true;
                                        }
                                    })

                    ])->columns(2),

                    Section::make('Order')
                                ->schema([
                                    TextInput::make('decoder_number')
                                        ->required()
                                        ->suffixAction(
                                         Action::make('VerifyDecoder')
                                        ->icon('heroicon-m-check-badge')
                                        //->requiresConfirmation()
                                        ->tooltip('Verify Decoder')
                                        ->action(function (Set $set, $state, Callable $get) {
                                            
                                            //Send a verify request to the VTPASS Endpoint...
                                            $response = Http::withHeaders([
                                                'api-key' => PaymentIntegration::first()->vtpass_api_key,
                                                'secret-key' => PaymentIntegration::first()->vtpass_secret_key,
                                                'Content-Type' => 'application/json'
                                            ])->post('https://api-service.vtpass.com/api/merchant-verify', [
                                                'billersCode' => $state,
                                                'serviceID' => $get('cableType'),
                                            ]);
                                            
                                            // Check if the request was successful
                                            if ($response->successful()) {
                                                $responseData = $response->json();
                                                // Handle response data
                                                $slicedResponce = json_decode($response->body(), true);

                                                
                                                

                                                if (array_key_exists('Customer_Name', $slicedResponce['content'])){
                                                
                                                    $customerName = $slicedResponce['content']['Customer_Name'];
                                                    
                                                    $this->dispatch(
                                                    'alert',
                                                    title: 'Confirmed',
                                                    text: "<b>Customer Name: </b> ".$customerName,
                                                    button: 'Got it!',
                                                    type: 'success'
                                                );


                                                }else{
                                                    $this->dispatch(
                                                        'alert',
                                                        title: 'Invalid Decoder Number',
                                                        text: "Kindly provide a valid Decoder Number and try again!",
                                                        button: 'Got it!',
                                                        type: 'warning'
                                                    );

                                                }

                                                //dd($slicedResponce);

                                                
                                                

                                            } else {
                                                // Handle unsuccessful response
                                                //dd("Failed Here".$response." || ".$state." || ".$get('cableType'));

                                                $this->dispatch(
                                                    'alert',
                                                    title: 'Something went wrong',
                                                    text: 'Please try again later or reach out to our reps for help',
                                                    button: 'Got it!',
                                                    type: 'error'
                                                );

                                            }



                                    }),
                                )
                                        
                                        ,
                                    TextInput::make('phone_number')
                                        ->required(),
                                    TextInput::make('transaction_pin')
                                        ->password()
                                        ->length(4)
                                        ->revealable()
                                        ->required(),

                                ])->columns(3),

                    Section::make('Checkout')
                    ->schema([

                        TextInput::make('total')
                            ->default('00.00')
                            ->label('Total')
                            ->prefix("$ngn")
                            ->readOnly(),

                        TextInput::make('charges')
                            ->default('00.00')
                            ->prefix("$ngn")
                            ->readOnly(),

                    ])->columns(2),

                    TextInput::make('customer_name')
                        ->readOnly()
                        ->visible(function(){
                            if($this->customer_name != "nill" && $this->customer_name != ""){
                                return true;
                            }else{
                                return false;
                            }
                        })
                        ->default($this->customer_name)
            ])
            ->statePath('data');
    }
    
  // Function to generate a random alphanumeric string
  private function generateRandomString($length = 10)
  {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $randomString = '';

      for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[rand(0, strlen($characters) - 1)];
      }

      return $randomString;
  }

  protected function getFormActions(): array
{
    return [


        Action::make('purchase')
            ->color('primary')
            ->icon('heroicon-m-shopping-bag')
            ->requiresConfirmation()
            ->submit('purchase'),



    ];
}


public $loadingWheel = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" style="shape-rendering: auto; display: block; background: rgb(255, 255, 255);" width="60" height="60" xmlns:xlink="http://www.w3.org/1999/xlink"><g><g transform="rotate(0 50 50)">
<rect fill="#f55302" height="12" width="6" ry="6" rx="3" y="24" x="47">
  <animate repeatCount="indefinite" begin="-0.9166666666666666s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
</rect>
</g><g transform="rotate(30 50 50)">
<rect fill="#f55302" height="12" width="6" ry="6" rx="3" y="24" x="47">
  <animate repeatCount="indefinite" begin="-0.8333333333333334s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
</rect>
</g><g transform="rotate(60 50 50)">
<rect fill="#f55302" height="12" width="6" ry="6" rx="3" y="24" x="47">
  <animate repeatCount="indefinite" begin="-0.75s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
</rect>
</g><g transform="rotate(90 50 50)">
<rect fill="#f55302" height="12" width="6" ry="6" rx="3" y="24" x="47">
  <animate repeatCount="indefinite" begin="-0.6666666666666666s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
</rect>
</g><g transform="rotate(120 50 50)">
<rect fill="#f55302" height="12" width="6" ry="6" rx="3" y="24" x="47">
  <animate repeatCount="indefinite" begin="-0.5833333333333334s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
</rect>
</g><g transform="rotate(150 50 50)">
<rect fill="#f55302" height="12" width="6" ry="6" rx="3" y="24" x="47">
  <animate repeatCount="indefinite" begin="-0.5s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
</rect>
</g><g transform="rotate(180 50 50)">
<rect fill="#f55302" height="12" width="6" ry="6" rx="3" y="24" x="47">
  <animate repeatCount="indefinite" begin="-0.4166666666666667s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
</rect>
</g><g transform="rotate(210 50 50)">
<rect fill="#f55302" height="12" width="6" ry="6" rx="3" y="24" x="47">
  <animate repeatCount="indefinite" begin="-0.3333333333333333s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
</rect>
</g><g transform="rotate(240 50 50)">
<rect fill="#f55302" height="12" width="6" ry="6" rx="3" y="24" x="47">
  <animate repeatCount="indefinite" begin="-0.25s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
</rect>
</g><g transform="rotate(270 50 50)">
<rect fill="#f55302" height="12" width="6" ry="6" rx="3" y="24" x="47">
  <animate repeatCount="indefinite" begin="-0.16666666666666666s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
</rect>
</g><g transform="rotate(300 50 50)">
<rect fill="#f55302" height="12" width="6" ry="6" rx="3" y="24" x="47">
  <animate repeatCount="indefinite" begin="-0.08333333333333333s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
</rect>
</g><g transform="rotate(330 50 50)">
<rect fill="#f55302" height="12" width="6" ry="6" rx="3" y="24" x="47">
  <animate repeatCount="indefinite" begin="0s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
</rect>
</g><g></g></g><!-- [ldio] generated by https://loading.io --></svg>';




public function pollTransaction()
{
    $transactionNotification = TransactionPuller::where('user_id', auth()->id())->latest()->first();
    
    //dd($transactionNotification);

    if($transactionNotification){

        $this->dispatch(
            'alert',
            type: $transactionNotification->status,
            title: $transactionNotification->title,
            text: $transactionNotification->message,
            button: 'Got it!'
        );
        

        $userId = auth()->id();

        TransactionPuller::where('user_id',$userId)->delete();

        $this->polling = false;
        $this->testMesg = "OFF";
        
        
    }else{
        $this->polling = false;
    }
    
}

public function purchase(): void
{


    $this->polling = true;
    $ngn = "₦";
    date_default_timezone_set('Africa/Lagos');
    $phone_number = $this->form->getState()['phone_number'];
    $cable_type = $this->form->getState()['cableType'];
    $cable_plan = $this->form->getState()['cablePlan'];
    $transaction_pin = $this->form->getState()['transaction_pin'];

    $decoder_number = $this->form->getState()['decoder_number'];

    $subscription_type = "";

    if(isset($this->form->getState()['subscription_type'])){
        $subscription_type = $this->form->getState()['subscription_type'];
    }



    $cable_pull = CableSubscription::where('api_code',$cable_plan)->where('service_id',$cable_type)->first();

    $cable_amount = $cable_pull->price;
    $cable_id = $cable_pull->id;
    $cable_vendor = $cable_pull->vendor_name;


        $currentDateTime = Carbon::now();

        $formattedDateTime = $currentDateTime->format('YmdHi');
        $randomString = $this->generateRandomString(10);
        $requestId = $formattedDateTime . $randomString;

        if(strlen($requestId) < 12) {
            $requestId .= $this->generateRandomString(12 - strlen($requestId));
        }

        //Check if user is not banned
        if(auth()->user()->user_status != true){
            return;
        }




         //Check if Cable status is enabled
         if(CableSubscription::find($cable_id)->active_status != true){

            $this->dispatch(
                'alert',
                title:'Invalid Operation',
                text: 'The requested cable value is not available',
                type: 'error',
                button: 'Got it!'
            );
            return;
        }


        $user_package = auth()->user()->package;


                                    $cable_charges = 0;

                                    //Determine the charges....
                                    
                                    switch($user_package){

                                        case 'primary':
                                            $cable_charges = CableSubscription::find($cable_id)->primary_charges;
                                        break;

                                        case 'agent':
                                            $cable_charges = CableSubscription::find($cable_id)->agent_charges;
                                        break;

                                        case 'special':
                                            $cable_charges = CableSubscription::find($cable_id)->special_charges;
                                        break;

                                        case 'api':
                                            $cable_charges = CableSubscription::find($cable_id)->api_charges;
                                        break;



                                    }
                

                                    
                //Check Transaction Pin Before moving on!

                $user_pin = auth()->user()->transaction_pin;
                $user_balance = auth()->user()->balance;
                $amount_to_pay = (double)$cable_amount + (double)$cable_charges;

                if($user_pin == $transaction_pin){

                    //Check if User's balance can withstand the transaction

                    if($user_balance >= $amount_to_pay){

                        //Proceed with the operation...


                        $userId = auth()->id();
                        ProcessCable::dispatch($userId,$requestId,$cable_id,$amount_to_pay,$phone_number,$decoder_number,$cable_type,$cable_plan,$cable_amount,$cable_charges,$subscription_type,$cable_vendor);

                        $this->dispatch(
                            'alert', 
                            title: 'Transaction Initiated',
                            text: '<center>'.$this->loadingWheel.'</center> Your Cable Transaction is in progress...',
                            button: 'Got it!',
                            type: 'info'
                             );
                        // switch($cable_vendor){
                        //     case 'vtpass':
                        //         $this->buyCableFromVtPass($requestId,$cable_id,$amount_to_pay,$phone_number,$decoder_number,$cable_type,$cable_plan,$cable_amount,$cable_charges,$subscription_type);
                        //     break;
                            
                        // }


                    }else{

                        $amount_remain = $amount_to_pay-$user_balance;
        
                        $this->dispatch(
                            'alert',
                            title:'Insufficient Fund!',
                            text:"Kindly top up your wallet with $ngn".number_format($amount_remain,2)." or more and try again.",
                            type:'warning',
                            button:'Got it!'
        
                        );
                        return;
        
        
                    }


                }else{
                
                    $this->dispatch(
                        'alert',
                        title:'Incorrect Transaction Pin',
                        text:'Kindly provide your correct 4-digit transaction pin.',
                        type:'warning',
                        button:'Got it!'
    
                    );
                    return;
    
                }




}


    public function buyCableFromVtPass($requestId,$cable_id,$amount_to_pay,$phone_number,$decoder_number,$service_id,$variation_code,$cable_amount,$cable_charges,$subscription_type){

        $ngn = "₦";

        $requestId .= "_CABLE";
            //Check my balance before moving on...
            $myVtPassBalance = 2000000;

            if($myVtPassBalance > $amount_to_pay){

                $old_balance = auth()->user()->balance;
                $new_balance = (double)$old_balance - (double)$amount_to_pay;
                $transactionStatus = "pending";
                $temporary_message = "Purchase of ". ucfirst($service_id) ." ".strtoupper($variation_code)." package to $decoder_number";
                $endpoint = CableSubscription::find($cable_id)->endpoint;
                $cable_name = CableSubscription::find($cable_id)->name;
                $customerName = "";
                $requestAmount = 0;




                //Check the billerCode and get the customer's name.....

                //Send a verify request to the VTPASS Endpoint...
                $response = Http::withHeaders([
                    'api-key' => PaymentIntegration::first()->vtpass_api_key,
                    'secret-key' => PaymentIntegration::first()->vtpass_secret_key,
                    'Content-Type' => 'application/json'
                ])->post('https://api-service.vtpass.com/api/merchant-verify', [
                    'billersCode' => $decoder_number,
                    'serviceID' => $service_id,
                ]);
                
                // Check if the request was successful
                if ($response->successful()) {
                    $responseData = $response->json();
                    // Handle response data
                    $slicedResponce = json_decode($response->body(), true);


                    if (array_key_exists('Renewal_Amount', $slicedResponce['content'])){
                        $requestAmount = $slicedResponce['content']['Renewal_Amount'];
                    }
                    if (array_key_exists('Customer_Name', $slicedResponce['content'])){
                    
                        $customerName = $slicedResponce['content']['Customer_Name'];



                    }else{  

                        $this->dispatch(
                            'alert',
                            title: 'Invalid Decoder Number',
                            text: "Kindly provide a valid Decoder Number and Try again!",
                            button: 'Got it!',
                            type: 'error'
                        );

                        return;
                    }


                    DB::table('users')
                    ->where('id', auth()->id())
                    ->update([
                        'balance' => $new_balance,
                    ]);
    
                    
    
                    Transaction::create([
                        'type' => 'cable',
                        'user_id' => auth()->id(),
                        'api_response' => $temporary_message,
                        'status' => $transactionStatus,
                        'note' => $temporary_message,
                        'phone_number' => $phone_number,
                        'amount' => "$ngn".number_format($cable_amount,2),
                        'old_balance' => "$ngn".number_format($old_balance,2),
                        'new_balance' => "$ngn".number_format($new_balance,2),
                        'reference_number' => $requestId,
                        'plan_name' => $cable_name,
                        'iuc_number' => $decoder_number,
                        'cable_plan' => strtoupper($variation_code),
                        'charges' => "$ngn".number_format($cable_charges,2)
                    ]);
    
    
                    $payload = [
                        'phone' => $phone_number,
                        'serviceID' => $service_id,
                        'billersCode' => $decoder_number,
                        'variation_code' => $variation_code,
                        'request_id' => $requestId,
                    ];
                    
                   //Now, handle the transaction based on the cable type

                if($service_id === 'startimes'){
                    
                }elseif($service_id === 'dstv'){    


                    $payload['subscription_type'] = $subscription_type;
                    $payload['amount'] = $requestAmount;

                }elseif($service_id === 'gotv'){

                    $payload['subscription_type'] = $subscription_type;
                    $payload['amount'] = $requestAmount;

                }

                    

                } else {
                    // Handle unsuccessful response
                    //dd("Failed Here".$response." || ".$state." || ".$get('cableType'));

                    $this->dispatch(
                        'alert',
                        title: 'Biller Err: Something went wrong',
                        text: 'Please try again later or reach out to our reps for help',
                        button: 'Got it!',
                        type: 'error'
                    );

                    return;

                }



                $responseCable = Http::withHeaders([
                    'api-key' => PaymentIntegration::first()->vtpass_api_key,
                    'secret-key' => PaymentIntegration::first()->vtpass_secret_key,
                    'Content-Type' => 'application/json'
                ])->post(trim($endpoint), $payload);


                $responsePurchase = json_decode($responseCable->body(), true);

                //dd($responsePurchase);
                if (isset($responsePurchase['content']['transactions']['status'])) {
                    $code = $responsePurchase['code'];
                    $content = $responsePurchase['content'];
                    $response_description = $responsePurchase['response_description'];
                    $requestId = $responsePurchase['requestId'];
                    $amount = $responsePurchase['amount'];
                    $transaction_date = $responsePurchase['transaction_date'];
                    $purchased_code = $responsePurchase['purchased_code'];
                    
                    // Extract variables from the 'content' array
                    $transactions = $content['transactions'];
                    
                    // Extract variables from the 'transactions' array
                    $status = $transactions['status'];
                    $channel = $transactions['channel'];
                    $transactionId = $transactions['transactionId'];
                    $method = $transactions['method'];
                    $platform = $transactions['platform'];
                    
                    $discount = $transactions['discount'];
                    $email = $transactions['email'];
                    $phone = $transactions['phone'];
                    $type = $transactions['type'];
                    $convinience_fee = $transactions['convinience_fee'];
                    $commission = $transactions['commission'];
                    $transaction_amount = $transactions['amount'];
                    $total_amount = $transactions['total_amount'];
                    $quantity = $transactions['quantity'];
                    $unit_price = $transactions['unit_price'];
                    
                    //". ucfirst($service_id) ." ".strtoupper($variation_code)." package to $decoder_number"

                    $successMessage = "You've successfully purchased ".ucfirst($service_id)." ".strtoupper($variation_code)." for <b>".$decoder_number."</b> on ".date("l jS \of F Y h:i:s A").".";

                        if($status === 'delivered'){

                            DB::table('transactions')
                            ->where('reference_number', $requestId)->where('user_id',auth()->id())
                            ->update([
                                'status' => "successful",
                                'api_response' => $successMessage,
                                'note' => $successMessage,
                                'customer_name' => $customerName,
                            ]);

                        
                        $this->dispatch(
                                'alert',
                                type:'success',
                                title:'Successful',
                                text:$successMessage." = (".$cable_name.")",
                                button:'Great!'
                            );


                        }elseif($status === 'failed'){
                    
                    DB::table('users')
                    ->where('id', auth()->id())
                    ->update([
                        'balance' => $old_balance,
                    ]);

                    DB::table('transactions')
                    ->where('reference_number', $requestId)->where('user_id',auth()->id())
                    ->update([
                        'status' => "failed",
                        'api_response' => "FAILED: ".$response_description,
                        'new_balance' => "$ngn".number_format($old_balance,2),
                        'charges' => $ngn."00.00",
                        'note' => "FAILED: ".$temporary_message
                    ]);

                    $this->dispatch(
                        'alert',
                        type:'error',
                        title:'Error Occurred',
                        text:'Something went wrong. Use a valid DECODER NUMBER. Please try again!',
                        button: 'Got it!'

                    );
                        }
                    
                }else{
                    //Transaction Failed.
                    //Refund the customer
                    //Update the transaction
                    $code = $responsePurchase['code'];
                    $content = $responsePurchase['content'];
                    $response_description = $responsePurchase['response_description'];

                    DB::table('users')
                    ->where('id', auth()->id())
                    ->update([
                        'balance' => $old_balance,
                    ]);

                    DB::table('transactions')
                    ->where('reference_number', $requestId)->where('user_id',auth()->id())
                    ->update([
                        'status' => "failed",
                        'api_response' => "FAILED: ".$response_description,
                        'new_balance' => "$ngn".number_format($old_balance,2),
                        'charges' => $ngn."00.00",
                        'note' => "FAILED: ".$temporary_message
                    ]);

                    $this->dispatch(
                        'alert',
                        type:'error',
                        title:'Error Occurred',
                        text:'Something went wrong. Please try again!',
                        button: 'Got it!'

                    );


                }

                
                



            }else{
                $this->dispatch('alert',
                title: 'Error Occurred!',
                text: 'Something went wrong we are working on it quickly. Thanks!',
                type:'error',
                button: 'Got it!'
            );
            }


    }




}
