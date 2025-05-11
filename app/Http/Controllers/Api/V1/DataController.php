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
            default:
                return response()->json(['error' => 'Invalid vendor'], 400);
        }
    }

    
}
