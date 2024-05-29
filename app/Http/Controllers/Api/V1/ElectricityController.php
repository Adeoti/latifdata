<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentIntegration;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\ElectricityIntegration;

class ElectricityController extends Controller
{
    public function buyElectricity(Request $request)
    {
        // Validate request
        $request->validate([
            'meter_number' => 'required|integer',
            'phone' => 'required|string',
            'meter_type' => 'required|string',
            'disco_type' => 'required|string',
            'amount' => 'required|numeric',
            'requestId' => 'required|string|min:10'
        ]);

        // Extract headers and body parameters
        $email = $request->header('email');
        $disco_type = $request->input('disco_type');
        $meter_type = $request->input('meter_type');
        $meter_number = $request->input('meter_number');
        $phone = $request->input('phone');
        $p_amount = $request->input('amount');
        $requestId = $request->input('requestId');

        // Get active electricity integration
        $active_electricity = ElectricityIntegration::where('active_status', true)->first();

        if (!$active_electricity) {
            return response()->json(['error' => 'Invalid electricity ID'], 400);
        }

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

        // Determine the charges based on user package
        $electricity_charges = match ($user->package) {
            'primary' => $active_electricity->primary_charges,
            'agent' => $active_electricity->agent_charges,
            'special' => $active_electricity->special_charges,
            'api' => $active_electricity->api_charges,
            default => 0,
        };

        // Calculate total amount to pay
        $amount_to_pay = (double)$p_amount + (double)$electricity_charges;

        // Check if the user has sufficient balance
        if ($user->balance < $amount_to_pay) {
            return response()->json(['status' => 'failed', 'error' => 'Insufficient Fund!'], 400);
        }

        // Proceed to buy electricity based on vendor
        if ($active_electricity->vendor_name === 'vtpass') {
            return $this->buyElectricityFromVtPass($user, $requestId, $amount_to_pay, $phone, $meter_number, $disco_type, $meter_type, $p_amount, $electricity_charges);
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

    public function buyElectricityFromVtPass($user, $requestId, $amount_to_pay, $phone, $meter_number, $disco_type, $meter_type, $product_amount, $electricity_charges)
    {
        $ngn = "₦";
        $requestId .= "_ELECTRICITY";
        $myVtPassBalance = $this->getMyVtPassBalance(); //switch_later
       // $myVtPassBalance = 2000000; 



        if ($myVtPassBalance < $amount_to_pay) {
            return response()->json(['status' => 'failed', 'error' => 'Something went wrong we are working on it quickly. Thanks!'], 400);
        }

        // Verify meter number
        $verifyResponse = Http::withHeaders([
            'api-key' => PaymentIntegration::first()->vtpass_api_key,
            'secret-key' => PaymentIntegration::first()->vtpass_secret_key,
            'Content-Type' => 'application/json'
        ])->post('https://api-service.vtpass.com/api/merchant-verify', [
            'billersCode' => $meter_number,
            'serviceID' => $disco_type,
            'type' => $meter_type,
        ]); //switch_later

        // Verify meter number
        // $verifyResponse = Http::withHeaders([
        //     'api-key' => "f40824cdb526d8d07bd1a4c7f54e2e9d",
        //     'secret-key' => "SK_458a2566c1c70073766c67f20498830d3d868f6d2b4",
        //     'Content-Type' => 'application/json'
        // ])->post('https://sandbox.vtpass.com/api/merchant-verify', [
        //     'billersCode' => $meter_number,
        //     'serviceID' => $disco_type,
        //     'type' => $meter_type,
        // ]);


        if (!$verifyResponse->successful() || !array_key_exists('Customer_Name', $verifyResponse->json()['content'])) {
            return response()->json(['status' => 'failed', 'error' => 'Kindly provide a valid Meter Number and Try again!'], 400);
        }

        $customerName = $verifyResponse->json()['content']['Customer_Name'];
        $customerAddress = $verifyResponse->json()['content']['Address'];

        // Update user balance
        $old_balance = $user->balance;
        $new_balance = (double)$old_balance - (double)$amount_to_pay;
        $user->update(['balance' => $new_balance]);

        // Log transaction
        $transaction = Transaction::create([
            'type' => 'electricity',
            'user_id' => $user->id,
            'api_response' => "Electricity subscription of ". ucfirst($disco_type) ." ".strtoupper($meter_type)." to $meter_number",
            'status' => 'pending',
            'note' => "Electricity subscription of ". ucfirst($disco_type) ." ".strtoupper($meter_type)." to $meter_number",
            'phone_number' => $phone,
            'amount' => "$ngn" . number_format($product_amount, 2),
            'old_balance' => "$ngn" . number_format($old_balance, 2),
            'new_balance' => "$ngn" . number_format($new_balance, 2),
            'reference_number' => $requestId,
            'meter_type' => $meter_type,
            'meter_number' => $meter_number,
            'customer_name' => $customerName,
            'customer_address' => $customerAddress,
            'disco_name' => ucfirst($disco_type) . "ity",
            'charges' => "$ngn" . number_format($electricity_charges, 2)
        ]);

        // Purchase electricity
        $purchaseResponse = Http::withHeaders([
            'api-key' => PaymentIntegration::first()->vtpass_api_key,
            'secret-key' => PaymentIntegration::first()->vtpass_secret_key,
            'Content-Type' => 'application/json'
        ])->post('https://api-service.vtpass.com/api/pay', [
            'phone' => $phone,
            'serviceID' => $disco_type,
            'billersCode' => $meter_number,
            'variation_code' => $meter_type,
            'request_id' => $requestId,
            'amount' => $product_amount,
        ]); //switch_later

        // // Purchase electricity
        // $purchaseResponse = Http::withHeaders([
        //     'api-key' => 'f40824cdb526d8d07bd1a4c7f54e2e9d',
        //     'secret-key' => 'SK_458a2566c1c70073766c67f20498830d3d868f6d2b4',
        //     'Content-Type' => 'application/json'
        // ])->post('https://sandbox.vtpass.com/api/pay', [
        //     'phone' => $phone,
        //     'serviceID' => $disco_type,
        //     'billersCode' => $meter_number,
        //     'variation_code' => $meter_type,
        //     'request_id' => $requestId,
        //     'amount' => $product_amount,
        // ]);

        

        if(!isset($purchaseResponse->json()['content']['transactions']['status'])){
            return response()->json(['status' => 'failed', 'error' => 'Invalid requestId!'], 400);
        }

        if ($purchaseResponse->successful() && $purchaseResponse->json()['content']['transactions']['status'] === 'delivered') {
            $content = $purchaseResponse->json()['content'];
            $transaction->update([
                'status' => 'successful',
                'api_response' => "You've successfully purchased ".strtoupper($content['transactions']['product_name'])." (".ucfirst($meter_type).") for <b>".$meter_number."</b> on ".date("l jS \of F Y h:i:s A").".",
                'token_pin' => $purchaseResponse->json()['purchased_code'],
                'disco_name' => $content['transactions']['product_name'],
                'note' => "You've successfully purchased ".strtoupper($content['transactions']['product_name'])." (".ucfirst($meter_type).") for <b>".$meter_number."</b> on ".date("l jS \of F Y h:i:s A")."."
            ]);

            return response()->json(['status' => 'success', 'message' => 'Electricity Transaction Successful!'], 200);
        } else {
            $transaction->update([
                'status' => 'failed',
                'api_response' => "FAILED: Electricity subscription of ". ucfirst($disco_type) ." ".strtoupper($meter_type)." to $meter_number",
                'new_balance' => "$ngn" . number_format($old_balance, 2),
                'charges' => $ngn . "00.00",
                'note' => "FAILED: Electricity subscription of ". ucfirst($disco_type) ." ".strtoupper($meter_type)." to $meter_number"
            ]);
            $user->update(['balance' => $old_balance]);

            $error_message = $purchaseResponse->json()['response_description'] ?? 'Something went wrong. Please try again!';
            return response()->json(['status' => 'failed', 'error' => $error_message], 400);
        }
    }
}
