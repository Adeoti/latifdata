<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Transaction;
use App\Models\CableSubscription;
use App\Models\TransactionPuller;
use App\Models\PaymentIntegration;
use App\Models\TemporaryLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessCable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $requestId;
    protected $cableId;
    protected $amountToPay;
    protected $phoneNumber;
    protected $decoderNumber;
    protected $cableType;
    protected $cablePlan;
    protected $cableAmount;
    protected $cableCharges;
    protected $subscriptionType;
    protected $paymentIntegration;

    public function __construct($userId, $requestId, $cableId, $amountToPay, $phoneNumber, $decoderNumber, $cableType, $cablePlan, $cableAmount, $cableCharges, $subscriptionType)
    {
        $this->userId = $userId;
        $this->requestId = $requestId;
        $this->cableId = $cableId;
        $this->amountToPay = $amountToPay;
        $this->phoneNumber = $phoneNumber;
        $this->decoderNumber = $decoderNumber;
        $this->cableType = $cableType;
        $this->cablePlan = $cablePlan;
        $this->cableAmount = $cableAmount;
        $this->cableCharges = $cableCharges;
        $this->subscriptionType = $subscriptionType;
        $this->paymentIntegration = PaymentIntegration::first();
    }

    public function handle(): void
    {
        $this->buyCableFromSweetBill();
    }

    private function getMySweetBillBalance()
    {
        $response = Http::withHeaders([
            'email' => $this->paymentIntegration->sweetbill_email,
            'password' => $this->paymentIntegration->sweetbill_password,
            'api_key' => $this->paymentIntegration->sweetbill_api_key,
            'Content-Type' => 'application/json',
        ])->get('https://sweetbill.ng/api/v1/balance');

        if ($response->successful()) {
            $responseData = $response->json();
            return $responseData['balance'];
        } else {
            TransactionPuller::create([
                'user_id' => $this->userId,
                'status' => 'error',
                'transaction_key' => $this->requestId,
                'title' => 'Balance Error',
                'message' => "Unable to fetch balance from the Vendor.",
            ]);
            return 0;
        }
    }

    private function verifyDecoder($decoderNumber, $serviceId)
    {
        $response = Http::retry(5, 200)->timeout(60)->withHeaders([
            'email' => $this->paymentIntegration->sweetbill_email,
            'password' => $this->paymentIntegration->sweetbill_password,
            'api_key' => $this->paymentIntegration->sweetbill_api_key,
            'Content-Type' => 'application/json',
        ])->post('https://sweetbill.ng/api/v1/verify-decoder', [
            'decoder_number' => $decoderNumber,
            'decoder_type' => $serviceId,
        ]);

        if ($response->successful()) {
            $responseData = $response->json();
            TemporaryLogger::create([
                'title' => 'Cable Response',
                'body' => $responseData,
            ]);

            return $responseData['customer_name'] ?? null;
        } else {
            return null;
        }
    }

    private function updateTransactionStatus($status, $requestId, $userId, $message, $customerName = null, $balance = null)
    {
        $updateData = [
            'status' => $status,
            'api_response' => $message,
            'note' => $message,
        ];

        if ($customerName !== null) {
            $updateData['customer_name'] = $customerName;
        }

        if ($balance !== null) {
            $updateData['new_balance'] = $balance;
        }

        DB::table('transactions')
            ->where('reference_number', $requestId)
            ->where('user_id', $userId)
            ->update($updateData);
    }

    private function buyCableFromSweetBill()
    {
        $ngn = "₦";
        $user = User::find($this->userId);
        $mySweetBillBalance = $this->getMySweetBillBalance();

        if ($this->amountToPay > $mySweetBillBalance) {
            TransactionPuller::create([
                'user_id' => $this->userId,
                'status' => 'error',
                'transaction_key' => $this->requestId,
                'title' => 'Error Occurred',
                'message' => 'Something went wrong. We are working on it quickly. Thanks!',
            ]);
            return;
        }

        $oldBalance = $user->balance;
        $newBalance = (double)$oldBalance - (double)$this->amountToPay;
        $temporaryMessage = "Purchase of " . CableSubscription::find($this->cableId)->name . " package to $this->decoderNumber";
        $customerName = $this->verifyDecoder($this->decoderNumber, $this->cableType);

        if (!$customerName) {
            TransactionPuller::create([
                'user_id' => $this->userId,
                'status' => 'warning',
                'transaction_key' => $this->requestId,
                'title' => 'Invalid Decoder Number',
                'message' => "Kindly provide a valid Decoder Number and Try again!",
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            DB::table('users')
                ->where('id', $this->userId)
                ->update([
                    'balance' => $newBalance,
                ]);

            Transaction::create([
                'type' => 'cable',
                'user_id' => $this->userId,
                'api_response' => $temporaryMessage,
                'status' => 'pending',
                'note' => $temporaryMessage,
                'phone_number' => $this->phoneNumber,
                'amount' => "$ngn" . number_format($this->cableAmount, 2),
                'old_balance' => "$ngn" . number_format($oldBalance, 2),
                'new_balance' => "$ngn" . number_format($newBalance, 2),
                'reference_number' => $this->requestId,
                'plan_name' => CableSubscription::find($this->cableId)->name,
                'iuc_number' => $this->decoderNumber,
                'charges' => "$ngn" . number_format($this->cableCharges, 2),
            ]);

            $payload = [
                'phone' => $this->phoneNumber,
                'serviceID' => $this->cableType,
                'decoder_number' => $this->decoderNumber,
                'cable_id' => $this->cablePlan,
                'requestId' => $this->requestId,
            ];

            if (in_array($this->cableType, ['dstv', 'gotv'])) {
                $payload['sub_type'] = $this->subscriptionType;
            }

            $responseCable = Http::retry(5, 200)->timeout(60)->withHeaders([
                'email' => $this->paymentIntegration->sweetbill_email,
                'password' => $this->paymentIntegration->sweetbill_password,
                'api_key' => $this->paymentIntegration->sweetbill_api_key,
                'Content-Type' => 'application/json',
            ])->post('https://sweetbill.ng/api/v1/buy-cable', $payload);

            $responsePurchase = $responseCable->json();

            if (isset($responsePurchase['status'])) {
                $status = $responsePurchase['status'];
                $responseMessage = $responsePurchase['message'];
                $successMessage = "You've successfully purchased " . ucfirst(CableSubscription::find($this->cableId)->name) . " for <b>" . $this->decoderNumber . "</b> on " . date("l jS \of F Y h:i:s A") . ".";

                if ($status === 'success') {
                    $this->updateTransactionStatus("successful", $this->requestId, $this->userId, $successMessage, $customerName);
                    TransactionPuller::create([
                        'user_id' => $this->userId,
                        'status' => 'success',
                        'transaction_key' => $this->requestId,
                        'title' => 'Successful',
                        'message' => $successMessage . " = (" . CableSubscription::find($this->cableId)->name . ")",
                    ]);
                } else {
                    $this->updateTransactionStatus("failed", $this->requestId, $this->userId, "FAILED: " . $responseMessage, null, "$ngn" . number_format($oldBalance, 2));
                    TransactionPuller::create([
                        'user_id' => $this->userId,
                        'status' => 'error',
                        'transaction_key' => $this->requestId,
                        'title' => 'Error Occurred',
                        'message' => 'Something went wrong. Use a valid DECODER NUMBER. Please try again!',
                    ]);
                }
            } else {
                $this->updateTransactionStatus("failed", $this->requestId, $this->userId, "FAILED: " . $responsePurchase['message'], null, "$ngn" . number_format($oldBalance, 2));
                TransactionPuller::create([
                    'user_id' => $this->userId,
                    'status' => 'error',
                    'transaction_key' => $this->requestId,
                    'title' => 'Error Occurred',
                    'message' => 'Something went wrong. Please try again!',
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            TransactionPuller::create([
                'user_id' => $this->userId,
                'status' => 'error',
                'transaction_key' => $this->requestId,
                'title' => 'Error Occurred',
                'message' => 'Something went wrong. We are working on it quickly. Thanks!',
            ]);
        }
    }
    //Commented for git B
}
