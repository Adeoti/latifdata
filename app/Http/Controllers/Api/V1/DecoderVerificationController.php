<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\PaymentIntegration;

class DecoderVerificationController extends Controller
{
    public function verifyDecoder(Request $request)
    {
        // Validate request headers
        $request->validate([
            'decoder_number' => 'required|string',
            'decoder_type' => 'required|string|in:dstv,gotv,startime',
        ]);

        $decoder_number = $request->header('decoder_number');
        $decoder_type = $request->header('decoder_type');

        // Send a verify request to the VTPASS Endpoint...
        $response = Http::withHeaders([
            'api-key' => PaymentIntegration::first()->vtpass_api_key,
            'secret-key' => PaymentIntegration::first()->vtpass_secret_key,
            'Content-Type' => 'application/json'
        ])->post('https://api-service.vtpass.com/api/merchant-verify', [
            'billersCode' => $decoder_number,
            'serviceID' => $decoder_type,
        ]);

        // Check if the request was successful
        return $response;
        if ($response->successful()) {
            $slicedResponse = json_decode($response->body(), true);

            if (array_key_exists('Customer_Name', $slicedResponse['content'])) {
                $customerName = $slicedResponse['content']['Customer_Name'];
                $customerAddress = $slicedResponse['content']['Customer_Address'] ?? 'N/A'; // Assuming Customer_Address might be present

                return response()->json([
                    'customer_name' => $customerName,
                    'customer_address' => $customerAddress,
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Invalid Decoder Number. Kindly provide a valid Decoder Number and try again!',
                ], 400);
            }
        } else {
            return response()->json([
                'error' => 'Something went wrong. Please try again later or reach out to our reps for help.',
            ], 500);
        }
    }
}
