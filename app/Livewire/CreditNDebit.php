<?php

namespace App\Livewire;

use Exception;
use App\Models\User;
use Livewire\Component;
use Filament\Forms\Form;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use App\Mail\SweetBillNotificationEmail;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;

class CreditNDebit extends Component implements HasForms
{

    use InteractsWithForms;

    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill();
    }

    public $ngn = "₦";

    /**
     * Validate Email
     * Validate Amount
     * 
     * Fetch Old Balance
     * Add Old Balance to New Amount and update user with the current amount
     * Add record to Transactions [Old Balance, ...]
     * Notify the receiver
     * Send Flash Message..
     * 
     */
    
     public function form(Form $form): Form
     {


       



         return $form
             ->schema([
                 Section::make('')->schema([
                     Select::make('email')
                         ->searchable()
                         ->preload()
                         ->required()
                         ->columnSpan(1)
                         ->options(User::all()->pluck('email','email'))
                         ->prefix('@')
                         ->placeholder('Choose or Enter user\'s email')
                         ,
                     Select::make('type')
                         ->options([
                             'credit' => 'Credit Wallet',
                             'debit' => 'Debit Wallet'
                         ])
                         ->placeholder('Choose Credit or Debit')
                         ->required()
                         ->columnSpan(1),
                    TextInput::make('amount')
                        ->required()
                        ->numeric()
                        ->prefix('NGN')        
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
 
 
     public function sendEmail($toEmail,$subject,$email_message,$emailRecipient){    

        try {
            $response = Mail::to($toEmail)->send(new SweetBillNotificationEmail($subject,$email_message,$emailRecipient));
            
        } catch (Exception $e) {
           
            Log::error('Unable to send email '. $e->getMessage() );
        }
    
    }
    
     public function creditNDebit(): void
    {

        $user_email = $this->form->getState()['email'];
        $type = $this->form->getState()['type'];
        $amount = $this->form->getState()['amount'];
        $note = $this->form->getState()['note'];
        $new_balance = $notification_tag = "";
        $ref_number = date('YmdHis') . uniqid();
        $ref_number = "MANUAL_".$ref_number;

        
    
        //Check if this user is actually a staff!

        $me = DB::table('users')->where('id',auth()->id())->first();

        if($me){
        // Retrieve the user record
        $user = DB::table('users')->where('email', $user_email)->first();
        

        //Get previous balance
        $user_old_balance = $user->balance;
        
    


        //Set new balance based on the selected type!
            if($type == 'credit'){
                $new_balance = (double)$amount + (double)$user_old_balance;
                $notification_tag = "credited";
            }elseif($type == 'debit'){
                $new_balance = (double)$user_old_balance - (double)$amount;
                $notification_tag = "debited";
            }else{
               Notification::make()
                ->title('Bad operation request!')
                ->warning()
                ->duration(10000)
                ->send();


                    //..............................
                    //....Send DB Notification + Sweet Alert
                    //..............................

                return;
            }




        if ($user) {
            // Update the user's package
            DB::table('users')
                ->where('email', $user_email)
                ->update(['balance' => $new_balance]);
            
                $this->form->fill();



                //Insert record into the transaction tb
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'wallet',
                'note' => $note,
                'operator_id' => auth()->id(),
                'status' => 'successful',
                'amount' => $this->ngn."".number_format($amount,2),
                'old_balance' => $this->ngn."".number_format($user_old_balance,2),
                'new_balance' => $this->ngn."".number_format($new_balance,2),
                'reference_number' => $ref_number
                
            ]);
                            //..............................
                                                //....Send DB Notification + Sweet Alert
                                                //..............................

                $this->dispatch(
                    'alert',
                    type: 'success',
                    title: 'Successful!',
                    text: "You've successfully ".$notification_tag." ".$user_email." with $this->ngn".number_format($amount,2),
                    button: 'Got it!'
                );


                //Send notification to the recipient...
                $recipient = User::where('email',$user_email)->first(); //auth()->user();

                
                Notification::make()
                ->title("Your wallet was $notification_tag with $this->ngn".number_format($amount,2))
                ->icon('heroicon-c-wallet')
                ->iconColor('primary')
                ->sendToDatabase($recipient);
            

                //Send Email to the recipient

                $message = "Your wallet was $notification_tag with $this->ngn".number_format($amount,2)." on ".date("l jS \of F Y h:i:s A").". Your new balance is ".$this->ngn."".number_format($new_balance,2).".";
                $subject = "Wallet ".ucfirst($notification_tag);
                $emailRecipient = $recipient->name;
                $this->sendEmail($user_email,$subject,$message,$emailRecipient);
                    

            
                
            
                

        } else {
            // Handle the case where user is not found
            // Notification::make()
            // ->title('Invalid User action!')
            // ->danger()
            // ->duration(10000)
            // ->send();

            $this->dispatch(
                'alert',
                type: 'error',
                title: 'Invalid Operation!',
                text: "Enter a valid user email!",
                button: 'Got it!'
            );
            $this->form->fill();

            return;
        }

    }else{
        return;
    }
    }
    

    public function render()
    {
       

        return view('livewire.credit-n-debit');
    }
}
