<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use App\Models\PaymentIntegration;


class MeterVerificationController extends Controller
{
    public function verifyMeter(Request $request)
    {

        // Validate body
        $request->validate([
            'meter_number' => 'required|string',
            'meter_type' => 'required|string|in:prepaid,postpaid',
            'service_id' => 'required|string'
        ]);

      
        // Extract body parameters
        $meter_number = $request->input('meter_number');
        $meter_type = $request->input('meter_type');
        $service_id = $request->input('service_id');

        // Send a verify request to the VTPASS Endpoint
        $response = Http::withHeaders([
            'api-key' => PaymentIntegration::first()->vtpass_api_key,
            'secret-key' => PaymentIntegration::first()->vtpass_secret_key,
            'Content-Type' => 'application/json'
        ])->post('https://api-service.vtpass.com/api/merchant-verify', [
            'billersCode' => $meter_number,
            'serviceID' => $service_id,
            'type' => $meter_type,
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            $responseData = $response->json();
            $slicedResponse = json_decode($response->body(), true);

            if (array_key_exists('Customer_Name', $slicedResponse['content'])) {
                $customerName = $slicedResponse['content']['Customer_Name'];
                $customerAddress = $slicedResponse['content']['Address'];

                return response()->json([
                    'customer_name' => $customerName,
                    'customer_address' => $customerAddress
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Invalid Meter Number. Kindly provide a valid Meter Number and try again!'
                ], 400);
            }
        } else {
            return response()->json([
                'error' => 'Something went wrong. Please try again later or reach out to our reps for help.'
            ], 500);
        }
    }
}
