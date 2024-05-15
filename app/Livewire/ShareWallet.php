<?php

namespace App\Livewire;

use App\Models\SiteSettings;
use App\Models\User;
use Livewire\Component;
use Filament\Forms\Form;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;

class ShareWallet extends Component implements HasForms
{


    use InteractsWithForms;

    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill();
    }

    public $ngn = "₦";


    public function form(Form $form): Form
    {
        $user_balance = number_format(User::find(auth()->id())->balance,2);


        return $form
            ->schema([
                Section::make("👉 Your balance is: $this->ngn".$user_balance)
                ->description("👉 You will be charged $this->ngn".SiteSettings::first()->wallet_to_charges." for this operation")
                ->schema([
                    TextInput::make('email')
                        ->required()
                        ->columnSpan(1)
                        ->prefix('@')
                        ->placeholder('Beneficiary\'s email')
                        ,
                   TextInput::make('amount')
                       ->required()
                       ->placeholder('Amount to share')
                       ->numeric()
                       ->prefix('NGN')        
                   ,
                   TextInput::make('transaction_pin')
                       ->required()
                       ->prefix('PIN')
                       ->password()
                       ->revealable()
                       ->placeholder('Transaction Pin')        
                   ,
                    RichEditor::make('note')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'link',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ])
                    ->columnSpanFull()
                ])->columns(3)

            ])
            ->statePath('data');
    }



    public function shareWallet(): void
   {

       $user_email = $this->form->getState()['email'];
       $amount = $this->form->getState()['amount'];
       $note = $this->form->getState()['note'];
       $transaction_pin = $this->form->getState()['transaction_pin'];
       $new_balance = "";

       $transfer_charges = SiteSettings::first()->wallet_to_charges;

       
       
       $ref_number = date('YmdHis') . uniqid();
       $ref_number = "Transfer_".$ref_number;

       
   
       //Check if the recipient is actually a user!

       $recipient = DB::table('users')->where('email', $user_email)->first();

       if($recipient){

       // Retrieve the recipient record
       $recipient = DB::table('users')->where('email', $user_email)->first();
       $sender = DB::table('users')->where('id', auth()->id())->first();
       

            //Check if the $amount is numeric

            if(!is_numeric($amount)){
                $this->dispatch(
                    'alert',
                    type: 'error',
                    title: 'Invalid Operation',
                    text: "Enter correct digit as the amount!",
                    button: 'Got it!'
                );
                return;
            }


            //Check if the Transaction Pin is Correct

            if($transaction_pin != $sender->transaction_pin){
                $this->dispatch(
                    'alert',
                    type: 'error',
                    title: 'Incorrect Pin',
                    text: "Enter your correct 4-digit pin!",
                    button: 'Got it!'
                );

                return;
            }


            //Check if the recipient is not the sender...

            if($recipient->id == $sender->id){
                $this->dispatch(
                    'alert',
                    type: 'error',
                    title: 'Invalid Operation',
                    text: "You can't share fund with yourself",
                    button: 'Got it!'
                );
            
            $this->form->fill();
                return;
            }
        

       //Get previous balance
       //...Sender
       $sender_old_balance = $sender->balance;

       //...Recipient
       $recipient_old_balance = $recipient->balance;
       
   


       //Check if the sender has enough balance!
        $amount_to_bill = $amount+$transfer_charges;
          
       if ($sender_old_balance > $amount_to_bill) {


            $sender_new_balance = $sender_old_balance - $amount_to_bill;
            $recipient_new_balance = $recipient_old_balance + $amount;




           // Update the sender's balance
           DB::table('users')
               ->where('id', auth()->id())
               ->update(['balance' => $sender_new_balance]);

           // Update the recipient's balance
           DB::table('users')
               ->where('email', $user_email)
               ->update(['balance' => $recipient_new_balance]);
           
               $this->form->fill();
            //    Notification::make()
            //    ->title("You've successfully shared ".$user_email." with NGN".$amount)
            //    ->success()
            //    ->seconds(15)
            //    ->send();
            //    number_format($amount_remain,2);
               $this->dispatch(
                'alert',
                type: 'success',
                title: 'Successful!',
                text: "You've successfully shared $this->ngn".number_format($amount,2)." with ".$user_email." Your balance is $this->ngn".number_format($sender_new_balance,2),
                button: 'Got it!'
            );




            $recipient = User::where('email',$user_email)->first(); //auth()->user();    
            Notification::make()
            ->title("You've received  $this->ngn".number_format($amount,2)." from ".auth()->user()->name)
            ->icon('heroicon-m-wallet')
            ->iconColor('success')
            ->sendToDatabase($recipient);



           //Insert record into the transaction tb
           //Send DB Notification


                $current_user_balance = auth()->user()->balance;
                $current_user_balance = (double)$current_user_balance;
                if($current_user_balance <= 100){
                    Notification::make()
                    ->title("Your wallet is low. Kindly top up your  ")
                    ->icon('heroicon-m-wallet')
                    ->iconColor('success')
                    ->sendToDatabase(auth()->user());
                }







           //..Sender
           Transaction::create([
               'user_id' => $sender->id,
               'type' => 'Transfer',
               'note' => $note,
               'operator_id' => auth()->id(),
               'status' => 'successful',
               'amount' => "$this->ngn".number_format($amount,2),
               'old_balance' => "$this->ngn".number_format($sender_old_balance,2),
               'new_balance' => "$this->ngn".number_format($sender_new_balance,2),
               'reference_number' => $ref_number
               
           ]);
          //$additional_note = " || You recieved $this->ngn."".number_format($amount,2) from $sender->email";
           $additional_note = " || You received " . $this->ngn . "" . number_format($amount, 2) . " from " . $sender->email;

           //..Recipient
           Transaction::create([
               'user_id' => $recipient->id,
               'type' => 'Transfer',
               'note' => $note.$additional_note,
               'operator_id' => auth()->id(),
               'status' => 'successful',
               'amount' => "$this->ngn".number_format($amount,2),
               'old_balance' => "$this->ngn".number_format($recipient_old_balance,2),
               'new_balance' => "$this->ngn".number_format($recipient_new_balance,2),
               'reference_number' => $ref_number
               
           ]);
               
           //..............................
           //....Send DB Notification + Sweet Alert
           //..............................
           
               

       } else {
           // Handle the case where user is not found
        
           //..............................
           //....Send DB Notification 
           //..............................


            $amount_remain = (double)$amount_to_bill - (double)$sender_old_balance;
            $amount_remain = number_format($amount_remain,2);



           $this->dispatch(
            'alert',
            type: 'warning',
            title: 'Insufficient Fund!',
            text: "You need $this->ngn$amount_remain more to perform this operation. Kindly top up your wallet and try again.",
            button: 'Got it!'
        );

        $this->form->fill();

       }

   }else{
       

    $this->dispatch(
        'alert',
        type: 'error',
        title: 'Invalid Operation',
        text: 'You entered an invalid Email Address',
        button: 'Got it!'
    );

//$this->form->fill();
        return;
   }
   }



    public function render()
    {
        return view('livewire.share-wallet',[
            'user_balance' => User::find(auth()->id())->first()->balance
        ]);
    }
}
