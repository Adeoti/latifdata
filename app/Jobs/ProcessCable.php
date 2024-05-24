<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use App\Models\CableSubscription;
use App\Models\TransactionPuller;
use App\Models\PaymentIntegration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessCable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
     protected $userId;
     protected $requestId;
     protected $cable_id;
     protected $amount_to_pay;
     protected $phone_number;
     protected $decoder_number;
     protected $cable_type;
     protected $cable_plan;
     protected $cable_amount;
     protected $cable_charges;
     protected $subscription_type;
     protected $cable_vendor;

    public function __construct($userId,$requestId,$cable_id,$amount_to_pay,$phone_number,$decoder_number,$cable_type,$cable_plan,$cable_amount,$cable_charges,$subscription_type,$cable_vendor)
    {
        //
        $this->userId = $userId;
        $this->requestId = $requestId;
        $this->cable_id = $cable_id;
        $this->amount_to_pay = $amount_to_pay;
        $this->phone_number = $phone_number;
        $this->decoder_number = $decoder_number;
        $this->cable_type = $cable_type;
        $this->cable_plan = $cable_plan;
        $this->cable_amount = $cable_amount;
        $this->cable_charges = $cable_charges;
        $this->subscription_type = $subscription_type;
        $this->cable_vendor = $cable_vendor;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
         switch($this->cable_vendor){
            case 'vtpass':
                $this->buyCableFromVtPass($this->userId,$this->requestId,$this->cable_id,$this->amount_to_pay,$this->phone_number,$this->decoder_number,$this->cable_type,$this->cable_plan,$this->cable_amount,$this->cable_charges,$this->subscription_type,$this->cable_vendor);
            break;
            
        }
    }


    public function getMyVtPassBalance(){
        $balance_mi = 0;

        $response = Http::withHeaders([
            'api-key' => PaymentIntegration::first()->vtpass_api_key,
            'public-key' => PaymentIntegration::first()->vtpass_public_key,
            'Content-Type' => 'application/json',

        ])->get('https://api-service.vtpass.com/api/balance');
        
           // dd($response);

        // Check if the request was successful
        if ($response->successful()) {
            // Request was successful, handle the response
            $responseData = $response->json();
            
            $balance_mi = $responseData['contents']['balance'];

        } else {

             //Pull out the notification....

             TransactionPuller::create([
                'user_id' => $this->userId,
                'status' => 'warning',
                'transaction_key' => $this->requestId,
                'title' => 'ERROR BLNC',
                'message' => "Something went wrong. Try again or chat our reps!",
            ]);
            return;
            
            //dd();

        }


        return $balance_mi;
    }


    public function buyCableFromVtPass($userId,$requestId,$cable_id,$amount_to_pay,$phone_number,$decoder_number,$service_id,$variation_code,$cable_amount,$cable_charges,$subscription_type,$cable_vendor){

        $ngn = "₦";

        $requestId .= "_CABLE";
            //Check my balance before moving on...

            $user = User::find($userId);
            $myVtPassBalance = $this->getMyVtPassBalance();

            if($myVtPassBalance > $amount_to_pay){

                $old_balance = $user->balance;
                $new_balance = (double)$old_balance - (double)$amount_to_pay;
                $transactionStatus = "pending";
                $temporary_message = "Purchase of ". ucfirst($service_id) ." ".strtoupper($variation_code)." package to $decoder_number";
                $endpoint = CableSubscription::find($cable_id)->endpoint;
                $cable_name = CableSubscription::find($cable_id)->name;
                $customerName = "";
                $requestAmount = 0;




                //Check the billerCode and get the customer's name.....

                //Send a verify request to the VTPASS Endpoint...
                $response = Http::withHeaders([
                    'api-key' => PaymentIntegration::first()->vtpass_api_key,
                    'secret-key' => PaymentIntegration::first()->vtpass_secret_key,
                    'Content-Type' => 'application/json'
                ])->post('https://api-service.vtpass.com/api/merchant-verify', [
                    'billersCode' => $decoder_number,
                    'serviceID' => $service_id,
                ]);
                
                // Check if the request was successful
                if ($response->successful()) {
                    $responseData = $response->json();
                    // Handle response data
                    $slicedResponce = json_decode($response->body(), true);


                    if (array_key_exists('Renewal_Amount', $slicedResponce['content'])){
                        $requestAmount = $slicedResponce['content']['Renewal_Amount'];
                    }
                    if (array_key_exists('Customer_Name', $slicedResponce['content'])){
                    
                        $customerName = $slicedResponce['content']['Customer_Name'];



                    }else{  

                         //Pull out the notification....

                            TransactionPuller::create([
                                'user_id' => $this->userId,
                                'status' => 'warning',
                                'transaction_key' => $this->requestId,
                                'title' => 'Invalid Decoder Number',
                                'message' => "Kindly provide a valid Decoder Number and Try again!",
                            ]);

                        return;
                    }


                    DB::table('users')
                    ->where('id', $userId)
                    ->update([
                        'balance' => $new_balance,
                    ]);
    
                    
    
                    Transaction::create([
                        'type' => 'cable',
                        'user_id' => $userId,
                        'api_response' => $temporary_message,
                        'status' => $transactionStatus,
                        'note' => $temporary_message,
                        'phone_number' => $phone_number,
                        'amount' => "$ngn".number_format($cable_amount,2),
                        'old_balance' => "$ngn".number_format($old_balance,2),
                        'new_balance' => "$ngn".number_format($new_balance,2),
                        'reference_number' => $requestId,
                        'plan_name' => $cable_name,
                        'iuc_number' => $decoder_number,
                        'cable_plan' => strtoupper($variation_code),
                        'charges' => "$ngn".number_format($cable_charges,2)
                    ]);
    
    
                    $payload = [
                        'phone' => $phone_number,
                        'serviceID' => $service_id,
                        'billersCode' => $decoder_number,
                        'variation_code' => $variation_code,
                        'request_id' => $requestId,
                    ];
                    
                   //Now, handle the transaction based on the cable type

                if($service_id === 'startimes'){
                    
                }elseif($service_id === 'dstv'){    


                    $payload['subscription_type'] = $subscription_type;
                    $payload['amount'] = $requestAmount;

                }elseif($service_id === 'gotv'){

                    $payload['subscription_type'] = $subscription_type;
                    $payload['amount'] = $requestAmount;

                }

                    

                } else {
                    // Handle unsuccessful response
                    
                    //Pull out the notification....

                    TransactionPuller::create([
                        'user_id' => $this->userId,
                        'status' => 'error',
                        'transaction_key' => $this->requestId,
                        'title' => 'Biller Err: Something went wrong',
                        'message' => "Please try again later or reach out to our reps for help",
                    ]);

                    return;

                }



                $responseCable = Http::withHeaders([
                    'api-key' => PaymentIntegration::first()->vtpass_api_key,
                    'secret-key' => PaymentIntegration::first()->vtpass_secret_key,
                    'Content-Type' => 'application/json'
                ])->post(trim($endpoint), $payload);


                $responsePurchase = json_decode($responseCable->body(), true);

                //dd($responsePurchase);
                if (isset($responsePurchase['content']['transactions']['status'])) {
                    $code = $responsePurchase['code'];
                    $content = $responsePurchase['content'];
                    $response_description = $responsePurchase['response_description'];
                    $requestId = $responsePurchase['requestId'];
                    $amount = $responsePurchase['amount'];
                    $transaction_date = $responsePurchase['transaction_date'];
                    $purchased_code = $responsePurchase['purchased_code'];
                    
                    // Extract variables from the 'content' array
                    $transactions = $content['transactions'];
                    
                    // Extract variables from the 'transactions' array
                    $status = $transactions['status'];
                    $channel = $transactions['channel'];
                    $transactionId = $transactions['transactionId'];
                    $method = $transactions['method'];
                    $platform = $transactions['platform'];
                    
                    $discount = $transactions['discount'];
                    $email = $transactions['email'];
                    $phone = $transactions['phone'];
                    $type = $transactions['type'];
                    $convinience_fee = $transactions['convinience_fee'];
                    $commission = $transactions['commission'];
                    $transaction_amount = $transactions['amount'];
                    $total_amount = $transactions['total_amount'];
                    $quantity = $transactions['quantity'];
                    $unit_price = $transactions['unit_price'];
                    
                    //". ucfirst($service_id) ." ".strtoupper($variation_code)." package to $decoder_number"

                    $successMessage = "You've successfully purchased ".ucfirst($service_id)." ".strtoupper($variation_code)." for <b>".$decoder_number."</b> on ".date("l jS \of F Y h:i:s A").".";

                        if($status === 'delivered'){

                            DB::table('transactions')
                            ->where('reference_number', $requestId)->where('user_id',$userId)
                            ->update([
                                'status' => "successful",
                                'api_response' => $successMessage,
                                'note' => $successMessage,
                                'customer_name' => $customerName,
                            ]);

                            //Pull out the notification....

                            TransactionPuller::create([
                                'user_id' => $this->userId,
                                'status' => 'success',
                                'transaction_key' => $this->requestId,
                                'title' => 'Successful',
                                'message' => $successMessage." = (".$cable_name.")",
                            ]);


                        }elseif($status === 'failed'){
                    
                    DB::table('users')
                    ->where('id', $userId)
                    ->update([
                        'balance' => $old_balance,
                    ]);

                    DB::table('transactions')
                    ->where('reference_number', $requestId)->where('user_id',$userId)
                    ->update([
                        'status' => "failed",
                        'api_response' => "FAILED: ".$response_description,
                        'new_balance' => "$ngn".number_format($old_balance,2),
                        'charges' => $ngn."00.00",
                        'note' => "FAILED: ".$temporary_message
                    ]);


                     //Pull out the notification....

                     TransactionPuller::create([
                        'user_id' => $this->userId,
                        'status' => 'error',
                        'transaction_key' => $this->requestId,
                        'title' => 'Error Occurred',
                        'message' => 'Something went wrong. Use a valid DECODER NUMBER. Please try again!',
                    ]);

                        }
                    
                }else{
                    //Transaction Failed.
                    //Refund the customer
                    //Update the transaction
                    $code = $responsePurchase['code'];
                    $content = $responsePurchase['content'];
                    $response_description = $responsePurchase['response_description'];

                    DB::table('users')
                    ->where('id', $userId)
                    ->update([
                        'balance' => $old_balance,
                    ]);

                    DB::table('transactions')
                    ->where('reference_number', $requestId)->where('user_id',$userId)
                    ->update([
                        'status' => "failed",
                        'api_response' => "FAILED: ".$response_description,
                        'new_balance' => "$ngn".number_format($old_balance,2),
                        'charges' => $ngn."00.00",
                        'note' => "FAILED: ".$temporary_message
                    ]);

                   

                     //Pull out the notification....

                     TransactionPuller::create([
                        'user_id' => $this->userId,
                        'status' => 'error',
                        'transaction_key' => $this->requestId,
                        'title' => 'Error Occurred',
                        'message' => 'Something went wrong. Please try again!',
                    ]);


                }

                
                



            }else{

             //Pull out the notification....

             TransactionPuller::create([
                'user_id' => $this->userId,
                'status' => 'error',
                'transaction_key' => $this->requestId,
                'title' => 'Error Occurred',
                'message' => 'Something went wrong we are working on it quickly. Thanks!',
            ]);

            }


    }
}
