<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\MobileData;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use App\Models\TransactionPuller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessDataPurchase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */


    protected $userId;
    protected $requestId;
    protected $data_id;
    protected $amount;
    protected $cashback;
    protected $phone_number;
    protected $bypass;
    protected $vendor;

    public function __construct($userId,$requestId,$data_id,$amount,$cashback,$phone_number,$bypass,$vendor)
    {
        //


        $this->userId = $userId;
        $this->requestId = $requestId;
        $this->data_id = $data_id;
        $this->amount = $amount;
        $this->cashback = $cashback;
        $this->phone_number = $phone_number;
        $this->bypass = $bypass;
        $this->vendor = $vendor;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        switch($this->vendor){
            case 'twins10':
                $this->buyDataFromTwins10andCo($this->userId,$this->requestId,$this->data_id,$this->amount,$this->cashback,$this->phone_number,$this->bypass,$this->vendor);
            break;

            
            case 'datalight':
                $this->buyDataFromTwins10andCo($this->userId,$this->requestId,$this->data_id,$this->amount,$this->cashback,$this->phone_number,$this->bypass,$this->vendor);
            break;

            
        }
    }




    public function buyDataFromTwins10andCo($userId,$requestId,$data_id,$amount,$cashback,$phone_number,$bypass,$vendor){
       
        $auth_route = $pass_n_username = "";
        $user = User::find($userId);
 
             switch($vendor){
                 case 'twins10':
                     $auth_route = "https://twins10.com/api/user";
                     $pass_n_username = 'Adeoti360:7DP75syvXML$$Ade#';
                 break;
                 case 'datalight':
                     $auth_route = "https://datalight.ng/api/user";
                     $pass_n_username = 'SweetBill:7DP75syvXML$$Ade#';
                 break;
             }
        
         $response = Http::withHeaders([
             'Authorization' => 'Basic ' . base64_encode($pass_n_username),
         ])->post($auth_route);
         
 
 
 
         $ngn = "₦";
 
         
 
         $json = $response->body();
         
         $responseData = json_decode($json, true);
 
        
 
         $status = $responseData['status'];
 
             if($status != "success"){

                
                 //Pull out the notification....

                 TransactionPuller::create([
                    'user_id' => $userId,
                    'status' => 'error',
                    'transaction_key' => $requestId,
                    'title' => 'Error Occurred',
                    'message' => "Authentication Error! Try again.",
                ]);

                 return;
 
             }
 
 
 
             $accessToken = $responseData['AccessToken'];
             $username = $responseData['username'];
             $balance = (float) str_replace(',', '', $responseData['balance']);
            
            
 
                 //Check if my balance is capable of the job!
 
                 
                 if($amount > $balance){

                     //Pull out the notification....

                     TransactionPuller::create([
                        'user_id' => $userId,
                        'status' => 'error',
                        'transaction_key' => $requestId,
                        'title' => 'Error Occurred',
                        'message' => "Something went wrong and we will fix it soon!",
                    ]);

                    
                     return;
                 }
 
 
 
                 //Get Data Details....
 
                 $network = MobileData::find($data_id)->api_code;
                 $data_plan = MobileData::find($data_id)->service_id;
                 $endpoint = MobileData::find($data_id)->endpoint;
 
 
                 //$user_old_balance = auth()->user()->balance;
                 $old_balance = $user->balance;
                 $new_balance = $old_balance - $amount;
 
 
                 $old_balance_solid = $user->balance;
                 $new_balance_solid = $old_balance_solid - $amount;
 
 
                 $old_cashback = $user->cashback_balance;
                 $new_cashback = $old_cashback+$cashback;
 
 
                 $old_balance = number_format($old_balance,2);
                 $new_balance = number_format($new_balance,2);
                 $transactionStatus = "pending";
 
 
 
                 
 
 
                 //
                 // Debit user ahead and record Transaction....
                 //
                 //
                 
                 
                 $temporary_network = MobileData::find($data_id)->network;
                 $temporary_plan_size = MobileData::find($data_id)->plan_size;
                 $temporary_plan_type = MobileData::find($data_id)->plan_type;
                 $temporary_message = "Purchase of $temporary_network $temporary_plan_size Data to $phone_number";
 
                 DB::table('users')
                 ->where('id', $userId)
                 ->update([
                     'balance' => $new_balance_solid,
                     'cashback_balance' => $new_cashback
                 ]);
 
                 Transaction::create([
                     'type' => 'data',
                     'user_id' => $userId,
                     'api_response' => $temporary_message,
                     'status' => $transactionStatus,
                     'note' => $temporary_message,
                     'phone_number' => $phone_number,
                     'amount' => "$ngn".number_format($amount,2),
                     'old_balance' => "$ngn".$old_balance,
                     'new_balance' => "$ngn".$new_balance,
                     'cashback' => "$ngn".number_format($cashback,2),
                     'reference_number' => $requestId,
                     'plan_name' => $temporary_plan_type,
                     'network' => $temporary_network,
                 ]);
 
                     $payload = [
                         'network' => $network,
                         'phone' => $phone_number,
                         'data_plan' => $data_plan,
                         'bypass' => $bypass,
                         'request-id' => $requestId,
                     ];
 
                     $purchaseResponse = Http::withHeaders([
                         'Authorization' => "Token ".$accessToken."",
                         'Content-Type' => 'application/json'
                     ])->post(trim($endpoint), $payload);
 
                    // $purchaseResult = $purchaseResponse->json();
                     $responsePurchase = json_decode($purchaseResponse->body(), true);
 
 
                     $purchaseStatus = $responsePurchase['status'];
                     $message = $responsePurchase['message'];
 
 
                         //dd($purchaseResult);
 
                         if(isset($purchaseStatus)){
                             
                     if($purchaseStatus == 'success'){
 
                             //Update Transaction Record and don't update balance and cashback
                             //Flash success message
 
                             DB::table('transactions')
                             ->where('reference_number', $requestId)->where('user_id',$userId)
                             ->update([
                                 'status' => "successful",
                                 'api_response' => $message,
                                 'note' => "You've successfully sold ".strtoupper($temporary_network)." $temporary_plan_type of ".$temporary_plan_size." Data to ".$phone_number." on ".date("l jS \of F Y h:i:s A")."."
                             ]);
 
                         

                              //Pull out the notification....

                     TransactionPuller::create([
                        'user_id' => $userId,
                        'status' => 'success',
                        'transaction_key' => $requestId,
                        'title' => 'Successful',
                        'message' => "You've successfully sold ".strtoupper($temporary_network)." $temporary_plan_type of ".$temporary_plan_size." Data to ".$phone_number." on ".date("l jS \of F Y h:i:s A").".",
                    ]);


 
 
 
 
                     }else{
 
                         //Record Transaction
                         //Update Balance and Cashback to old data
                         //Flash Message
 
                         DB::table('users')
                         ->where('id', $userId)
                         ->update([
                             'balance' => $old_balance_solid,
                             'cashback_balance' => $old_cashback
                         ]);
 
                         DB::table('transactions')
                             ->where('reference_number', $requestId)->where('user_id',$userId)
                             ->update([
                                 'status' => "failed",
                                 'api_response' => $message,
                                 'new_balance' => "$ngn".$old_balance,
                                 'cashback' => $ngn."00.00",
                                 'note' => "Failed to sell ".strtoupper($temporary_network)." $temporary_plan_type of ".$temporary_plan_size." Data to ".$phone_number." on the ".date("l jS \of F Y h:i:s A")."."
                             ]);
 


                         //Pull out the notification....

                     TransactionPuller::create([
                        'user_id' => $userId,
                        'status' => 'error',
                        'transaction_key' => $requestId,
                        'title' => 'Error Occurred',
                        'message' => "Something went wrong. Please try again!",
                    ]);
                            
                       
 
                     }
                         }
 
                     
 
              
 
 
 
 
 
 
      }
 
}
