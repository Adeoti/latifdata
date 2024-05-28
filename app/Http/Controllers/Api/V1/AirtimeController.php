<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\MobileAirtime;
use App\Models\Transaction;
use App\Models\TransactionPuller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AirtimeController extends Controller
{
    public function buyAirtime(Request $request)
    {
        // Validate body
        $request->validate([
            'network' => 'required|integer',
            'phone' => 'required|string',
            'bypass' => 'required|boolean',
            'amount' => 'required|numeric',
            'requestId' => 'required|string|min:10'
        ]);

        // Extract headers
        $email = $request->header('email');

        // Extract body parameters
        $networkId = $request->input('network');
        $phone = $request->input('phone');
        $bypass = $request->input('bypass');
        $p_amount = $request->input('amount');
        $requestId = $request->input('requestId');

        // Get airtime details from the database
        $airtime = MobileAirtime::find($networkId);
        if (!$airtime) {
            return response()->json(['error' => 'Invalid network ID'], 400);
        }
        $network = $airtime->api_code;
        $vendor = $airtime->vendor_name;
        $airtime_network = $airtime->network;

        // Get the user's details (including the balance and active_status)
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $userId = $user->id;
        $userBalance = $user->balance;

        $minimum_amount = $airtime->minimum_amount;
        $maximum_amount = $airtime->maximum_amount;

        if ($p_amount < $minimum_amount) {
            return response()->json([
                'status' => 'failed',
                'error' => 'Too Low Entry',
            ], 400);
        }

        if ($p_amount > $maximum_amount) {
            return response()->json([
                'status' => 'failed',
                'error' => 'Too High Entry',
            ], 400);
        }


          // Check if requestId already exists
          if (Transaction::where('reference_number', $requestId)->exists()) {
            return response()->json([
                'status' => 'failed',
                'error' => 'Duplicate requestId. Transaction already exists.',
            ], 400);
        }


         // Validate requestId format (YYYYMMDDHHmmss followed by any alphanumeric or underscore)
        //  if (!preg_match('/^\d{14}[a-zA-Z0-9_]*$/', $requestId)) {
        //     return response()->json(['error' => 'Invalid requestId format'], 400);
        // }

        // Check if user is not banned
        if ($user->user_status != true) {
            return response()->json([
                'status' => false,
                'error' => 'Your account has been blocked. Reach out to the SweetBill admin!',
            ], 400);
        }

        // Check if airtime status is enabled
        if ($airtime->active_status != true) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid network ID',
            ], 400);
        }

        $user_package = $user->package;
        $cashback = 0;
        $amount = 0;

        switch ($user_package) {
            case 'primary':
                $amount = $airtime->primary_price;
                $cashback = $airtime->primary_cashback;
                break;

            case 'agent':
                $amount = $airtime->agent_price;
                $cashback = $airtime->agent_cashback;
                break;

            case 'special':
                $amount = $airtime->special_price;
                $cashback = $airtime->special_cashback;
                break;

            case 'api':
                $amount = $airtime->api_price;
                $cashback = $airtime->api_cashback;
                break;
        }

        $percentage = (double)$amount / 100;
        $total_amount = (double)$p_amount * (double)$percentage;

        $cashback_percentage = $cashback / 100;
        $total_cashback = $total_amount * $cashback_percentage;

        // Check if the user has the balance to proceed
        if ($userBalance < $total_amount) {
            return response()->json([
                'status' => 'failed',
                'error' => 'Insufficient Fund!',
            ], 400);
        }

        switch ($vendor) {
            case 'vtpass':
                // $this->callVTPass();
                break;

            case 'twins10':
                return $this->buyAirtimeFromTwins10andCo($userId, $requestId, $networkId, $p_amount, $total_amount, $total_cashback, $phone, $bypass, $vendor);

            case 'datalight':
                return $this->buyAirtimeFromTwins10andCo($userId, $requestId, $networkId, $p_amount, $total_amount, $total_cashback, $phone, $bypass, $vendor);
        }
    }

    public function buyAirtimeFromTwins10andCo($userId, $requestId, $airtime_id, $airtime_amount, $total_amount, $total_cashback, $phone_number, $validate_phone_number, $airtime_vendor)
    {
        $auth_route = "";
        $pass_n_username = "";
        $user = User::find($userId);

        switch ($airtime_vendor) {
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

        $json = $response->body();
        $responseData = json_decode($json, true);

        if (!$responseData || $responseData['status'] != "success") {
            return response()->json([
                'status' => 'failed',
                'error' => 'Authentication Error! Try again.',
            ], 400);
        }

        $accessToken = $responseData['AccessToken'];
        $balance = (float) str_replace(',', '', $responseData['balance']);

        if ($airtime_amount > $balance) {
            // Craft Notification...
            // Pull out the notification...
            return response()->json([
                'status' => 'failed',
                'error' => 'Something went wrong and we will fix it soon',
            ], 400);
        }

        // Get Airtime Details....
        $airtime = MobileAirtime::find($airtime_id);
        $network = $airtime->api_code;
        $plan_type = $airtime->service_id;
        $endpoint = $airtime->endpoint;

        $old_balance = $user->balance;
        $new_balance = (double)$old_balance - (double)$total_amount;
        $old_cashback = $user->cashback_balance;
        $new_cashback = (double)$old_cashback + (double)$total_cashback;

        DB::table('users')->where('id', $userId)->update([
            'balance' => $new_balance,
            'cashback_balance' => $new_cashback
        ]);

        $transactionStatus = "pending";
        $temporary_message = "Purchase of " . strtoupper($airtime->network) . " Airtime to $phone_number";

        Transaction::create([
            'type' => 'airtime',
            'user_id' => $userId,
            'api_response' => $temporary_message,
            'status' => $transactionStatus,
            'note' => $temporary_message,
            'phone_number' => $phone_number,
            'amount' => "₦" . number_format($airtime_amount, 2),
            'amount_paid' => "₦" . number_format($total_amount, 2),
            'old_balance' => "₦" . number_format($old_balance, 2),
            'new_balance' => "₦" . number_format($new_balance, 2),
            'cashback' => "₦" . number_format($total_cashback, 2),
            'reference_number' => $requestId,
            'plan_name' => $plan_type,
            'network' => strtoupper($airtime->network),
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
            'Authorization' => "Token " . $accessToken,
            'Content-Type' => 'application/json'
        ])->post(trim($endpoint), $payload);

        $responsePurchase = json_decode($purchaseResponse->body(), true);

        if (!isset($responsePurchase['status'])) {
            return response()->json([
                'status' => 'failed',
                'error' => 'Invalid purchase response',
            ], 400);
        }

        $purchaseStatus = $responsePurchase['status'];
        $message = $responsePurchase['message'];

        if ($purchaseStatus == 'success') {
            DB::table('transactions')->where('reference_number', $requestId)->where('user_id', $userId)->update([
                'status' => "successful",
                'api_response' => $message,
                'note' => "You've successfully sold " . strtoupper($airtime->network) . " Airtime of ₦" . number_format($airtime_amount, 2) . " to " . $phone_number . " on " . date("l jS \of F Y h:i:s A") . "."
            ]);

            // Craft Notification...
            return response()->json([
                'status' => 'success',
                'message' => "Airtime transaction successful!",
            ], 200);
        } else {
            DB::table('users')->where('id', $userId)->update([
                'balance' => $old_balance,
                'cashback_balance' => $old_cashback
            ]);

            DB::table('transactions')->where('reference_number', $requestId)->where('user_id', $userId)->update([
                'status' => "failed",
                'api_response' => $message,
                'new_balance' => "₦" . number_format($old_balance, 2),
                'cashback' => "₦" . "00.00",
                'amount_paid' => "₦" . "00.00",
                'note' => "Failed to sell " . strtoupper($airtime->network) . " $plan_type of Airtime to " . $phone_number . " on the " . date("l jS \of F Y h:i:s A") . "."
            ]);

            // Craft Notification...
            return response()->json([
                'status' => 'failed',
                'error' => 'Something went wrong. Please try again!',
            ], 400);
        }
    }
}
