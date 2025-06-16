<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentTracker;
use App\Models\PaymentIntegration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\SweetBillNotificationEmail;
use Filament\Notifications\Notification;

class WebhookMonnifyController extends Controller
{

    public $ngn = "₦";
    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    //


    //####
    //####...........
    // Check duplicate notification
    // Check the status of the transaction 
    //####...........
    //####



   


    public function getTransactionStatus($transactionReference)
    {
      //Check transation status...
      $apiKey = PaymentIntegration::first()->monnify_api_key; 
      $secretKey = PaymentIntegration::first()->monnify_secret_key; 
      
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


         


           // Send the GET request with the token included in the request header


           $url = $baseUrl."api/v2/transactions/" . urlencode($transactionReference);

           $response = Http::withHeaders([
          'Content-Type' => 'application/json',
          'Authorization' => 'Bearer '.$accessToken, // Replace with your OAuth 2.0 Bearer token
      ])->get($url);

      if ($response->successful()) {
          $responseData = $response->json();
          // Check if the request was successful
          if ($responseData['requestSuccessful']) {
              // Extract transaction status and update database records accordingly
              $transactionStatus = $responseData['responseBody']['paymentStatus'];

              return $transactionStatus;

          } else {
              // Log or handle unsuccessful request
              $errorMessage = $responseData['responseMessage'];
              // Handle error appropriately
          }
      } else {
          // Log or handle request failure
          $errorMessage = 'Failed to fetch transaction status';
          // Handle error appropriately
      }

          
      }
    }


    //public function handleWebhook(Request $request)
    //{ 
      //  return response()->json(['message' => 'Webhook received successfully Fake'], 200);
    //}
    public function sendEmail($toEmail,$subject,$email_message,$emailRecipient){    

        try {
            $response = Mail::to($toEmail)->send(new SweetBillNotificationEmail($subject,$email_message,$emailRecipient));
            
        } catch (Exception $e) {
           
            Log::error('Unable to send email '. $e->getMessage() );
        }
    
    }
   public function handleWebhook(Request $request)
    {   
        // Extract Monnify signature from the request header
        $monnifySignature = $request->header('monnify-signature');
    
        // Compute hash of the request body using your client secret key
        $computedHash = hash_hmac('sha512', $request->getContent(), 'iytxxxxxxxx*QRFQZYXRJCLZXWYooowQsxcTU5P9');
    
        // Compare the computed hash with the one sent by Monnify
        if ($monnifySignature !== $computedHash) {
            // Invalid request, log or handle error
            return response()->json(['error' => 'Invalid signature'], 400);
            //return response()->json(['message' => 'Webhook received successfully Fake'], 200);
             
        }
    
        // Request is valid, process the notification
        // Extract event data from the request body
        $eventData = $request->json()->all();
    
        // Extract event type and event data
        $eventType = $eventData['eventType'];
        $eventPayload = $eventData['eventData'];
    
        


        // Perform actions based on the event type
        switch ($eventType) {
            case 'SUCCESSFUL_TRANSACTION':
                // Handle successful transaction event
                // Extract amountPaid and customer email
                $amountPaid = $eventPayload['amountPaid'];
                $customerEmail = $eventPayload['customer']['email'];




                $automated_charges = PaymentIntegration::first()->automated_charges;
                $amountPaid = (double)$amountPaid - (double)$automated_charges;
    
                // Save the extracted data to the database
                // Assuming you have a model named Transaction, you can create a new instance and save it

                $customer_old_balance = User::where('email',$customerEmail)->first()->balance;
                $new_balance = (double)$customer_old_balance + (double)$amountPaid;

                $currentDateTime = Carbon::now();

                $formattedDateTime = $currentDateTime->format('YmdHi');
                $randomString = $this->generateRandomString(10);
                $requestId = $formattedDateTime . $randomString;

                $recipient = User::where('email',$customerEmail)->first(); //auth()->user();
        
                if(strlen($requestId) < 12) {
                    $requestId .= $this->generateRandomString(12 - strlen($requestId));
                }

                $requestId = "AUTOMATED_".$requestId;

                $transactionReference = $eventPayload['transactionReference'];


                


                //Check duplicate transaction before moving on..
                
                $payment_check = PaymentTracker::where('ref_key', $transactionReference)->count();

                if($payment_check > 0){
                    return response()->json(['error' => 'Duplicate transaction'], 400);
                    
                }




                //Handle transaction status check...
               //Call function to get transaction status
                $transactionStatus = $this->getTransactionStatus($transactionReference);

                //Stop further operations if transaction status is not PAID
                if ($transactionStatus !== 'PAID') {
                    return response()->json(['error' => 'Transaction status is not PAID'], 400);
                }

                DB::table('users')
                ->where('email', $customerEmail)
                ->update([
                    'balance' => $new_balance,
                ]);


                //Record Transaction
                Transaction::create([
                    'type' => 'automated',
                    'user_id' => $recipient->id,
                    'api_response' => "Your wallet has been credited with the sum of  $this->ngn".number_format($amountPaid,2)." from your automated funding.",
                    'status' => 'successful',
                    'note' => "Your wallet has been credited with the sum of  $this->ngn".number_format($amountPaid,2)." from your automated funding.",
                    
                    'amount' => $this->ngn.number_format($amountPaid,2),
                    'old_balance' => $this->ngn.number_format($customer_old_balance,2),
                    'new_balance' => $this->ngn.number_format($new_balance,2),
                    'reference_number' => $requestId,
                ]);

                //Send DB Notification to the user

                
                
                Notification::make()
                ->title("Your wallet has been credited with the sum of  $this->ngn".number_format($amountPaid,2)." from your automated funding.")
                ->icon('heroicon-c-wallet')
                ->iconColor('primary')
                ->sendToDatabase($recipient);



                
            //Send Email to the recipient

            $message = "Your wallet has been credited with the sum of  $this->ngn".number_format($amountPaid,2)." from your automated funding  on ".date("l jS \of F Y h:i:s A").". Your new balance is ".$this->ngn."".number_format($new_balance,2).".";
            $subject = "Automated Funding of ".$this->ngn . "" . number_format($amountPaid, 2);
            $emailRecipient = $recipient->name;
            $this->sendEmail($customerEmail,$subject,$message,$emailRecipient);



                //Record the payment to the tracker
                
                PaymentTracker::create([
                    'ref_key' => $transactionReference
                ]);

    
                // Respond with a 200 HTTP status code to acknowledge receipt of the notification
                return response()->json(['message' => 'Webhook received successfully ...FIne'], 200);
    
            // Add cases for other event types as needed
            default:
                // Unsupported event type, log or handle error
                return response()->json(['error' => 'Unsupported event type'], 400);
        }
    }
   
}
