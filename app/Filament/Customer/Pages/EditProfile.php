<?php

namespace App\Filament\Customer\Pages;

use App\Jobs\ProcessAirtimePurchase;
use App\Mail\SweetBillNotificationEmail;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use App\Models\PaymentIntegration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\Mail;

class EditProfile extends Page implements HasForms
{

    use InteractsWithForms;


    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.customer.pages.edit-profile';
    protected static ?string $navigationGroup = "Settings";


    protected static ?string $title = "Profile Settings";

    protected static ?int $navigationSort = 40;


    public ?array $data = [];
 
    public function mount(): void
    {
        $this->form->fill(
            auth()->user()->attributesToArray()
        );
    }

    public function form(Form $form): Form
{
    return $form
        ->schema([
            




            Section::make('')->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->required(),
                TextInput::make('transaction_pin')
                    ->label('Transaction Pin')
                    ->required()
                    ->password()
                    ->revealable(),
                TextInput::make('phone_number')
                    ->autofocus()

            ])->columns(3),


            Section::make('KYC Info')
                
                ->schema([
                    TextInput::make('bvn')
                        ->label('BVN')
                        ->numeric()
                        ->required()
                        ->prefix('BVN'),

                    TextInput::make('nin')
                        ->label('NIN')
                        ->numeric()
                        ->required()
                        ->prefix('NIN'),

                    DatePicker::make('bvn_date_of_birth')
                        ->required()
                        ->label('Date of birth (attached to BVN) ')
                        ->prefix('DOB')
                        ->hidden(true)
                        ,


                        
                ])->columns('2')
                ->visible(function(){
                    
                    if(auth()->user()->filled_kyc){
                        return false;
                    }else{
                        return true;
                    }
            })
            
        ])
        ->statePath('data')
        ->model(auth()->user());
}


protected function getFormActions(): array
{
    return [
        Action::make('Update')
            ->color('primary')
            ->requiresConfirmation()
            ->icon('heroicon-m-check')
            ->submit('Update'),
    ];
}


private function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $randomString;
}




public function updateMonnifyAccounts($bvn,$nin){

  


    $currentDateTime = Carbon::now();

    $formattedDateTime = $currentDateTime->format('YmdHi');
    $randomString = $this->generateRandomString(10);
    $requestId = $formattedDateTime . $randomString;

    if(strlen($requestId) < 12) {
        $requestId .= $this->generateRandomString(12 - strlen($requestId));
    }


    $apiKey = PaymentIntegration::first()->monnify_api_key; 
    $secretKey = PaymentIntegration::first()->monnify_secret_key; 
    $contractCode = PaymentIntegration::first()->monnify_contract_code;
    
    $baseUrl = 'https://api.monnify.com/'; //Production



    $base64Encoded = base64_encode("$apiKey:$secretKey");

    // Make the POST request
    $response = Http::withHeaders([
        'Authorization' => 'Basic ' . $base64Encoded,
    ])->post($baseUrl.'api/v1/auth/login');

    $responseData = json_decode($response->body(), true);


    // Check if the request was successful
    if ($responseData['requestSuccessful']) {
        // Extract the access token and expiration time
        $accessToken = $responseData['responseBody']['accessToken'];
        $expiresIn = $responseData['responseBody']['expiresIn'];


        //Handle the account creation process

        // Define the request payload
        $payload = [
            "accountReference" => auth()->user()->account_reference,
            "bvn" => $bvn,
            "nin" => $nin,
        ];
        
        // Send the POST request with the token included in the request header
        $accountResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken, 
            'Content-Type' => 'application/json'
        ])->post($baseUrl.'api/v1/bank-transfer/reserved-accounts/{accountReference}/kyc-info', $payload);
        
        // Handle the response as needed...
        
        $decodedAccountResponse = json_decode($accountResponse->body(), true);

        
            dd($decodedAccountResponse);

// Check if the request was successful
if ($decodedAccountResponse['requestSuccessful']) {
    // Extract the decodedAccountResponse body
    $responseBody = $decodedAccountResponse['responseBody'];

    // Access individual fields in the decodedAccountResponse body
    $contractCode = $responseBody['contractCode'];
    $accountReference = $responseBody['accountReference'];
    $customerEmail = $responseBody['customerEmail'];
    $accounts = $responseBody['accounts'];
    


     // Convert the accounts array to JSON
     $accountsJson = json_encode($accounts);

     // Update the user's accounts column with the JSON data
     auth()->user()->update([
        'filled_kyc' => true,
    ]);
    

} else {
    // Handle unsuccessful request
    $responseMessage = $decodedAccountResponse['responseMessage'];
    $responseCode = $decodedAccountResponse['responseCode'];

    $this->dispatch(
        'alert',
        type: 'error',
        title:'ERROR '.$responseCode.': KYC Error',
        text: 'Error syncing your KYC info. Please try again.',
        button: 'Got it!'  
);
    // Handle error message or code...
}



    } else {
        // Handle unsuccessful response...

        $this->dispatch(
            'alert',
            type: 'error',
            title:'KYC Error',
            text: 'Error syncing your KYC info. Please try again.',
            button: 'Got it!'  
    );
    }




}






protected function testSweetBillBalanceEndPoint(){

    $response = Http::withHeaders([
        'email' => 'adeoti360@gmail.com',
        'password' => '7DP75syvXML#',
        'api_key' => '202405040704iuncrHgIai6635cfd23ded9AG2OEBMN6635cfd23dedaSWEETBILL6635cfd23dedb562d0d6fWI32Me4Nfdee00cc232ed3GLadEdiEd3d2ud24d623geN6rf76L65Ei3T6fEdfdB3cd6S2E66ccb003dLd4WaAE6TccdBGI935c53OBMde32ed256IfA5B3SOLH5nd202405040704',
    ])->get('http://127.0.0.1:8000/api/v1/balance');

        return $response;
}


 
public function update(): void
{



    if(auth()->user()->filled_kyc !== true){
        $bvn = $this->form->getState()['bvn'];
        $nin =  $this->form->getState()['nin'];
        
        
        $this->updateMonnifyAccounts($bvn,$nin);
    }


    // auth()->user()->update(
    //     $this->form->getState()
    // );
    //     $this->dispatch(
    //         'alert',
    //         type: 'success',
    //         title: 'Successful!',
    //         text: "You've successfully Edited Your Profile!",
    //         button: 'Happy!'
    //     );
}

}
