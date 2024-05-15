<?php

namespace App\Filament\Pages;

use App\Models\PaymentIntegration;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class PaymentIntegrations extends Page implements HasForms
{

    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string $view = 'filament.pages.payment-integrations';
    protected static ?string $navigationGroup = "Control Panel";
    protected static ?int $navigationSort = 3;


    protected static ?string $title="Payment Integration Panel";
    protected ?string $heading = "Payment Integrations";
    protected ?string $subheading = "Set Your Automated Payment Endpoints.";
    protected static ?string $navigationLabel = "Payment Integrations";


     // public static function canAccess(): bool
    // {
    //    return auth()->user()->canManageSettings();
    // }
    
//     public function form(Form $form): Form
// {





//     return $form
//         ->schema([
            




//             Section::make('Automated Payment')->schema([
               
//                 TextInput::make('monnify_api_key')
//                     ->label('Monnify API Key')
//                     ->required()
//                     ->password()
//                     ->default('000000000')
//                     ->revealable(),
               
//                 TextInput::make('monnify_secret_key')
//                     ->label('Monnify Secret Key')
//                     ->required()
//                     ->default(000000000)
//                     ->password()
//                     ->revealable(),
               
//                 TextInput::make('monnify_contract_code')
//                     ->label('Monnify Contract Code')
//                     ->required()
//                     ->password()
//                     ->default(000000000)
//                     ->revealable(),
               
//                 TextInput::make('monnify_bvn')
//                     ->label('Monnify BVN')
//                     ->required()
//                     ->password()
//                     ->default(000000000)
//                     ->revealable(),
               
//                 TextInput::make('paystack_secret_key')
//                     ->label('Paystack Secret Key')
//                     ->password()
//                     ->default(000000000)
//                     ->revealable(),
               
//                 TextInput::make('paystack_live_key')
//                     ->label('Paystack Live Key')
//                     ->password()
//                     ->default(000000000)
//                     ->revealable(),
               

//             ])->columns(3),


//             Section::make('Manual Payment')
//                 ->schema([
//                     Select::make('manual_bank_name')
//                        // ->options($bankList)
//                         ->label('Bank Name'),

//                     TextInput::make('manual_account_name')
//                         ->label('Account Name'),

//                     TextInput::make('manual_account_number')
//                         ->label('Account Number')
//                         ->numeric(),



                        
//                 ])->columns('3')
            
//         ])
//         ->statePath('data')
//         ->model(PaymentIntegration::class);
// }


// protected function getFormActions(): array
// {
//     return [
//         Action::make('Update')
//             ->color('primary')
//             ->submit('Update'),
//     ];
// }
 
// public function update(): void
// {
//     PaymentIntegration::updateOrCreate(
//         $this->form->getState()
//     );
//         $this->dispatch(
//             'alert',
//             type: 'success',
//             title: 'Successful!',
//             text: "You've successfully updated your payment settings!",
//             button: 'Happy!'
//         );
// }
}
