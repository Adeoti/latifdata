<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Models\PaymentIntegration;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateAutomatedAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $user;
    public function __construct($user)
    {
        //
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $this->createMonnifyAccounts($this->user);
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
    
    
    
    protected function createMonnifyAccounts($theUser){
    
     
    
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
                "accountName" => $theUser->name,
                "currencyCode" => "NGN",
                "contractCode" => $contractCode,
                "customerEmail" => $theUser->email,
                "customerName" => $theUser->name,
                "bvn" => "22206510323",
                "nin" => "66376968325",
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
    
            // $this->dispatch(
            //     'alert',
            //     title: 'Invalid KYC Info',
            //     text: 'Kindly provide your correct BVN and NIN details to align with the CBN directive!',
            //     button: 'Got it',
            //     type: 'error'
            // );
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
         $theUser->update([
            'accounts' => $accountsJson,
            'has_accounts' => true,
            'account_reference' => $accountReference
        ]);
    
        //  $this->dispatch(
        //         'alert',
        //         type: 'success',
        //         title: 'Successful!',
        //         text: "You've successfully satisfied the requirements of the CBN!",
        //         button: 'Great!'
        //     );
        
    
    } else {
        // Handle unsuccessful request
        $responseMessage = $decodedAccountResponse['responseMessage'];
        $responseCode = $decodedAccountResponse['responseCode'];
    
    //     $this->dispatch(
    //         'alert',
    //         type: 'error',
    //         title:'ERROR '.$responseCode.': KYC Error',
    //         text: 'Make sure you are providing your correct BVN and NIN details.',
    //         button: 'Got it!'  
    // );
        // Handle error message or code...
    }
    
    
    
        } else {
            // Handle unsuccessful response...
    
        //     $this->dispatch(
        //         'alert',
        //         type: 'error',
        //         title:'KYC Error',
        //         text: 'Error syncing your KYC info. Please try again.',
        //         button: 'Got it!'  
        // );
        }
    
    
    
    
    }
}
