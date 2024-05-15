<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Filament\Forms\Form;
use App\Models\Transaction;
use App\Models\SiteSettings;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use App\Filament\Customer\Pages\Settings;
use Filament\Forms\Concerns\InteractsWithForms;

class UpgradeLevel extends Component implements HasForms
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

        $current_package = User::find(auth()->id())->first()->package;
        $current_package = ucfirst($current_package);
        return $form
            ->schema([
                Section::make("👉 Your balance is: $this->ngn".$user_balance)
                ->description("Your current account level is [[".$current_package."]]")
                ->schema([
                    Radio::make('package')
                    ->inline()
                    ->label('')
                    ->columnSpan(2)
                    ->required()
                    ->inlineLabel(false)
                    ->options([
                        'agent' => 'AGENT',
                        'special' => 'SPECIAL',
                        'api' => 'API',
                        'portal' => 'Own a VTU Website like SweetBill'
                    ])
                    ->descriptions([
                        'agent' => "You will be charged $this->ngn".number_format(SiteSettings::first()->agent_charges,2).".",
                        'special' => "You will be charged $this->ngn".number_format(SiteSettings::first()->special_charges,2).".",
                        'api' => "You will be charged $this->ngn".number_format(SiteSettings::first()->api_charges,2).".",
                        'portal' => "You will be charged $this->ngn".number_format(SiteSettings::first()->portal_dev_charges,2)."."
                    ]),

                    TextInput::make('transaction_pin')
                        ->required()
                        ->numeric()
                        ->password()
                        ->revealable()
                    
                ])->columns(1)

            ])
            ->statePath('data');
    }


    public function upgradeLevel(): void
    {
        $ngn = "₦";
        $package = $this->form->getState()['package'];
        $transaction_pin = $this->form->getState()['transaction_pin'];
        $note = "You upgraded your account to the ".strtoupper($package)." package" ;
       

        $ref_number = date('YmdHis') . uniqid();
        $ref_number = "Package_".$ref_number;

        $sender = DB::table('users')->where('id', auth()->id())->first();
        $sender_old_balance = $sender->balance;

        $package_charges = 0;

        switch($package){
            case 'agent':
                $package_charges = SiteSettings::first()->agent_charges;
            break;

            case 'special':
                $package_charges = SiteSettings::first()->special_charges;
            break;

            case 'api':
                $package_charges = SiteSettings::first()->api_charges;
            break;

            case 'portal':
                $package_charges = SiteSettings::first()->portal_dev_charges;
            break;
            default:
            $this->dispatch(
                'alert',
                type: 'error',
                title: 'Invalid Package',
                text: "Choose the correct package and try again!",
                button: 'Ouch!'
            );
            $this->form->fill();
                return;
        }


        //Check if the user is not currently on this package!

            
            $current_package = User::find(auth()->id())->first()->package;
            if($package == $current_package){

                $this->dispatch(
                    'alert',
                    type: 'warning',
                    title: '🧐 Duplicate Operation',
                    text: "You are already on this package! Kindly choose another package to continue",
                    button: 'Ouch!'
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

        if ($sender_old_balance > $package_charges) {


            $sender_new_balance = $sender_old_balance - $package_charges;


             // Update the User's balance and package
           DB::table('users')
           ->where('id', auth()->id())
           ->update([
                'balance' => $sender_new_balance,
                'package' => $package
            ]);

            //Record the transaction

            Transaction::create([
                'user_id' => $sender->id,
                'type' => 'package',
                'note' => $note,
                'operator_id' => auth()->id(),
                'status' => 'successful',
                'amount' => $ngn."".number_format($package_charges,2),
                'old_balance' => $ngn."".number_format($sender_old_balance,2),
                'new_balance' => $ngn."".number_format($sender_new_balance,2),
                'reference_number' => $ref_number
                
            ]);

            $this->dispatch(
                'alert',
                type: 'success',
                title: '👍 Congratulations',
                text: "You've successfully upgraded your account to the ".strtoupper($package)." package",
                button: 'Ok, thanks!'
            );

        }else{
            $this->dispatch(
                'alert',
                type: 'warning',
                title: 'Insufficient Fund',
                text: "Kindly top up your wallet and try again!",
                button: 'Got it!'
            );
            
            $this->form->fill();
            return;
        }




    }




    public function render()
    {
        return view('livewire.upgrade-level');
    }
}
