<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\CableSubscription;
use App\Models\PaymentIntegration;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class CableController extends Controller
{
    public function buyCable(Request $request)
    {
        // Validate request
        $request->validate([
            'cable_id' => 'required|integer', // 1
            'phone' => 'required|string',
            'decoder_number' => 'required|numeric', // 123456099
            'sub_type' => 'nullable|string', // change || renewal, optional
            'requestId' => 'required|string|min:10'
        ]);

        // Extract headers and body parameters
        $email = $request->header('email');
        $cable_id = $request->input('cable_id');
        $decoder_number = $request->input('decoder_number');
        $sub_type = $request->input('sub_type');
        $phone = $request->input('phone');
        $requestId = $request->input('requestId');

        // Check if requestId already exists
        if (Transaction::where('reference_number', $requestId)->exists()) {
            return response()->json([
                'status' => 'failed',
                'error' => 'Duplicate requestId. Transaction already exists.',
            ], 400);
        }

        // Get user details
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $userId = $user->id;

        // Get cable details from the database
        $cable = CableSubscription::find($cable_id);
        if (!$cable) {
            return response()->json(['error' => 'Invalid cable ID'], 400);
        }

        // Determine the charges based on user package
        $cable_charges = match ($user->package) {
            'primary' => $cable->primary_charges,
            'agent' => $cable->agent_charges,
            'special' => $cable->special_charges,
            'api' => $cable->api_charges,
            default => 0,
        };

        // Get the details of the cable
        $cable_price = $cable->price;
        $cable_vendor = $cable->vendor_name;
        $cable_serviceID = $cable->service_id;
        $cable_variation_code = $cable->api_code;

        // Calculate total amount to pay
        $amount_to_pay = (double)$cable_price + (double)$cable_charges;

        // Check if the user has sufficient balance
        if ($user->balance < $amount_to_pay) {
            return response()->json(['status' => 'failed', 'error' => 'Insufficient Fund!'], 400);
        }

        // Proceed to buy cable based on vendor
        if ($cable_vendor === 'vtpass') {
            return $this->buyCableFromVtPass($userId, $requestId, $cable_id, $amount_to_pay, $phone, $decoder_number, $cable_serviceID, $cable_variation_code, $cable_price, $cable_charges, $sub_type, $cable_vendor);
        }

        return response()->json(['error' => 'Unsupported vendor'], 400);
    }

    public function getMyVtPassBalance()
    {
        $response = Http::withHeaders([
            'api-key' => PaymentIntegration::first()->vtpass_api_key,
            'public-key' => PaymentIntegration::first()->vtpass_public_key,
            'Content-Type' => 'application/json',
        ])->get('https://api-service.vtpass.com/api/balance');

        if ($response->successful()) {
            return $response->json()['contents']['balance'];
        }

        return response()->json(['status' => 'failed', 'error' => 'Something went wrong. Try again or chat our reps!'], 400);
    }

    public function buyCableFromVtPass($userId, $requestId, $cable_id, $amount_to_pay, $phone_number, $decoder_number, $service_id, $variation_code, $cable_amount, $cable_charges, $subscription_type, $cable_vendor)
    {
        $ngn = "₦";

        $requestId .= "_CABLE";
        $user = User::find($userId);
        //$myVtPassBalance = 2000000; // Static balance for demonstration
        $myVtPassBalance = $this->getMyVtPassBalance(); 

        if ($myVtPassBalance > $amount_to_pay) {
            $old_balance = $user->balance;
            $new_balance = (double)$old_balance - (double)$amount_to_pay;
            $transactionStatus = "pending";
            $temporary_message = "Purchase of " . ucfirst($service_id) . " " . strtoupper($variation_code) . " package to $decoder_number";
            $endpoint = CableSubscription::find($cable_id)->endpoint;
            $cable_name = CableSubscription::find($cable_id)->name;
            $customerName = "";
            $requestAmount = 0;

            // Send a verify request to the VTPASS Endpoint...
            $response = Http::retry(5, 200)->timeout(60)->withHeaders([
                'api-key' => PaymentIntegration::first()->vtpass_api_key,
                'secret-key' => PaymentIntegration::first()->vtpass_secret_key,
                'Content-Type' => 'application/json'
            ])->post('https://api-service.vtpass.com/api/merchant-verify', [
                'billersCode' => $decoder_number,
                'serviceID' => $service_id,
            ]);

            // Check if the request was successful

            
            if ($response->successful()) {
                $slicedResponce = json_decode($response->body(), true);

                if (array_key_exists('Renewal_Amount', $slicedResponce['content'])) {
                    $requestAmount = $slicedResponce['content']['Renewal_Amount'];
                }
                if (array_key_exists('Customer_Name', $slicedResponce['content'])) {
                    $customerName = $slicedResponce['content']['Customer_Name'];
                } else {
                    return response()->json(['status' => 'failed', 'error' => 'Kindly provide a valid Decoder Number and Try again!'], 400);
                }

                DB::table('users')
                    ->where('id', $userId)
                    ->update(['balance' => $new_balance]);

                Transaction::create([
                    'type' => 'cable',
                    'user_id' => $userId,
                    'api_response' => $temporary_message,
                    'status' => $transactionStatus,
                    'note' => $temporary_message,
                    'phone_number' => $phone_number,
                    'amount' => "$ngn" . number_format($cable_amount, 2),
                    'old_balance' => "$ngn" . number_format($old_balance, 2),
                    'new_balance' => "$ngn" . number_format($new_balance, 2),
                    'reference_number' => $requestId,
                    'plan_name' => $cable_name,
                    'iuc_number' => $decoder_number,
                    'cable_plan' => strtoupper($variation_code),
                    'charges' => "$ngn" . number_format($cable_charges, 2)
                ]);

                $payload = [
                    'phone' => $phone_number,
                    'serviceID' => $service_id,
                    'billersCode' => $decoder_number,
                    'variation_code' => $variation_code,
                    'request_id' => $requestId,
                ];

                if ($service_id === 'dstv' || $service_id === 'gotv') {
                    $payload['subscription_type'] = $subscription_type;
                    $payload['amount'] = $requestAmount;
                }

                $responseCable = Http::retry(5, 200)->timeout(60)->withHeaders([
                    'api-key' => PaymentIntegration::first()->vtpass_api_key,
                    'secret-key' => PaymentIntegration::first()->vtpass_secret_key,
                    'Content-Type' => 'application/json'
                ])->post(trim($endpoint), $payload);

                $responsePurchase = json_decode($responseCable->body(), true);

                if (!isset($responsePurchase['content']['transactions']['status'])) {
                    return response()->json(['status' => 'failed', 'error' => 'API Initialization Error!: 187'], 400);
                }

                if (isset($responsePurchase['content']['transactions']['status'])) {
                    $status = $responsePurchase['content']['transactions']['status'];
                    $response_description = $responsePurchase['response_description'];

                    $successMessage = "You've successfully purchased " . ucfirst($service_id) . " " . strtoupper($variation_code) . " for <b>" . $decoder_number . "</b> on " . date("l jS \of F Y h:i:s A") . ".";

                    if ($status === 'delivered') {
                        DB::table('transactions')
                            ->where('reference_number', $requestId)->where('user_id', $userId)
                            ->update([
                                'status' => "successful",
                                'api_response' => $successMessage,
                                'note' => $successMessage,
                                'customer_name' => $customerName,
                            ]);

                        return response()->json(['status' => 'success', 'message' => 'Cable Transaction Successful'], 200);
                    } elseif ($status === 'failed') {
                        DB::table('users')
                            ->where('id', $userId)
                            ->update(['balance' => $old_balance]);

                        DB::table('transactions')
                            ->where('reference_number', $requestId)->where('user_id', $userId)
                            ->update([
                                'status' => "failed",
                                'api_response' => "FAILED: " . $response_description,
                                'new_balance' => "$ngn" . number_format($old_balance, 2),
                                'charges' => $ngn . "00.00",
                                'note' => "FAILED: " . $temporary_message
                            ]);

                        return response()->json(['status' => 'failed', 'message' => 'Something went wrong. Use a valid DECODER NUMBER. Please try again!'], 400);
                    }
                } else {
                    // Transaction Failed. Refund the customer and update the transaction
                    $response_description = $responsePurchase['response_description'];

                    DB::table('users')
                        ->where('id', $userId)
                        ->update(['balance' => $old_balance]);

                    DB::table('transactions')
                        ->where('reference_number', $requestId)->where('user_id', $userId)
                        ->update([
                            'status' => "failed",
                            'api_response' => "FAILED: " . $response_description,
                            'new_balance' => "$ngn" . number_format($old_balance, 2),
                            'charges' => $ngn . "00.00",
                            'note' => "FAILED: " . $temporary_message
                        ]);

                    return response()->json(['status' => 'failed', 'message' => 'Something went wrong. Please try again!'], 400);
                }
            } else {
                return response()->json(['status' => 'failed', 'error' => 'Please try again later or reach out to our reps for help!'], 400);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Something went wrong we are working on it quickly. Thanks!'], 400);
        }
    }
}
