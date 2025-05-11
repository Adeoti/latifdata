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
                
        }
    }

    
}
