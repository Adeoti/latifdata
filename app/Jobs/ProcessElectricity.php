<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use App\Models\TransactionPuller;
use App\Models\PaymentIntegration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessElectricity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

     protected $userId;
     protected $requestId;
     protected $amount_to_pay;
     protected $phone_number; 
     protected $meter_number;
     protected $service_id;
     protected $meter_type;
     protected $product_amount;
     protected $electricity_charges;
     protected $product_vendor;
     
    public function __construct($userId,$requestId,$amount_to_pay,$phone_number,$meter_number,$service_id,$meter_type,$product_amount,$electricity_charges,$product_vendor)
    {
        //
        $this->userId = $userId;
        $this->requestId = $requestId;
        $this->amount_to_pay = $amount_to_pay;
        $this->phone_number = $phone_number;
        $this->meter_number = $meter_number;
        $this->service_id = $service_id;
        $this->meter_type = $meter_type;
        $this->product_amount = $product_amount;
        $this->electricity_charges = $electricity_charges;
        $this->product_vendor = $product_vendor;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //

        switch($this->product_vendor){
                case 'vtpass':
                    $this->buyElectricityFromVtPass($this->userId,$this->requestId,$this->amount_to_pay,$this->phone_number,$this->meter_number,$this->service_id,$this->meter_type,$this->product_amount,$this->electricity_charges,$this->product_vendor);
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

    public function buyElectricityFromVtPass($userId,$requestId,$amount_to_pay,$phone_number,$meter_number,$service_id,$meter_type,$product_amount,$electricity_charges,$product_vendor){

        $ngn = "₦";

        $requestId .= "_ELECTRICITY";
        $user = User::find($userId);

            

            //Check my balance before moving on...
            $myVtPassBalance = $this->getMyVtPassBalance();
           //$myVtPassBalance = 2000000;

            if($myVtPassBalance > $amount_to_pay){

                $old_balance = $user->balance;
                $new_balance = (double)$old_balance - (double)$amount_to_pay;
                $transactionStatus = "pending";
                $temporary_message = "Electricity subscription of ". ucfirst($service_id) ." ".strtoupper($meter_type)." to $meter_number";
                $endpoint = "https://api-service.vtpass.com/api/pay";
                  
               
                $customerName = $customerName = "";
                $requestAmount = 0;




                //Check the billerCode and get the customer's name.....

                //Send a verify request to the VTPASS Endpoint...
                $response = Http::withHeaders([
                    'api-key' => PaymentIntegration::first()->vtpass_api_key,
                    'secret-key' => PaymentIntegration::first()->vtpass_secret_key,
                    'Content-Type' => 'application/json'
                ])->post('https://api-service.vtpass.com/api/merchant-verify', [
                    'billersCode' => $meter_number,
                    'serviceID' => $service_id,
                    'type' => $meter_type,
                ]);


                    // Verify meter number
                // $response = Http::withHeaders([
                //     'api-key' => "f40824cdb526d8d07bd1a4c7f54e2e9d",
                //     'secret-key' => "SK_458a2566c1c70073766c67f20498830d3d868f6d2b4",
                //     'Content-Type' => 'application/json'
                // ])->post('https://sandbox.vtpass.com/api/merchant-verify', [
                //     'billersCode' => $meter_number,
                //     'serviceID' => $service_id,
                //     'type' => $meter_type,
                // ]);
                
                // Check if the request was successful
                if ($response->successful()) {
                    $responseData = $response->json();
                    // Handle response data
                    $slicedResponce = json_decode($response->body(), true);


                    if (array_key_exists('Customer_Name', $slicedResponce['content'])){
                    
                        $customerName = $slicedResponce['content']['Customer_Name'];
                        $customerAddress = $slicedResponce['content']['Address'];



                    }else{  


                         //Pull out the notification....

                        TransactionPuller::create([
                            'user_id' => $this->userId,
                            'status' => 'error',
                            'transaction_key' => $this->requestId,
                            'title' => 'Invalid Meter Number',
                            'message' => "Kindly provide a valid Meter Number and Try again!",
                        ]);

                        return;
                    }


                    DB::table('users')
                    ->where('id', $userId)
                    ->update([
                        'balance' => $new_balance,
                    ]);
    
                    
    
                    Transaction::create([
                        'type' => 'electricity',
                        'user_id' => $userId,
                        'api_response' => $temporary_message,
                        'status' => $transactionStatus,
                        'note' => $temporary_message,
                        'phone_number' => $phone_number,
                        'amount' => "$ngn".number_format($product_amount,2),
                        'old_balance' => "$ngn".number_format($old_balance,2),
                        'new_balance' => "$ngn".number_format($new_balance,2),
                        'reference_number' => $requestId,
                        'meter_type' => $meter_type,
                        'meter_number' => $meter_number,
                        'customer_name' => $customerName,
                        'customer_address' => $customerAddress,
                        'disco_name' => ucfirst($service_id)."ity",
                        'charges' => "$ngn".number_format($electricity_charges,2)
                    ]);
    
    
                    $payload = [
                        'phone' => $phone_number,
                        'serviceID' => $service_id,
                        'billersCode' => $meter_number,
                        'variation_code' => $meter_type,
                        'request_id' => $requestId,
                        'amount' => $product_amount,
                    ];
                    
                   
                    

                } else {
                    // Handle unsuccessful response
                    //dd("Failed Here".$response." || ".$state." || ".$get('cableType'));


                    
                         //Pull out the notification....

                         TransactionPuller::create([
                            'user_id' => $this->userId,
                            'status' => 'error',
                            'transaction_key' => $this->requestId,
                            'title' => 'BILLER ERR: Something went wrong',
                            'message' => "Please try again later or reach out to our reps for help!",
                        ]);

                    return;

                }



                $responseCable = Http::withHeaders([
                    'api-key' => PaymentIntegration::first()->vtpass_api_key,
                    'secret-key' => PaymentIntegration::first()->vtpass_secret_key,
                    'Content-Type' => 'application/json'
                ])->post(trim($endpoint), $payload); 


                // $responseCable = Http::withHeaders([
                //     'api-key' => 'f40824cdb526d8d07bd1a4c7f54e2e9d',
                //     'secret-key' => 'SK_458a2566c1c70073766c67f20498830d3d868f6d2b4',
                //     'Content-Type' => 'application/json'
                // ])->post('https://sandbox.vtpass.com/api/pay', $payload);


                $responsePurchase = json_decode($responseCable->body(), true);

                    //dd($responsePurchase);
                if(isset($responsePurchase['content']['transactions']['status'])) {
                    


                    $content = $responsePurchase['content'];
                    $response_description = $responsePurchase['response_description'];
                    $requestId = $responsePurchase['requestId'];
                    $amount = $responsePurchase['amount'];
                    $purchased_code = $responsePurchase['purchased_code'];
                   
                   

                    // Extract variables from the 'content' array
                    $transactions = $content['transactions'];

                    $status = $transactions['status'];
                   
                    $product_name = $transactions['product_name'];

                    
                    
                    //". ucfirst($service_id) ." ".strtoupper($variation_code)." package to $decoder_number"

                    $successMessage = "You've successfully purchased ".strtoupper($product_name)." (".ucfirst($meter_type).") for <b>".$meter_number."</b> on ".date("l jS \of F Y h:i:s A").".";

                        if($status === 'delivered'){

                            DB::table('transactions')
                            ->where('reference_number', $requestId)->where('user_id',$userId)
                            ->update([
                                'status' => "successful",
                                'api_response' => $successMessage,
                                'token_pin' => $purchased_code,
                                'disco_name' => $product_name,
                                'note' => $successMessage,
                            ]);

                        
                            
                         //Pull out the notification....

                         TransactionPuller::create([
                            'user_id' => $this->userId,
                            'status' => 'success',
                            'transaction_key' => $this->requestId,
                            'title' => 'Successful',
                            'message' => $successMessage,
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
                        'message' => 'Something went wrong. Use a valid METER NUMBER. Please try again!',
                    ]);

                        }
                    
                }else{
                    //Transaction Failed.
                    //Refund the customer
                    //Update the transaction
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
                        'api_response' => "FAILED: ".$temporary_message,
                        'new_balance' => "$ngn".number_format($old_balance,2),
                        'charges' => $ngn."00.00",
                        'note' => "FAILED: ".$temporary_message
                    ]);

                    if($response_description === "BELOW MINIMUM AMOUNT ALLOWED"){
                    
                        
                    //Pull out the notification....

                    TransactionPuller::create([
                        'user_id' => $this->userId,
                        'status' => 'warning',
                        'transaction_key' => $this->requestId,
                        'title' => 'Too Low Entry',
                        'message' => 'Enter a minimum of '.$ngn.number_format(1100,2)." and try again.",
                    ]);

                    }else{
                         
                    //Pull out the notification....

                    TransactionPuller::create([
                        'user_id' => $this->userId,
                        'status' => 'error',
                        'transaction_key' => $this->requestId,
                        'title' => 'Error Occurred',
                        'message' => 'Something went wrong. Please try again!',
                    ]);
                    }

                    


                }

                
                



            }else{

            //Pull out the notification....

            TransactionPuller::create([
                'user_id' => $this->userId,
                'status' => 'error',
                'transaction_key' => $this->requestId,
                'title' => 'Error Occurred!',
                'message' => 'Something went wrong we are working on it quickly. Thanks!',
            ]);

            }


    }
}
