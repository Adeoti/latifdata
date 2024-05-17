<?php

namespace App\Filament\Customer\Widgets;

use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use App\Models\PaymentIntegration;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;

class KYCWidget extends Widget implements HasForms
{

    protected int | string | array $columnSpan = 'full';

    use InteractsWithForms;


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
            

            Section::make('KYC Info')
                ->description('Provide your correct BVN and NIN to generate your automated account numbers.')
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

                   


                        
                ])->columns('2')
                ->visible(function(){
                    
                    if(auth()->user()->has_accounts == true){
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

    if(auth()->user()->has_accounts == true){
        return [];
    }else{
         return [
        Action::make('Update')
            ->color('primary')
            ->requiresConfirmation()
            ->icon('heroicon-m-check')
            ->submit('Update'),
    ];
    }
   
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

public function verifyBVN(){

}

public function createMonnifyAccounts($bvn,$nin){

   //$this->verifyBVN();

    $currentDateTime = Carbon::now();

    $formattedDateTime = $currentDateTime->format('YmdHi');
    $randomString = $this->generateRandomString(10);
    $requestId = $formattedDateTime . $randomString;

    if(strlen($requestId) < 12) {
        $requestId .= $this->generateRandomString(12 - strlen($requestId));
    }



    //$requestId .= "SWEETBILL";


    
    // $apiKey = 'MK_TEST_Y0J7HGM835'; //Sandbox
    // $secretKey = 'KUW10CC1LUPD5V9J24N6U7RH4WN82LCN'; //Sandbox
    // $baseUrl = 'https://sandbox.monnify.com/'; //Sandbox 
    // $contractCode = '1128816807';

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
            "accountReference" => $requestId,
            "accountName" => auth()->user()->name,
            "currencyCode" => "NGN",
            "contractCode" => $contractCode,
            "customerEmail" => auth()->user()->email,
            "customerName" => auth()->user()->name,
            "bvn" => $bvn,
            "nin" => $nin,
            "getAllAvailableBanks" => true
        ];
        
        // Send the POST request with the token included in the request header
        $accountResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken, 
            'Content-Type' => 'application/json'
        ])->post($baseUrl.'api/v2/bank-transfer/reserved-accounts', $payload);
        
        // Handle the response as needed...
        
        $decodedAccountResponse = json_decode($accountResponse->body(), true);


       //dd($decodedAccountResponse);
       //Nothing should be here
        
            

// Check if the request was successful
if ($decodedAccountResponse['requestSuccessful']) {
    // Extract the decodedAccountResponse body

    if($decodedAccountResponse['requestSuccessful'] != true){

        $this->dispatch(
            'alert',
            title: 'Invalid KYC Info',
            text: 'Kindly provide your correct BVN and NIN details to align with the CBN directive!',
            button: 'Got it',
            type: 'error'
        );
        return;
    }

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
        'accounts' => $accountsJson,
        'has_accounts' => true
    ]);

     $this->dispatch(
            'alert',
            type: 'success',
            title: 'Successful!',
            text: "You've successfully satisfied the requirements of the CBN!",
            button: 'Great!'
        );
    

} else {
    // Handle unsuccessful request
    $responseMessage = $decodedAccountResponse['responseMessage'];
    $responseCode = $decodedAccountResponse['responseCode'];

    $this->dispatch(
        'alert',
        type: 'error',
        title:'ERROR '.$responseCode.': KYC Error',
        text: 'Make sure you are providing your correct BVN and NIN details.',
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



public function update(): void
{

    


    auth()->user()->update(
        $this->form->getState()
    );

    if(!auth()->user()->has_accounts){
        $bvn = $this->form->getState()['bvn'];
        $nin =  $this->form->getState()['nin'];
        
        
        $this->createMonnifyAccounts($bvn,$nin);
   }

        // $this->dispatch(
        //     'alert',
        //     type: 'success',
        //     title: 'Successful!',
        //     text: "You've successfully Edited Your Profile!",
        //     button: 'Happy!'
        // );
}






    protected static string $view = 'filament.customer.widgets.k-y-c-widget';
}
