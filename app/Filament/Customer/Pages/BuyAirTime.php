<?php

namespace App\Filament\Customer\Pages;

use App\Jobs\ProcessAirtimePurchase;
use App\Jobs\ResolveTransactionNotification;
use Carbon\Carbon;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\Beneficiary;
use App\Models\Transaction;
use App\Models\MobileAirtime;
use App\Models\TransactionPuller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;

use Filament\Notifications\Livewire\DatabaseNotifications;
 


class BuyAirTime extends Page implements HasForms
{



    protected static ?string $navigationIcon = 'heroicon-o-phone-arrow-up-right';

    protected static string $view = 'filament.customer.pages.buy-air-time';
    protected static ?string $navigationLabel = "Buy Airtime";
    protected static ?string $title = "Buy Airtime";
    protected  ?string $heading = "Buy Airtime";
    protected static ?int $navigationSort = 3;
    public $polling = false;
    public $testMesg = "Earlier...";


    use InteractsWithForms;

  



    
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
                        $set('phone_number',$customer_phone);
                       

                    })
                    
                    ,
                Section::make('')
                    ->schema([

                        Select::make('network')
                            ->options([
                                'mtn'=>'MTN',
                                'airtel'=>'Airtel',
                                'glo'=>'GLO',
                                '9mobile'=>'9Mobile'

                            ])
                            ->required()
                            ->afterStateUpdated(function(callable $set, $get){

                               
                                    if(!empty($get('amount'))){
                                      $set('amount', '00.00');  
                                    }
                                
                                $set('total', '00.00');
                                $set('cashback', '00.00');
                            })
                            ->live()
                            ,



                        TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->prefix($ngn)
                            ->live(debounce: 500)
                            
                                ->afterStateUpdated(function(callable $set, $get, ?string $state){
                                  


                                    $user_package = auth()->user()->package;

                                        // $this->dispatch(
                                        //     'alert',
                                        //     type:'success',
                                        //     title:'Hello',
                                        //     text:'Your package is '.$user_package,
                                        //     button:'Got it!'
                                        // );

                                    $cashback = 0;
                                    $amount = 0;

                                    //Determine the amount and cashback....
                                   
                                    
                                    switch($user_package){

                                        case 'primary':
                                            $amount = MobileAirtime::where('network',$get('network'))->where('active_status',true)->first()->primary_price;
                                            $cashback = MobileAirtime::where('network',$get('network'))->where('active_status',true)->first()->primary_cashback;
                                        break;

                                        case 'agent':
                                            $amount = MobileAirtime::where('network',$get('network'))->where('active_status',true)->first()->agent_price;
                                            $cashback = MobileAirtime::where('network',$get('network'))->where('active_status',true)->first()->agent_cashback;
                                        break;

                                        case 'special':
                                            $amount = MobileAirtime::where('network',$get('network'))->where('active_status',true)->first()->special_price;
                                            $cashback = MobileAirtime::where('network',$get('network'))->where('active_status',true)->first()->special_cashback;
                                        break;

                                        case 'api':
                                            $amount = MobileAirtime::where('network',$get('network'))->where('active_status',true)->first()->api_price;
                                            $cashback = MobileAirtime::where('network',$get('network'))->where('active_status',true)->first()->api_cashback;
                                        break;



                                    }

                                    $percentage = (double)$amount / 100;
                                    $total_amount = (double)$get('amount') * (double)$percentage;

                                    $cashback_percentage = $cashback /100;
                                    $total_cashback = $total_amount * $cashback_percentage;

                                    // $this->dispatch(
                                    //         'alert',
                                    //         type:'success',
                                    //         title:'Hello',
                                    //         text:'Your amount is '.$total_amount." and your cashback is ".$total_cashback,
                                    //         button:'Got it!'
                                    //     );


                                    $set('cashback', number_format($total_cashback,2));
                                    $set('total', number_format($total_amount,2));

                                })
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
                            ->inline(false)
                            

                    ])->columns(3),

                    Section::make('Checkout')
                            ->schema([

                                TextInput::make('total')
                                    ->default('00.00')
                                    ->label('Total')
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


    public function buyAirtime(): void{
        date_default_timezone_set('Africa/Lagos');
        //Get Form data


        

            $this->polling = true;
        

        $ngn = "₦";
        $airtime_network = $this->form->getState()['network'];
        $airtime_amount = $this->form->getState()['amount'];
        $airtime_id = MobileAirtime::where('network',$airtime_network)->where('active_status',true)->first()->id;
        $phone_number = $this->form->getState()['phone_number'];
        $transaction_pin = $this->form->getState()['transaction_pin'];
        $validate_phone_number = $this->form->getState()['validate_phone_number'];

        $beneficiary = "";
        $phone_number = str_replace(' ', '', $phone_number);
        
        
        if (array_key_exists('beneficiary_name', $this->form->getState())){

            $beneficiary = $this->form->getState()['beneficiary_name'];

        }

        //Check if user is not banned
        if(auth()->user()->user_status != true){
            return;
        }

        //Check if airtime status is enabled
        if(MobileAirtime::find($airtime_id)->active_status != true){

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



        $airtime_vendor = MobileAirtime::find($airtime_id)->vendor_name;

        $requestId .= "_AIRTIME";

        $user_package = auth()->user()->package;
        
        $cashback = 0;
        $amount = 0;

       //dd($amount);
        
        switch($user_package){

            case 'primary':
                $amount = MobileAirtime::where('network',$airtime_network)->where('active_status',true)->first()->primary_price;
                $cashback = MobileAirtime::where('network',$airtime_network)->where('active_status',true)->first()->primary_cashback;
            break;

            case 'agent':
                $amount = MobileAirtime::where('network',$airtime_network)->where('active_status',true)->first()->agent_price;
                $cashback = MobileAirtime::where('network',$airtime_network)->where('active_status',true)->first()->agent_cashback;
            break;

            case 'special':
                $amount = MobileAirtime::where('network',$airtime_network)->where('active_status',true)->first()->special_price;
                $cashback = MobileAirtime::where('network',$airtime_network)->where('active_status',true)->first()->special_cashback;
            break;

            case 'api':
                $amount = MobileAirtime::where('network',$airtime_network)->where('active_status',true)->first()->api_price;
                $cashback = MobileAirtime::where('network',$airtime_network)->where('active_status',true)->first()->api_cashback;
            break;



        }

        $percentage = (double)$amount / 100;
        $total_amount = (double)$airtime_amount * (double)$percentage;

        $cashback_percentage = $cashback /100;
        $total_cashback = $total_amount * $cashback_percentage;

        


                //Check Transaction Pin Before moving on!

                $user_pin = auth()->user()->transaction_pin;
                $user_balance = auth()->user()->balance;

                if($user_pin == $transaction_pin){

                //Check if User's balance can withstand the transaction

                    if($user_balance >= $total_amount){

                    

                    //  Record the transaction (as pending)
                    //  Deduct Price
                    //  Check the API endpoint based on the submission
                    //  Process balance
                    //  Process cashback
                    //  Flash Report





                    //Check if the airtime_amount is within the minimum and maximum range of the chosen route

                        $minimum_amount = MobileAirtime::find($airtime_id)->minimum_amount;
                        $maximum_amount = MobileAirtime::find($airtime_id)->maximum_amount;

                       // dd("mini = ".$minimum_amount." | maxi = ".$maximum_amount." Total Amount = ".$total_amount." Cashback = ".$total_cashback);

                        if($airtime_amount < $minimum_amount){
                            $this->dispatch(
                                'alert',
                                title:'Too low entry!',
                                text:"You cannot purchase airtime worth less than $ngn".number_format($minimum_amount,2).".",
                                type:'warning',
                                button:'Got it!'
            
                            );
                            return;
                        }

                        if($airtime_amount > $maximum_amount){
                            $this->dispatch(
                                'alert',
                                title:'Too high entry!',
                                text:"You cannot purchase airtime exceeding $ngn".number_format($maximum_amount,2)." at a time.",
                                type:'warning',
                                button:'Got it!'
            
                            );
                            return;
                        }
                
                

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
                                
                                if ($airtime_network == $expected_network) {
                                   
                                    
                                    //Save beneficiary...
                                    if(!empty($beneficiary) && $beneficiary != "old customer"){
                                        $this->saveBeneficiary($beneficiary,$airtime_network,$phone_number);
                                    }





                                    //Dispatch the JOB.....
                                    $userId = auth()->id();

                                    ProcessAirtimePurchase::dispatch($userId,$requestId,$airtime_id,$airtime_amount,$total_amount,$total_cashback,$phone_number,$validate_phone_number);



                                 


                                $this->dispatch(
                                        'alert', 
                                        title: 'Transaction Initiated',
                                        text: '<center>'.$this->loadingWheel.'</center> Your Airtime Transaction is in progress...',
                                        button: 'Got it!',
                                        type: 'info'
                            );
                            
                            $this->form->fill();
                                
                                } else {
        
                                    $this->dispatch(
                                        'alert',
                                        title:'Invalid Phone Number',
                                        text:"The entered phone number doesn't seem to be a valid ".strtoupper($airtime_network)." number.",
                                        type:'warning',
                                        button:'Got it!'
                    
                                    );
                                    
                                }
                            } else {
        
                                $this->dispatch(
                                    'alert',
                                    title:'Invalid Phone Number',
                                    text:"The entered phone number doesn't seem to be a valid ".strtoupper($airtime_network)." number.",
                                    type:'warning',
                                    button:'Got it!'
                
                                );
                            }
        
        
                        }else{


                            
                //Save beneficiary...
                if(!empty($beneficiary) && $beneficiary != "old customer"){
                    $this->saveBeneficiary($beneficiary,$airtime_network,$phone_number);
                }


                 //Dispatch the JOB.....
                 $userId = auth()->id();

                 ProcessAirtimePurchase::dispatch($userId,$requestId,$airtime_id,$airtime_amount,$total_amount,$total_cashback,$phone_number,$validate_phone_number);


                 $this->dispatch(
                    'alert', 
                    title: 'Transaction Initiated',
                    text: '<center>'.$this->loadingWheel.'</center> Your Airtime Transaction is in progress...',
                    button: 'Got it!',
                    type: 'info'
        );
        $this->form->fill();



                    }












            }else{

                $amount_remain = $total_amount-$user_balance;

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