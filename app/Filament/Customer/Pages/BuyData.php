<?php

namespace App\Filament\Customer\Pages;

use Carbon\Carbon;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\MobileData;
use App\Models\Beneficiary;
use App\Models\Transaction;
use App\Jobs\ProcessDataPurchase;
use App\Models\TransactionPuller;
use PhpParser\Node\Expr\CallLike;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;

class BuyData extends Page implements HasForms
{

    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string $view = 'filament.customer.pages.buy-data';
    protected static ?string $navigationLabel = "Buy Data";
    protected static ?int $navigationSort = 2;
    public $polling = false;
    public $testMesg = "Earlier...";



    
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

                ToggleButtons::make('newuser')
                ->label('')
                ->options([
                    'new customer'=>'New Customer',
                    'beneficiary'=>'Beneficiary'
                ])
                ->icons([
                    'new customer'=>'heroicon-o-user-plus',
                    'beneficiary'=>'heroicon-o-users',
                ])
                ->default('new customer')
                ->inline(true)
                ->live()
                ->afterStateUpdated(function(Callable $get, $set){
                    //$customer = 
                })
                ,
                Select::make('beneficiaries')
                    ->options(Beneficiary::where('user_id',auth()->id())->pluck('name','id'))
                    ->live()
                    ->required()
                    ->visible(function(Callable $get){
                        
                        $userTYpe = $get('newuser');
                        if($userTYpe == 'beneficiary'){
                            return true;
                        }

                    })
                    ->searchable()
                    ->label('')
                    ->placeholder('choose a beneficiary')
                    ->afterStateUpdated(function(Callable $get, $set, $state){
                        $customer = Beneficiary::find($state);

                        $customer_phone = $customer->number;

                        $customer_network = $customer->network;

                        $set('network',$customer_network);
                        $set('plan_type',null);
                        $set('phone_number',$customer_phone);
                       


                    })
                    
                    ,
                Section::make('')
                    ->schema([

                        Select::make('network')
                            ->required()
                            ->options([
                                'mtn'=>'MTN',
                                'airtel'=>'Airtel',
                                'glo'=>'GLO',
                                '9mobile'=>'9Mobile'

                            ])
                            
                            ->afterStateUpdated(function(callable $set){
                                $set('amount', '00.00');
                                $set('cashback', '00.00');
                                $set('plan_type', null);
                            })
                            ->reactive()
                            ,


                        Select::make('plan_type')
                            ->reactive()
                            ->required()
                            ->preload()
                            ->afterStateUpdated(
                                function(callable $set){
                                    $set('amount', '00.00');
                                    $set('cashback', '00.00');
                                    $set('plan_size', null);
                            })
                                
                            ->options(function(callable $get){
                                $plan_type = MobileData::where('network',$get('network'))->where('active_status',true)->pluck('plan_type','plan_type');
                                return $plan_type;
                            }),


                        Select::make('plan_size')
                            ->required()
                            ->loadingMessage('Loading Packages...')
                            ->label('Package')
                            ->preload()
                            //->afterStateUpdated(fn (callable $set) => $set('amount','4000'))
                            ->options(
                                   
                                    function(callable $get){
                                      //  $package = MobileData::where('network',$get('network'));
                                        $package = MobileData::where('network',$get('network'))->where('plan_type',$get('plan_type'))->where('active_status',true)->get();
                                        
                                        $options = [];
                                        
                                        if(! $package){
                                            return null;
                                        }else{




                                            $user_package = auth()->user()->package;
                                            $cashback_price = $data_price = 0;

                                          

                                            foreach($package as $item){

                                                switch($user_package){

                                                    case 'primary':
                                                        $data_price = MobileData::find($item->id)->primary_price;
                                                        $cashback_price = MobileData::find($item->id)->primary_cashback;
                                                    break;
            
                                                    case 'agent':
                                                        $data_price = MobileData::find($item->id)->agent_price;
                                                        $cashback_price = MobileData::find($item->id)->agent_cashback;
                                                    break;
            
                                                    case 'special':
                                                        $data_price = MobileData::find($item->id)->special_price;
                                                        $cashback_price = MobileData::find($item->id)->special_cashback;
                                                    break;
            
                                                    case 'api':
                                                        $data_price = MobileData::find($item->id)->api_price;
                                                        $cashback_price = MobileData::find($item->id)->api_cashback;
                                                    break;
            
            
            
                                                }

                                                $options[$item->id] = $item->plan_size . " - $this->ngn".number_format($data_price,2)." - ".$item->validity;

                                            }

                                            //return $package->pluck('plan_size','id');
                                            return $options;

                                        }
                                    }
                                )
                                ->afterStateUpdated(function(callable $set, $get){
                                  

                                    $user_package = auth()->user()->package;


                                    $cashback = 0;
                                    $amount = 0;

                                    //Determine the amount and cashback....
                                    
                                    switch($user_package){

                                        case 'primary':
                                            $amount = MobileData::find($get('plan_size'))->primary_price;
                                            $cashback = MobileData::find($get('plan_size'))->primary_cashback;
                                        break;

                                        case 'agent':
                                            $amount = MobileData::find($get('plan_size'))->agent_price;
                                            $cashback = MobileData::find($get('plan_size'))->agent_cashback;
                                        break;

                                        case 'special':
                                            $amount = MobileData::find($get('plan_size'))->special_price;
                                            $cashback = MobileData::find($get('plan_size'))->special_cashback;
                                        break;

                                        case 'api':
                                            $amount = MobileData::find($get('plan_size'))->api_price;
                                            $cashback = MobileData::find($get('plan_size'))->api_cashback;
                                        break;



                                    }

                                    // $this->dispatch(
                                    //         'alert',
                                    //         type:'success',
                                    //         title:'Hello',
                                    //         text:'Your amount is '.$amount." and your cashback is ".$cashback,
                                    //         button:'Got it!'
                                    //     );


                                    $set('amount', number_format($amount,2));
                                    $set('cashback', number_format($cashback,2));

                                })->reactive()
                                ,
                        
                        TextInput::make('phone_number')
                            ->required()
                            ->tel()
                            ->prefixIcon('heroicon-s-phone-arrow-up-right'),
                        
                        TextInput::make('transaction_pin')
                            ->required()
                            ->numeric()
                            ->password()
                            ->revealable()
                            ->maxLength(4),
                        Toggle::make('validate_phone_number')
                            ->default(true)
                            ->label('Validate Phone Number')
                            ->inline(false),

                            

                    ])->columns(3),

                    Section::make('Checkout')
                            ->schema([

                                TextInput::make('amount')
                                    ->default('00.00')
                                    ->prefix("$ngn")
                                    ->readOnly(),

                                TextInput::make('cashback')
                                    ->default('00.00')
                                    ->prefix("$ngn")
                                    ->readOnly(),

                            ])->columns(2),

                    Section::make('Add Beneficiary')
                            ->schema([

                                TextInput::make('beneficiary_name')
                                    ->label('')
                                    ->placeholder('Beneficiary\'s Name')
                                    ,

                            ])
                            ->hidden(function(Callable $get){
                                    $userType = $get('newuser');

                                    if($userType == "beneficiary"){
                                        return true;
                                    }
                            })

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
     
     public function buyDataFromTwins10andCo($requestId,$data_id,$amount,$cashback,$phone_number,$bypass,$vendor){
       
       $auth_route = $pass_n_username = "";

            switch($vendor){
                case 'twins10':
                    $auth_route = "https://twins10.com/api/user";
                    $pass_n_username = 'Adeoti360:7DP75syvXML$$Ade#';
                break;
                case 'datalight':
                    $auth_route = "https://datalight.ng/api/user";
                    $pass_n_username = 'SweetBill:7DP75syvXML$$Ade#';
                break;
            }
       
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($pass_n_username),
        ])->post($auth_route);
        



        $ngn = "₦";

        

        $json = $response->body();
        
        $responseData = json_decode($json, true);

       

        $status = $responseData['status'];

            if($status != "success"){

                $this->dispatch(
                    'alert',
                    type: 'error',
                    title: 'Error Occurred',
                    text: "Authentication Error! Try again.",
                    button: 'Got it!'
                );
                return;

            }



            $accessToken = $responseData['AccessToken'];
            $username = $responseData['username'];
            $balance = (float) str_replace(',', '', $responseData['balance']);
           
           

                //Check if my balance is capable of the job!

                if($amount > $balance){
                    $this->dispatch(
                        'alert',
                        type:'warning',
                        title:'Transaction Failed',
                        text:'Something went wrong and we will fix it soon',
                        button: 'Got it!',
                    );
                    return;
                }



                //Get Data Details....

                $network = MobileData::find($data_id)->api_code;
                $data_plan = MobileData::find($data_id)->service_id;
                $endpoint = MobileData::find($data_id)->endpoint;


                $user_old_balance = auth()->user()->balance;
                $old_balance = auth()->user()->balance;
                $new_balance = $old_balance - $amount;


                $old_balance_solid = auth()->user()->balance;
                $new_balance_solid = $old_balance_solid - $amount;


                $old_cashback = auth()->user()->cashback_balance;
                $new_cashback = $old_cashback+$cashback;


                $old_balance = number_format($old_balance,2);
                $new_balance = number_format($new_balance,2);
                $transactionStatus = "pending";



                


                //
                // Debit user ahead and record Transaction....
                //
                //
                
                
                $temporary_network = MobileData::find($data_id)->network;
                $temporary_plan_size = MobileData::find($data_id)->plan_size;
                $temporary_plan_type = MobileData::find($data_id)->plan_type;
                $temporary_message = "Purchase of $temporary_network $temporary_plan_size Data to $phone_number";

                DB::table('users')
                ->where('id', auth()->id())
                ->update([
                    'balance' => $new_balance_solid,
                    'cashback_balance' => $new_cashback
                ]);

                Transaction::create([
                    'type' => 'data',
                    'user_id' => auth()->id(),
                    'api_response' => $temporary_message,
                    'status' => $transactionStatus,
                    'note' => $temporary_message,
                    'phone_number' => $phone_number,
                    'amount' => "$ngn".number_format($amount,2),
                    'old_balance' => "$ngn".$old_balance,
                    'new_balance' => "$ngn".$new_balance,
                    'cashback' => "$ngn".number_format($cashback,2),
                    'reference_number' => $requestId,
                    'plan_name' => $temporary_plan_type,
                    'network' => $temporary_network,
                ]);

                    $payload = [
                        'network' => $network,
                        'phone' => $phone_number,
                        'data_plan' => $data_plan,
                        'bypass' => $bypass,
                        'request-id' => $requestId,
                    ];

                    $purchaseResponse = Http::withHeaders([
                        'Authorization' => "Token ".$accessToken."",
                        'Content-Type' => 'application/json'
                    ])->post(trim($endpoint), $payload);

                   // $purchaseResult = $purchaseResponse->json();
                    $responsePurchase = json_decode($purchaseResponse->body(), true);


                    $purchaseStatus = $responsePurchase['status'];
                    $message = $responsePurchase['message'];


                        //dd($purchaseResult);

                        if(isset($purchaseStatus)){
                            
                    if($purchaseStatus == 'success'){

                            //Update Transaction Record and don't update balance and cashback
                            //Flash success message

                            DB::table('transactions')
                            ->where('reference_number', $requestId)->where('user_id',auth()->id())
                            ->update([
                                'status' => "successful",
                                'api_response' => $message,
                                'note' => "You've successfully sold ".strtoupper($temporary_network)." $temporary_plan_type of ".$temporary_plan_size." Data to ".$phone_number." on ".date("l jS \of F Y h:i:s A")."."
                            ]);

                        
                        $this->dispatch(
                                'alert',
                                type:'success',
                                title:'Successful',
                                text:"You've successfully sold ".strtoupper($temporary_network)." $temporary_plan_type of ".$temporary_plan_size." Data to ".$phone_number." on ".date("l jS \of F Y h:i:s A").".",
                                button:'Great!'
                            );




                    }else{

                        //Record Transaction
                        //Update Balance and Cashback to old data
                        //Flash Message

                        DB::table('users')
                        ->where('id', auth()->id())
                        ->update([
                            'balance' => $old_balance_solid,
                            'cashback_balance' => $old_cashback
                        ]);

                        DB::table('transactions')
                            ->where('reference_number', $requestId)->where('user_id',auth()->id())
                            ->update([
                                'status' => "failed",
                                'api_response' => $message,
                                'new_balance' => "$ngn".$old_balance,
                                'cashback' => $ngn."00.00",
                                'note' => "Failed to sell ".strtoupper($temporary_network)." $temporary_plan_type of ".$temporary_plan_size." Data to ".$phone_number." on the ".date("l jS \of F Y h:i:s A")."."
                            ]);

                        $this->dispatch(
                            'alert',
                            type:'error',
                            title:'Error Occurred',
                            text:'Something went wrong. Please try again!',
                            button: 'Got it!'

                        );

                    }
                        }

                    

             






     }


     public function buyDataFromVTPass(){


     }




    public function saveBeneficiary($name,$network,$number){


        //Check if this number hasn't been saved by this user before!

        $number_check = Beneficiary::where('number',$number)->where('user_id',auth()->id())->count();
        if(!($number_check>0)){
            Beneficiary::create([
            'user_id'=>auth()->id(),
            'name'=>$name,
            'number'=>$number,
            'network'=>$network,
        ]);
        }

       

    }

    public function buyData(): void{

        

        $this->polling = true;
        
        date_default_timezone_set('Africa/Lagos');
        //Get Form data

        $ngn = "₦";
        $data_id = $this->form->getState()['plan_size'];
        $phone_number = $this->form->getState()['phone_number'];
        $chosen_network = $this->form->getState()['network'];
        $transaction_pin = $this->form->getState()['transaction_pin'];
        $validate_phone_number = $this->form->getState()['validate_phone_number'];

        $beneficiary = "";
        
        
        if (array_key_exists('beneficiary_name', $this->form->getState())){

            $beneficiary = $this->form->getState()['beneficiary_name'];

        }


       

        //Check if user is not banned
        if(auth()->user()->user_status != true){
            return;
        }


        //



        //Check if data status is enabled
        if(MobileData::find($data_id)->active_status != true){

            $this->dispatch(
                'alert',
                title:'Invalid Operation',
                text: 'The requested data value is not available',
                type: 'error',
                button: 'Got it!'
            );
            return;
        }

        $currentDateTime = Carbon::now();

        $formattedDateTime = $currentDateTime->format('YmdHi');
        $randomString = $this->generateRandomString(10);
        $requestId = $formattedDateTime . $randomString;

        if(strlen($requestId) < 12) {
            $requestId .= $this->generateRandomString(12 - strlen($requestId));
        }



        $data_vendor = MobileData::find($data_id)->vendor_name;

        $requestId .= "_DATA";

        $user_package = auth()->user()->package;
        $cashback = 0;
        $amount = 0;

        //Determine the amount and cashback....
        
        switch($user_package){

            case 'primary':
                $amount = MobileData::find($data_id)->primary_price;
                $cashback = MobileData::find($data_id)->primary_cashback;
            break;

            case 'agent':
                $amount = MobileData::find($data_id)->agent_price;
                $cashback = MobileData::find($data_id)->agent_cashback;
            break;

            case 'special':
                $amount = MobileData::find($data_id)->special_price;
                $cashback = MobileData::find($data_id)->special_cashback;
            break;

            case 'api':
                $amount = MobileData::find($data_id)->api_price;
                $cashback = MobileData::find($data_id)->api_cashback;
            break;



        }

        


                //Check Transaction Pin Before moving on!

                $user_pin = auth()->user()->transaction_pin;
                $user_balance = auth()->user()->balance;

                if($user_pin == $transaction_pin){

                //Check if User's balance can withstand the transaction

                    if($user_balance >= $amount){

                   
                //Check if the supplied number tallies with the network....
                

                if($validate_phone_number){

                    // Dataset of supported phone numbers and networks
                        $supported_numbers = [
                            "0701" => "airtel",
                            "07025" => "mtn",
                            "07026" => "mtn",
                            "0703" => "mtn",
                            "0704" => "mtn",
                            "0705" => "glo",
                            "0706" => "mtn",
                            "0708" => "airtel",
                            "0802" => "airtel",
                            "0803" => "mtn",
                            "0805" => "glo",
                            "0806" => "mtn",
                            "0807" => "glo",
                            "0808" => "airtel",
                            "0809" => "9mobile",
                            "0810" => "mtn",
                            "0811" => "glo",
                            "0812" => "airtel",
                            "0813" => "mtn",
                            "0814" => "mtn",
                            "0815" => "glo",
                            "0816" => "mtn",
                            "0817" => "9mobile",
                            "0818" => "9mobile",
                            "0909" => "9mobile",
                            "0908" => "9mobile",
                            "0901" => "airtel",
                            "0902" => "airtel",
                            "0903" => "mtn",
                            "0904" => "airtel",
                            "0905" => "glo",
                            "0906" => "mtn",
                            "0907" => "airtel",
                            "0915" => "glo",
                            "0913" => "mtn",
                            "0912" => "airtel",
                            "0916" => "mtn",
                            "0911" => "airtel"
                        ];


                    $prefix = substr($phone_number, 0, 4);

                    if (array_key_exists($prefix, $supported_numbers)) {
                        $expected_network = $supported_numbers[$prefix];
                        
                        if ($chosen_network == $expected_network) {
                           
                            //Save beneficiary...
                            if(!empty($beneficiary) && $beneficiary != "old customer"){
                                $this->saveBeneficiary($beneficiary,$chosen_network,$phone_number);
                            }




                            $userId = auth()->id();
                            ProcessDataPurchase::dispatch($userId,$requestId,$data_id,$amount,$cashback,$phone_number,$validate_phone_number,$data_vendor);

                            $this->dispatch(
                                'alert', 
                                title: 'Transaction Initiated',
                                text: '<center>'.$this->loadingWheel.'</center> Your Data Transaction is in progress...',
                                button: 'Got it!',
                                type: 'info'
                    );

                            // switch($data_vendor){
                            //     case 'twins10':
                            //         $this->buyDataFromTwins10andCo($requestId,$data_id,$amount,$cashback,$phone_number,$validate_phone_number,$data_vendor);
                            //     break;
                            //     case 'datalight':
                            //         $this->buyDataFromTwins10andCo($requestId,$data_id,$amount,$cashback,$phone_number,$validate_phone_number,$data_vendor);
                            //     break;
                            // }

                        
                        } else {

                            $this->dispatch(
                                'alert',
                                title:'Invalid Phone Number',
                                text:"The entered phone number doesn't seem to be a valid ".strtoupper($chosen_network)." number.",
                                type:'warning',
                                button:'Got it!'
            
                            );
                            
                        }
                    } else {

                        $this->dispatch(
                            'alert',
                            title:'Invalid Phone Number',
                            text:"The entered phone number doesn't seem to be a valid ".strtoupper($chosen_network)." number.",
                            type:'warning',
                            button:'Got it!'
        
                        );
                    }


                }else{

                //Save beneficiary...
                if(!empty($beneficiary) && $beneficiary != "old customer"){
                    $this->saveBeneficiary($beneficiary,$chosen_network,$phone_number);
                }



                $userId = auth()->id();
                ProcessDataPurchase::dispatch($userId,$requestId,$data_id,$amount,$cashback,$phone_number,$validate_phone_number,$data_vendor);

                $this->dispatch(
                    'alert', 
                    title: 'Transaction Initiated',
                    text: '<center>'.$this->loadingWheel.'</center> Your Data Transaction is in progress...',
                    button: 'Got it!',
                    type: 'info'
        );
                // switch($data_vendor){
                //     case 'twins10':
                //         $this->buyDataFromTwins10andCo($requestId,$data_id,$amount,$cashback,$phone_number,$validate_phone_number,$data_vendor);
                //     break;
                //     case 'datalight':
                //         $this->buyDataFromTwins10andCo($requestId,$data_id,$amount,$cashback,$phone_number,$validate_phone_number,$data_vendor);
                //     break;
                // }
                    }




            }else{

                $amount_remain = $amount-$user_balance;

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









}
