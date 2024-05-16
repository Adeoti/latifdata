<?php

namespace App\Filament\Customer\Pages;

use App\Models\PaymentIntegration;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;

class EditProfile extends Page implements HasForms
{

    use InteractsWithForms;


    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.customer.pages.edit-profile';


    protected static ?string $title = "Core Settings";

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
                        ->prefix('DOB'),


                        
                ])->columns('3')
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


public function verifyBVN(){

}

public function createMonnifyAccounts($bvn,$nin,$bvn_date_of_birth){

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
    
    $baseUrl = 'https://api.monnify.com/'; //Sandbox



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

        
            // Assuming $decodedAccountResponse contains the decodedAccountResponse from the POST request

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
        'accounts' => $accountsJson,
        'has_accounts' => true
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




// public function testWebhook(){

//     $transactionReference = 'MNFY|10|20240514191116|000088';
    
//         //Check transation status...
//         $apiKey = PaymentIntegration::first()->monnify_api_key; 
//         $secretKey = PaymentIntegration::first()->monnify_secret_key; 
        
//         $baseUrl = 'https://sandbox.monnify.com/'; //Sandbox
    
    
    
//         $base64Encoded = base64_encode("$apiKey:$secretKey");
    
//         // Make the POST request
//         $response = Http::withHeaders([
//             'Authorization' => 'Basic ' . $base64Encoded,
//         ])->post($baseUrl.'api/v1/auth/login');
    
//         $responseData = json_decode($response->body(), true);
    
    
//         // Check if the request was successful
//         if ($responseData['requestSuccessful']) {
//             // Extract the access token and expiration time
//             $accessToken = $responseData['responseBody']['accessToken'];
  
  
           
  
  
//              // Send the GET request with the token included in the request header
  
  
//              $url = $baseUrl."api/v2/transactions/" . urlencode($transactionReference);
  
//              $response = Http::withHeaders([
//             'Content-Type' => 'application/json',
//             'Authorization' => 'Bearer '.$accessToken, // Replace with your OAuth 2.0 Bearer token
//         ])->get($url);

//        // dd($response." | ". $accessToken);
  
//         if ($response->successful()) {
//             $responseData = $response->json();
//             // Check if the request was successful
//             dd($responseData);
//             if ($responseData['requestSuccessful']) {
//                 // Extract transaction status and update database records accordingly
//                 $transactionStatus = $responseData['responseBody']['paymentStatus'];
  
//                 //return $transactionStatus;
  
//             } else {
//                 // Log or handle unsuccessful request
//                 $errorMessage = $responseData['responseMessage'];
//                 // Handle error appropriately
//             }
//         } else {
//             // Log or handle request failure
//             $errorMessage = 'Failed to fetch transaction status';
//             // Handle error appropriately

//             dd('Could not get the response');
//         }
  
            
//         }
      



// }


 
public function update(): void
{

    //$this -> testWebhook();


   if(!auth()->user()->has_accounts){
        $bvn = $this->form->getState()['bvn'];
        $nin =  $this->form->getState()['nin'];
        $bvn_date_of_birth =  $this->form->getState()['bvn_date_of_birth'];
        
        $this->createMonnifyAccounts($bvn,$nin,$bvn_date_of_birth);
   }
    




    auth()->user()->update(
        $this->form->getState()
    );
        $this->dispatch(
            'alert',
            type: 'success',
            title: 'Successful!',
            text: "You've successfully Edited Your Profile!",
            button: 'Happy!'
        );
}

}
