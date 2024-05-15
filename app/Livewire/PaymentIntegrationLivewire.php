<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Forms\Form;
use App\Models\PaymentIntegration;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\DB;

class PaymentIntegrationLivewire extends Component implements HasForms
{

    use InteractsWithForms;

    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill(
            PaymentIntegration::find(1)->attributesToArray()
        );
    }



   



public function form(Form $form): Form
{
    $bankList = [
        'Access Bank' => 'Access Bank',
        'Citibank Nigeria' => 'Citibank Nigeria',
        'Diamond Bank' => 'Diamond Bank',
        'Ecobank Nigeria' => 'Ecobank Nigeria',
        'Enterprise Bank Limited' => 'Enterprise Bank Limited',
        'Fidelity Bank Nigeria' => 'Fidelity Bank Nigeria',
        'First Bank of Nigeria' => 'First Bank of Nigeria',
        'First City Monument Bank' => 'First City Monument Bank',
        'Guaranty Trust Bank' => 'Guaranty Trust Bank',
        'Heritage Bank Plc' => 'Heritage Bank Plc',
        'Keystone Bank Limited' => 'Keystone Bank Limited',
        'Mainstreet Bank Limited' => 'Mainstreet Bank Limited',
        'Opay' => 'Opay',
        'Polaris Bank' => 'Polaris Bank',
        'Stanbic IBTC Bank Nigeria Limited' => 'Stanbic IBTC Bank Nigeria Limited',
        'Standard Chartered Bank' => 'Standard Chartered Bank',
        'Sterling Bank' => 'Sterling Bank',
        'Union Bank of Nigeria' => 'Union Bank of Nigeria',
        'United Bank for Africa' => 'United Bank for Africa',
        'Unity Bank Plc' => 'Unity Bank Plc',
        'Wema Bank' => 'Wema Bank',
        'Zenith Bank' => 'Zenith Bank',
    ];

    $ngn = "₦";

    return $form
    ->schema([

        Section::make('Automated Payment')->schema([
           
            TextInput::make('monnify_api_key')
                ->label('Monnify API Key')
                ->required()
                ->password()
                ->default('000000000')
                ->revealable(),
           
            TextInput::make('monnify_secret_key')
                ->label('Monnify Secret Key')
                ->required()
                ->default(000000000)
                ->password()
                ->revealable(),
           
            TextInput::make('monnify_contract_code')
                ->label('Monnify Contract Code')
                ->required()
                ->password()
                ->default(000000000)
                ->revealable(),
           
            TextInput::make('monnify_bvn')
                ->label('Monnify BVN')
                ->required()
                ->password()
                ->default(000000000)
                ->revealable(),
           
            TextInput::make('automated_charges')
                ->label('Monnify Charges')
                ->prefix($ngn)
                ->required(),
           
            TextInput::make('paystack_secret_key')
                ->label('Paystack Secret Key')
                ->password()
                ->default(000000000)
                ->revealable(),
           
            TextInput::make('paystack_live_key')
                ->label('Paystack Live Key')
                ->password()
                ->default(000000000)
                ->revealable(),
           

        ])->columns(3),


        Section::make('Manual Payment')
            ->schema([
                Select::make('manual_bank_name')
                    ->options($bankList)
                    ->label('Bank Name'),

                TextInput::make('manual_account_name')
                    ->label('Account Name'),

                TextInput::make('manual_account_number')
                    ->label('Account Number')
                    ->numeric(),



                    
            ])->columns('3')
        
    ])
    ->statePath('data')
    ->model(PaymentIntegration::class);
}
    





    public function updatePaymentIntegration(): void
    {
        $monnify_api_key = $this->form->getState()['monnify_api_key'];
        $monnify_secret_key = $this->form->getState()['monnify_secret_key'];
        $monnify_contract_code = $this->form->getState()['monnify_contract_code'];
        $monnify_bvn = $this->form->getState()['monnify_bvn'];
        $paystack_secret_key = $this->form->getState()['paystack_secret_key'];
        $paystack_live_key = $this->form->getState()['paystack_live_key'];
        $manual_bank_name = $this->form->getState()['manual_bank_name'];
        $manual_account_name = $this->form->getState()['manual_account_name'];
        $manual_account_number = $this->form->getState()['manual_account_number'];

        $ref_number = date('YmdHis') . uniqid();
        $ref_number = "Wallet_".$ref_number;



        DB::table('payment_integrations')->update(
            $this->form->getState()
        );

        $this->dispatch(
            'alert',
            type: 'success',
            title: 'Successful!',
            text: "You've successfully updated your payment settings!",
            button: 'Happy!'
        );
        







    }








    public function render()
    {
        return view('livewire.payment-integration-livewire');
    }
}
