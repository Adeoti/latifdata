<?php

namespace App\Filament\Customer\Pages;

use Carbon\Carbon;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\Transaction;
use App\Models\SiteSettings;
use App\Models\MobileAirtime;
use function Filament\authorize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;

use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class CashbackWithdrawalPage extends Page implements HasForms
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static string $view = 'filament.customer.pages.cashback-withdrawal-page';
    protected static ?string $navigationLabel = "Withdraw Fund";
    protected static ?string $title = "Withdraw Cashback";
    protected  ?string $heading = "Withdraw Cashback";
    protected static ?int $navigationSort = 29;



    public static function getNavigationBadge(): ?string
    {
        return 'new';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }


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
                Section::make('Withdrawal Note:')
                    ->description("You can only withdraw your cashback if you have at least $ngn".number_format(SiteSettings::first()->cashbak_cap_amount,2))
                    ->schema([




                        TextInput::make('amount')
                            ->required()
                            ->tel()
                            ->prefixIcon('heroicon-s-phone-arrow-up-right'),
                        
                        TextInput::make('transaction_pin')
                            ->password()
                            ->numeric()
                            ->revealable()
                            ->required()
                            

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
    public function withdrawlCashback(): void{
        
        date_default_timezone_set('Africa/Lagos');
        //Get Form data
        $ngn = "₦";
        $withdraw_amount = $this->form->getState()['amount'];
        $transaction_pin = $this->form->getState()['transaction_pin'];
        $minimum_cashout = SiteSettings::first()->cashbak_cap_amount;


        //Check if user is not banned
        if(auth()->user()->user_status != true){
            return;
        }

      

        $currentDateTime = Carbon::now();

        $formattedDateTime = $currentDateTime->format('YmdHi');
        $randomString = $this->generateRandomString(10);
        $requestId = $formattedDateTime . $randomString;

        if(strlen($requestId) < 12) {
            $requestId .= $this->generateRandomString(12 - strlen($requestId));
        }




        $requestId .= "_WITHDRAWAL";

      
        
      
       

                //Check Transaction Pin Before moving on!

                $user_pin = auth()->user()->transaction_pin;
                $user_cashback_balance = auth()->user()->cashback_balance;

                if($user_pin == $transaction_pin){

                //Check if User's balance can withstand the transaction

                    if((double)$user_cashback_balance >= (double)$minimum_cashout){

                    
                        //Check if the specified amount is not greater the user's balance

                        if((double)$withdraw_amount <= (double)$user_cashback_balance){


                            //Update user's balance from the cashback
                            //Record the transaction

                            $old_balance = auth()->user()->balance;
                            $new_balance = (double)$old_balance+(double)$withdraw_amount;
                            $new_cashback = (double)$user_cashback_balance-(double)$withdraw_amount;
                            $transactionStatus = "successful";
                            $success_msg = "You have successfully withdrawn $ngn".number_format($withdraw_amount,2)." from your cashback balance.";

                            DB::table('users')
                            ->where('id', auth()->id())
                            ->update([
                                'balance' => $new_balance,
                                'cashback_balance' => $new_cashback
                            ]);

                            Transaction::create([
                                'type' => 'cashback',
                                'user_id' => auth()->id(),
                                'status' => $transactionStatus,
                                'note' => $success_msg,
                                'amount' => "$ngn".number_format($withdraw_amount,2),
                                'old_balance' => "$ngn".number_format($old_balance,2),
                                'new_balance' => "$ngn".number_format($new_balance,2),
                                'cashback' => "$ngn".number_format($new_cashback,2),
                                'reference_number' => $requestId,
                                'plan_name' => 'Cashback',
                                'network' => 'SweetBill',
                            ]);


                           // $recipient = User::where('email',$user_email)->first(); //auth()->user();    
                            Notification::make()
                            ->title($success_msg)
                            ->icon('heroicon-o-banknotes')
                            ->iconColor('success')
                            ->sendToDatabase(auth()->user());


                             //Send Email to the recipient

                            $message = $success_msg." Your new main balance is "."$ngn".number_format($new_balance,2)." and your cashback balance is "."$ngn".number_format($new_cashback,2) ;
                            $subject = "Cashback Withdrawal";
                            $this->sendEmail(auth()->user()->email,$subject,$message,auth()->user()->name);
                            
                            $this->dispatch(
                                'alert',
                                title:'Successful',
                                text:$success_msg,
                                type:'success',
                                button:'Great!'
            
                            );

                        }else{

                            $this->dispatch(
                                'alert',
                                title:'Insufficient Cashback Balance!',
                                text:"You can only withdraw what you have in your cashback balance and you have ".$ngn.number_format($user_cashback_balance,2)." at this moment. \n 👍 Perform more transactions to earn more cashback.",
                                type:'warning',
                                button:'Got it!'
            
                            );


                            return;
                        }








            }else{

               

                $this->dispatch(
                    'alert',
                    title:'Insufficient Cashback Balance!',
                    text:"You can only withdraw your cashback to your wallet if your cashback balance is at least ".$ngn.number_format($minimum_cashout,2).".",
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