<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Transaction;
use App\Models\MyJobTracker;
use App\Models\MobileAirtime;
use App\Models\TransactionPuller;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessAirtimePurchase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $userId;
    protected $requestId;
    protected $airtime_id;
    protected $airtime_amount;
    protected $total_amount;
    protected $total_cashback;
    protected $phone_number;
    protected $validate_phone_number;
    protected $airtime_vendor;

    public function __construct($userId,$requestId, $airtime_id, $airtime_amount, $total_amount, $total_cashback, $phone_number, $validate_phone_number, $airtime_vendor)
    {
        $this->userId = $userId;
        $this->requestId = $requestId;
        $this->airtime_id = $airtime_id;
        $this->airtime_amount = $airtime_amount;
        $this->total_amount = $total_amount;
        $this->total_cashback = $total_cashback;
        $this->phone_number = $phone_number;
        $this->validate_phone_number = $validate_phone_number;
        $this->airtime_vendor = $airtime_vendor;
    }
    
    

    /**
     * Execute the job.
     */


    public function callVTPass(){
        MyJobTracker::create([
            'user_id' => $this->userId,
            'status' => 'pending',
            'message' => "VTPass - "

        ]);
    }

  

    public function handle(): void
    {
        //......
        switch($this->airtime_vendor){
            case 'vtpass':
                 $this->callVTPass();
            break;

            case 'twins10':
                 $this->buyAirtimeFromTwins10andCo($this->userId,$this->requestId,$this->airtime_id,$this->airtime_amount,$this->total_amount,$this->total_cashback,$this->phone_number,$this->validate_phone_number,$this->airtime_vendor);
            break;

            case 'datalight':
                 $this->buyAirtimeFromTwins10andCo($this->userId,$this->requestId,$this->airtime_id,$this->airtime_amount,$this->total_amount,$this->total_cashback,$this->phone_number,$this->validate_phone_number,$this->airtime_vendor);
            break;
        }
        
    }




    public function buyAirtimeFromTwins10andCo($userId,$requestId,$airtime_id,$airtime_amount,$total_amount,$total_cashback,$phone_number,$validate_phone_number,$airtime_vendor){
       
        $auth_route = $pass_n_username = "";
        $user = User::find($userId);
 
             switch($airtime_vendor){
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
 
                //Craft Notification......

                         
                     //Pull out the notification....

                     TransactionPuller::create([
                        'user_id' => $userId,
                        'status' => 'error',
                        'transaction_key' => $requestId,
                        'title' => 'Error Occurred',
                        'message' => "Authentication Error! Try again.",
                    ]);

                //  $this->dispatch(
                //      'alert',
                //      type: 'error',
                //      title: 'Error Occurred',
                //      text: "Authentication Error! Try again.",
                //      button: 'Got it!'
                //  );

                 return;
 
             }
 
 
 
             $accessToken = $responseData['AccessToken'];
             $username = $responseData['username'];
             $balance = (float) str_replace(',', '', $responseData['balance']);
            
            
 
                 //Check if my balance is capable of the job!


                 
 
                 if($airtime_amount > $balance){

                    //Craft Notification......



                    //Pull out the notification....



                    TransactionPuller::create([
                        'user_id' => $userId,
                        'status' => 'warning',
                        'transaction_key' => $requestId,
                        'title' => 'Transaction Failed',
                        'message' => 'Something went wrong and we will fix it soon',
                    ]);

                    // Notification::make()
                    // ->title("Transaction Failed")
                    // ->body("Something went wrong and we will fix it soon")
                    // ->icon('heroicon-m-wallet')
                    // ->iconColor('warning')
                    // ->warning()
                    // ->sendToDatabase($user);

                    
                    // $this->dispatch(
                    //      'alert',
                    //      type:'warning',
                    //      title:'Transaction Failed',
                    //      text:'Something went wrong and we will fix it soon',
                    //      button: 'Got it!',
                    //  );
                     return;

                 }
 
 
 
                 //Get Airtime Details....
 
                 $network = MobileAirtime::find($airtime_id)->api_code;
                 $plan_type = MobileAirtime::find($airtime_id)->service_id;
                 $endpoint = MobileAirtime::find($airtime_id)->endpoint;
 
 
                // $user_old_balance = auth()->user()->balance;
                 $old_balance = $user->balance;
                 $new_balance = (double)$old_balance - (double)$total_amount;
 
 
                 $old_balance_solid =  $user->balance;
                 $new_balance_solid = (double)$old_balance_solid - (double)$total_amount;
 
 
                 $old_cashback = $user->cashback_balance;
                 $new_cashback = (double)$old_cashback + (double)$total_cashback;
 
 
                 $old_balance = number_format($old_balance,2);
                 $new_balance = number_format($new_balance,2);
                 $transactionStatus = "pending";
 
 
                 //
                 // Debit user ahead and record Transaction....
                 //
                 //
                 
                 
                 $temporary_network = MobileAirtime::find($airtime_id)->network;
                 $temporary_plan_type = MobileAirtime::find($airtime_id)->service_id;
                 $temporary_message = "Purchase of ".strtoupper($temporary_network)." Airtime to $phone_number";
 
                 DB::table('users')
                 ->where('id', $userId)
                 ->update([
                     'balance' => $new_balance_solid,
                     'cashback_balance' => $new_cashback
                 ]);
 
                 Transaction::create([
                     'type' => 'airtime',
                     'user_id' => $userId,
                     'api_response' => $temporary_message,
                     'status' => $transactionStatus,
                     'note' => $temporary_message,
                     'phone_number' => $phone_number,
                     'amount' => "$ngn".number_format($airtime_amount,2),
                     'amount_paid' => "$ngn".number_format($total_amount,2),
                     'old_balance' => "$ngn".$old_balance,
                     'new_balance' => "$ngn".$new_balance,
                     'cashback' => "$ngn".number_format($total_cashback,2),
                     'reference_number' => $requestId,
                     'plan_name' => $temporary_plan_type,
                     'network' => strtoupper($temporary_network),
                 ]);
 
                     $payload = [
                         'network' => $network,
                         'phone' => $phone_number,
                         'plan_type' => $plan_type,
                         'bypass' => $validate_phone_number,
                         'amount' => $airtime_amount,
                         'request-id' => $requestId,
                     ];
 
                     $purchaseResponse = Http::withHeaders([
                         'Authorization' => "Token ".$accessToken."",
                         'Content-Type' => 'application/json'
                     ])->post(trim($endpoint), $payload);
 
                     $responsePurchase = json_decode($purchaseResponse->body(), true);
 
                    // dd($responsePurchase);
 
                     $purchaseStatus = $responsePurchase['status'];
                     $message = $responsePurchase['message'];
 
 
 
                         if(isset($purchaseStatus)){
                             
                     if($purchaseStatus == 'success'){
 
                             //Update Transaction Record and don't update balance and cashback
                             //Flash success message
 
                             DB::table('transactions')
                             ->where('reference_number', $requestId)->where('user_id',$userId)
                             ->update([
                                 'status' => "successful",
                                 'api_response' => $message,
                                 'note' => "You've successfully sold ".strtoupper($temporary_network)." Airtime of $ngn".number_format($airtime_amount,2)." to ".$phone_number." on ".date("l jS \of F Y h:i:s A")."."
                             ]);
 
                         



                            //Craft Notification......

                            
                    //Pull out the notification....

                    TransactionPuller::create([
                        'user_id' => $userId,
                        'status' => 'success',
                        'transaction_key' => $requestId,
                        'title' => 'Successful',
                        'message' => "You've successfully sold ".strtoupper($temporary_network)." Airtime of $ngn".number_format($airtime_amount,2)." to ".$phone_number." on ".date("l jS \of F Y h:i:s A").".",
                    ]);

                            // Notification::make()
                            // ->title("Successful")
                            // ->body("You've successfully sold ".strtoupper($temporary_network)." Airtime of $ngn".number_format($airtime_amount,2)." to ".$phone_number." on ".date("l jS \of F Y h:i:s A").".")
                            // ->icon('heroicon-m-check-badge')
                            // ->iconColor('success')
                            // ->success()
                            // ->sendToDatabase($user);


                        //  $this->dispatch(
                        //          'alert',
                        //          type:'success',
                        //          title:'Successful',
                        //          text:"You've successfully sold ".strtoupper($temporary_network)." Airtime of $ngn".number_format($airtime_amount,2)." to ".$phone_number." on ".date("l jS \of F Y h:i:s A").".",
                        //          button:'Great!'
                        //      );
 
 
 
 
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
                                 'cashback' => "$ngn"."00.00",
                                 'amount_paid' => "$ngn"."00.00",
                                 'note' => "Failed to sell ".strtoupper($temporary_network)." $temporary_plan_type of Airtime to ".$phone_number." on the ".date("l jS \of F Y h:i:s A")."."
                             ]);



                        //Craft Notification......
                             
                     //Pull out the notification....

                     TransactionPuller::create([
                        'user_id' => $userId,
                        'status' => 'error',
                        'transaction_key' => $requestId,
                        'title' => 'Error Occurred',
                        'message' => "Something went wrong. Please try again!",
                    ]);
                        //  $this->dispatch(
                        //      'alert',
                        //      type:'error',
                        //      title:'Error Occurred',
                        //      text:'Something went wrong. Please try again!',
                        //      button: 'Got it!'
 
                        //  );
 
                     }
                         }
 
                     
 
              
 
 
 
 
 
 
      }



}
