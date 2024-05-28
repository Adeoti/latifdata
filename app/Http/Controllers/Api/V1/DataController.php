<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\MobileData;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class DataController extends Controller
{
    public function buyData(Request $request)
    {
        // Validate body
        $request->validate([
            'data_id' => 'required|integer',
            'phone' => 'required|string',
            'requestId' => 'required|string|min:10' 
        ]);

        // Extract headers
        $email = $request->header('email');

        // Extract body parameters
        $data_id = $request->input('data_id');
        $phone = $request->input('phone');
        $requestId = $request->input('requestId');

        // Validate requestId format (YYYYMMDDHHmmss followed by any alphanumeric or underscore)
        // if (!preg_match('/^\d{14}[a-zA-Z0-9_]*$/', $requestId)) {
        //     return response()->json(['error' => 'Invalid requestId format'], 400);
        // }

        // Get mobile data details from the database
        $mobileData = MobileData::find($data_id);
        if (!$mobileData) {
            return response()->json(['error' => 'Invalid Data ID'], 400);
        }

        // Get the user's details (including the balance and active_status)
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Check if requestId already exists
        if (Transaction::where('reference_number', $requestId)->exists()) {
            return response()->json([
                'status' => 'failed',
                'error' => 'Duplicate requestId. Transaction already exists.',
            ], 400);
        }

        // Check if user is not banned
        if ($user->user_status != true) {
            return response()->json([
                'status' => false,
                'error' => 'Your account has been blocked. Reach out to the SweetBill admin!',
            ], 400);
        }

        // Check if mobile data status is enabled
        if ($mobileData->active_status != true) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid network ID',
            ], 400);
        }

        // Determine the price and cashback based on the user's package
        $user_package = $user->package;
        $amount = 0;
        $cashback = 0;

        switch ($user_package) {
            case 'primary':
                $amount = $mobileData->primary_price;
                $cashback = $mobileData->primary_cashback;
                break;
            case 'agent':
                $amount = $mobileData->agent_price;
                $cashback = $mobileData->agent_cashback;
                break;
            case 'special':
                $amount = $mobileData->special_price;
                $cashback = $mobileData->special_cashback;
                break;
            case 'api':
                $amount = $mobileData->api_price;
                $cashback = $mobileData->api_cashback;
                break;
            default:
                return response()->json(['error' => 'Invalid user package'], 400);
        }

        // Check if the user has sufficient balance
        if ($user->balance < $amount) {
            return response()->json([
                'status' => 'failed',
                'error' => 'Insufficient Fund!',
            ], 400);
        }

        $vendor = $mobileData->vendor_name;

        switch ($vendor) {
            case 'vtpass':
                // Add code to call VTPass API
                break;
            case 'twins10':
            case 'datalight':
                return $this->buyDataFromVendor($user, $requestId, $data_id, $amount, $cashback, $phone, $vendor);
            default:
                return response()->json(['error' => 'Invalid vendor'], 400);
        }
    }

    private function buyDataFromVendor($user, $requestId, $data_id, $amount, $cashback, $phone_number, $vendor)
    {
        $auth_route = $credentials = "";
        switch ($vendor) {
            case 'twins10':
                $auth_route = "https://twins10.com/api/user";
                $credentials = 'Adeoti360:7DP75syvXML$$Ade#';
                break;
            case 'datalight':
                $auth_route = "https://datalight.ng/api/user";
                $credentials = 'SweetBill:7DP75syvXML$$Ade#';
                break;
        }

        // Authenticate with vendor API
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($credentials),
        ])->post($auth_route);

        $responseData = json_decode($response->body(), true);
        if ($responseData['status'] != 'success') {
            return response()->json([
                'status' => false,
                'error' => 'Authentication Error! Try again.',
            ], 400);
        }

        $accessToken = $responseData['AccessToken'];
        $vendorBalance = (float) str_replace(',', '', $responseData['balance']);
        if ($amount > $vendorBalance) {
            return response()->json([
                'status' => false,
                'error' => 'Something went wrong and we will fix it soon!',
            ], 400);
        }

        // Get data plan details
        $mobileData = MobileData::find($data_id);
        $network = $mobileData->api_code;
        $data_plan = $mobileData->service_id;
        $endpoint = $mobileData->endpoint;

        // Update user balance and cashback
        $user->balance -= $amount;
        $user->cashback_balance += $cashback;
        $user->save();

        // Record transaction
        $transaction = Transaction::create([
            'type' => 'data',
            'user_id' => $user->id,
            'api_response' => "Purchase of $mobileData->network $mobileData->plan_size Data to $phone_number",
            'status' => 'pending',
            'note' => "Purchase of $mobileData->network $mobileData->plan_size Data to $phone_number",
            'phone_number' => $phone_number,
            'amount' => "₦" . number_format($amount, 2),
            'old_balance' => "₦" . number_format($user->balance + $amount, 2),
            'new_balance' => "₦" . number_format($user->balance, 2),
            'cashback' => "₦" . number_format($cashback, 2),
            'reference_number' => $requestId,
            'plan_name' => $mobileData->plan_type,
            'network' => $mobileData->network,
        ]);

        // Call vendor API to purchase data
        $payload = [
            'network' => $network,
            'phone' => $phone_number,
            'data_plan' => $data_plan,
            'bypass' => true,
            'request-id' => $requestId,
        ];

        $purchaseResponse = Http::withHeaders([
            'Authorization' => "Token $accessToken",
            'Content-Type' => 'application/json'
        ])->post(trim($endpoint), $payload);

        $purchaseData = json_decode($purchaseResponse->body(), true);
        if ($purchaseData['status'] == 'success') {
            $transaction->update([
                'status' => 'successful',
                'api_response' => $purchaseData['message'],
                'note' => "You've successfully purchased {$mobileData->network} {$mobileData->plan_type} of {$mobileData->plan_size} Data to {$phone_number} on " . now()->format('l jS \of F Y h:i:s A') . "."
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Data Transaction Successful!',
            ], 200);
        } else {
            $user->balance += $amount;
            $user->cashback_balance -= $cashback;
            $user->save();

            $transaction->update([
                'status' => 'failed',
                'api_response' => $purchaseData['message'],
                'new_balance' => "₦" . number_format($user->balance, 2),
                'cashback' => "₦00.00",
                'note' => "Failed to purchase {$mobileData->network} {$mobileData->plan_type} of {$mobileData->plan_size} Data to {$phone_number} on " . now()->format('l jS \of F Y h:i:s A') . "."
            ]);
            return response()->json([
                'status' => 'failed',
                'error' => 'Something went wrong. Please try again!',
            ], 400);
        }
    }
}
