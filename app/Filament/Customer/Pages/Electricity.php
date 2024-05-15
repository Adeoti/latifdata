<?php

namespace App\Filament\Customer\Pages;

use App\Models\ElectricityIntegration;
use Carbon\Carbon;
use Filament\Forms\Set;

use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Actions\Action;

class Electricity extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static string $view = 'filament.customer.pages.electricity';


    protected static ?int $navigationSort = 9;

    
    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill();
    }

    public $ngn = "₦";

    public function form(Form $form): Form
    {



        $electricityCompanies = [
            "ikeja-electric" => "Ikeja Electricity",
            "eko-electric" => "Eko Electricity",
            "kano-electric" => "Kano Electricity",
            "portharcourt-electric" => "Port Harcourt Electricity",
            "jos-electric" => "Jos Electricity",
            "ibadan-electric" => "Ibadan Electricity",
            "kaduna-electric" => "Kaduna Electricity",
            "abuja-electric" => "Abuja Electricity",
           // "enugu-electric" => "Enugu Electricity",
            "benin-electric" => "Benin Electricity",
            "aba-electric" => "ABA Electricity",
            "yola-electric" => "YOLA Electricity",
        ];
        

        
        $user_package = auth()->user()->package;


                                    $electricity_charges = 0;

                                    //Determine the charges....
                                    
                                    switch($user_package){

                                        case 'primary':
                                            $electricity_charges = ElectricityIntegration::first()->primary_charges;
                                        break;

                                        case 'agent':
                                            $electricity_charges = ElectricityIntegration::first()->agent_charges;
                                        break;

                                        case 'special':
                                            $electricity_charges = ElectricityIntegration::first()->special_charges;
                                        break;

                                        case 'api':
                                            $electricity_charges = ElectricityIntegration::first()->api_charges;
                                        break;


                                    }

        $electricity_charges = number_format($electricity_charges,2);
        $user_balance = number_format(auth()->user()->balance,2);
        $ngn = "₦";

        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        Select::make('service_id')
                            ->options($electricityCompanies)
                            ->required()
                            ->searchable()
                            ->label('Disco name')
                            ,

                        Select::make('meter_type')
                                ->options([
                                    'prepaid' => 'Prepaid',
                                    'postpaid' => 'Postpaid',
                                ])
                                ->preload()
                                ->searchable()
                                ->required(),

                               

                    ])->columns(2),

                    Section::make('Order')
                                ->schema([
                                    TextInput::make('meter_number')
                                        ->required()
                                        ->suffixAction(
                                         Action::make('VerifyDecoder')
                                        ->icon('heroicon-m-check-badge')
                                        //->requiresConfirmation()
                                        ->tooltip('Verify Meter')
                                        ->action(function (Set $set, $state, Callable $get) {
                                            
                                            //Send a verify request to the VTPASS Endpoint...
                                            $response = Http::withHeaders([
                                                'api-key' => 'f40824cdb526d8d07bd1a4c7f54e2e9d',
                                                'secret-key' => 'SK_6320c831de33e325dac37e25f43c027a6dc09877a27',
                                                'Content-Type' => 'application/json'
                                            ])->post('https://sandbox.vtpass.com/api/merchant-verify', [
                                                'billersCode' => $state,
                                                'serviceID' => $get('service_id'),
                                                'type' => $get('meter_type'),
                                            ]);
                                            
                                            // Check if the request was successful
                                            if ($response->successful()) {
                                                $responseData = $response->json();
                                                // Handle response data
                                                $slicedResponce = json_decode($response->body(), true);

                                                
                                                

                                                if (array_key_exists('Customer_Name', $slicedResponce['content'])){
                                                
                                                    $customerName = $slicedResponce['content']['Customer_Name'];
                                                    $customerAddress = $slicedResponce['content']['Address'];
                                                    
                                                    $this->dispatch(
                                                    'alert',
                                                    title: 'Confirmed',
                                                    text: "<b>Customer Name: </b> ".$customerName." <br> <b>Address</b> ".$customerAddress,
                                                    button: 'Got it!',
                                                    type: 'success'
                                                );


                                                }else{
                                                    $this->dispatch(
                                                        'alert',
                                                        title: 'Invalid Meter Number',
                                                        text: "Kindly provide a valid Meter Number and try again!",
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

                        TextInput::make('amount')
                            ->required()
                            ->label('Amount')
                            ->prefix("$ngn"),

                        TextInput::make('charges')
                            ->default($electricity_charges)
                            ->prefix("$ngn")
                            ->readOnly(),

                    ])->columns(2),

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



public function purchase(): void
{
    $ngn = "₦";
    date_default_timezone_set('Africa/Lagos');
    $phone_number = $this->form->getState()['phone_number'];
    $service_id = $this->form->getState()['service_id'];
    $meter_type = $this->form->getState()['meter_type'];
    $transaction_pin = $this->form->getState()['transaction_pin'];

    $meter_number = $this->form->getState()['meter_number'];

    $product_amount = $this->form->getState()['amount'];


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
         if(ElectricityIntegration::first()->active_status != true){

            $this->dispatch(
                'alert',
                title:'Invalid Operation',
                text: 'This service is currently not available',
                type: 'error',
                button: 'Got it!'
            );

            return;
        }


        $user_package = auth()->user()->package;


                                    $electricity_charges = 0;

                                    //Determine the charges....
                                    
                                    switch($user_package){

                                        case 'primary':
                                            $electricity_charges = ElectricityIntegration::first()->primary_charges;
                                        break;

                                        case 'agent':
                                            $electricity_charges = ElectricityIntegration::first()->agent_charges;
                                        break;

                                        case 'special':
                                            $electricity_charges = ElectricityIntegration::first()->special_charges;
                                        break;

                                        case 'api':
                                            $electricity_charges = ElectricityIntegration::first()->api_charges;
                                        break;


                                    }
                

                                    
                //Check Transaction Pin Before moving on!

                $user_pin = auth()->user()->transaction_pin;
                $user_balance = auth()->user()->balance;
                $amount_to_pay = (double)$product_amount + (double)$electricity_charges;

                if($user_pin == $transaction_pin){

                    //Check if User's balance can withstand the transaction

                    if($user_balance >= $amount_to_pay){

                        //Proceed with the operation...
                        $product_vendor = ElectricityIntegration::first()->vendor_name;

                        switch($product_vendor){
                            case 'vtpass':
                                $this->buyElectricityFromVtPass($requestId,$amount_to_pay,$phone_number,$meter_number,$service_id,$meter_type,$product_amount,$electricity_charges,$product_vendor);
                            break;
                            
                        }


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




    public function getMyVtPassBalance(){
        $balance_mi = 0;

        $response = Http::withHeaders([
            'api-key' => 'f40824cdb526d8d07bd1a4c7f54e2e9d',
            'public-key' => 'PK_635c62becefdcf3c266258fd918c910c6680349d04d',
        ])->get('https://sandbox.vtpass.com/api/balance');
        
            dd($response);

        // Check if the request was successful
        if ($response->successful()) {
            // Request was successful, handle the response
            $responseData = $response->json();
            
            dd($responseData);

        } else {
            
            //dd();

        }


       // return $balance_mi;
    }

    public function buyElectricityFromVtPass($requestId,$amount_to_pay,$phone_number,$meter_number,$service_id,$meter_type,$product_amount,$electricity_charges,$product_vendor){

        $ngn = "₦";

        $requestId .= "_ELECTRICITY";

       // $this->getMyVtPassBalance();

            //Check my balance before moving on...
           // $myVtPassBalance = $this->getMyVtPassBalance();
            $myVtPassBalance = 2000000;

            if($myVtPassBalance > $amount_to_pay){

                $old_balance = auth()->user()->balance;
                $new_balance = (double)$old_balance - (double)$amount_to_pay;
                $transactionStatus = "pending";
                $temporary_message = "Electricity subscription of ". ucfirst($service_id) ." ".strtoupper($meter_type)." to $meter_number";
                $endpoint = "https://sandbox.vtpass.com/api/pay";
                  
               
                $customerName = $customerName = "";
                $requestAmount = 0;




                //Check the billerCode and get the customer's name.....

                //Send a verify request to the VTPASS Endpoint...
                $response = Http::withHeaders([
                    'api-key' => 'f40824cdb526d8d07bd1a4c7f54e2e9d',
                    'secret-key' => 'SK_6320c831de33e325dac37e25f43c027a6dc09877a27',
                    'Content-Type' => 'application/json'
                ])->post('https://sandbox.vtpass.com/api/merchant-verify', [
                    'billersCode' => $meter_number,
                    'serviceID' => $service_id,
                    'type' => $meter_type,
                ]);
                
                // Check if the request was successful
                if ($response->successful()) {
                    $responseData = $response->json();
                    // Handle response data
                    $slicedResponce = json_decode($response->body(), true);


                    if (array_key_exists('Customer_Name', $slicedResponce['content'])){
                    
                        $customerName = $slicedResponce['content']['Customer_Name'];
                        $customerAddress = $slicedResponce['content']['Address'];



                    }else{  

                        $this->dispatch(
                            'alert',
                            title: 'Invalid Meter Number',
                            text: "Kindly provide a valid Meter Number and Try again!",
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
                        'type' => 'electricity',
                        'user_id' => auth()->id(),
                        'api_response' => $temporary_message,
                        'status' => $transactionStatus,
                        'note' => $temporary_message,
                        'phone_number' => $phone_number,
                        'amount' => "$ngn".number_format($product_amount,2),
                        'old_balance' => "$ngn".number_format($old_balance,2),
                        'new_balance' => "$ngn".number_format($new_balance,2),
                        'reference_number' => $requestId,
                        'meter_type' => $meter_type,
                        'meter_number' => $meter_number,
                        'customer_name' => $customerName,
                        'customer_address' => $customerAddress,
                        'disco_name' => ucfirst($service_id)."ity",
                        'charges' => "$ngn".number_format($electricity_charges,2)
                    ]);
    
    
                    $payload = [
                        'phone' => $phone_number,
                        'serviceID' => $service_id,
                        'billersCode' => $meter_number,
                        'variation_code' => $meter_type,
                        'request_id' => $requestId,
                        'amount' => $product_amount,
                    ];
                    
                   
                    

                } else {
                    // Handle unsuccessful response
                    //dd("Failed Here".$response." || ".$state." || ".$get('cableType'));

                    $this->dispatch(
                        'alert',
                        title: 'BILLER ERR: Something went wrong',
                        text: 'Please try again later or reach out to our reps for help',
                        button: 'Got it!',
                        type: 'error'
                    );

                    return;

                }



                $responseCable = Http::withHeaders([
                    'api-key' => 'f40824cdb526d8d07bd1a4c7f54e2e9d',
                    'secret-key' => 'SK_6320c831de33e325dac37e25f43c027a6dc09877a27',
                    'Content-Type' => 'application/json'
                ])->post(trim($endpoint), $payload);


                $responsePurchase = json_decode($responseCable->body(), true);

                    //dd($responsePurchase);
                if(isset($responsePurchase['content']['transactions']['status'])) {
                    


                    $content = $responsePurchase['content'];
                    $response_description = $responsePurchase['response_description'];
                    $requestId = $responsePurchase['requestId'];
                    $amount = $responsePurchase['amount'];
                    $purchased_code = $responsePurchase['purchased_code'];
                   
                   

                    // Extract variables from the 'content' array
                    $transactions = $content['transactions'];

                    $status = $transactions['status'];
                   
                    $product_name = $transactions['product_name'];

                    
                    
                    //". ucfirst($service_id) ." ".strtoupper($variation_code)." package to $decoder_number"

                    $successMessage = "You've successfully purchased ".strtoupper($product_name)." (".ucfirst($meter_type).") for <b>".$meter_number."</b> on ".date("l jS \of F Y h:i:s A").".";

                        if($status === 'delivered'){

                            DB::table('transactions')
                            ->where('reference_number', $requestId)->where('user_id',auth()->id())
                            ->update([
                                'status' => "successful",
                                'api_response' => $successMessage,
                                'token_pin' => $purchased_code,
                                'disco_name' => $product_name,
                                'note' => $successMessage,
                            ]);

                        
                        $this->dispatch(
                                'alert',
                                type:'success',
                                title:'Successful',
                                text:$successMessage,
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
                        text:'Something went wrong. Use a valid METER NUMBER. Please try again!',
                        button: 'Got it!'

                    );
                        }
                    
                }else{
                    //Transaction Failed.
                    //Refund the customer
                    //Update the transaction
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
                        'api_response' => "FAILED: ".$temporary_message,
                        'new_balance' => "$ngn".number_format($old_balance,2),
                        'charges' => $ngn."00.00",
                        'note' => "FAILED: ".$temporary_message
                    ]);

                    if($response_description === "BELOW MINIMUM AMOUNT ALLOWED"){
                        $this->dispatch(
                            'alert',
                            type:'warning',
                            title:'Too Low Entry',
                            text:'Enter a minimum of '.$ngn.number_format(500,2)." and try again.",
                            button: 'Got it!'
    
                        );
                    }else{
                        $this->dispatch(
                        'alert',
                        type:'error',
                        title:'Error Occurred',
                        text:'Something went wrong. Please try again!',
                        button: 'Got it!'

                    );
                    }

                    


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

