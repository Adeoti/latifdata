<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\MobileData;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use App\Models\TransactionPuller;
use App\Models\PaymentIntegration;
use App\Models\TemporaryLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessDataPurchase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $requestId;
    protected $dataId;
    protected $amount;
    protected $cashback;
    protected $phoneNumber;
    protected $bypass;
    protected $paymentIntegration;

    public function __construct($userId, $requestId, $dataId, $amount, $cashback, $phoneNumber, $bypass)
    {
        $this->userId = $userId;
        $this->requestId = $requestId;
        $this->dataId = $dataId;
        $this->amount = $amount;
        $this->cashback = $cashback;
        $this->phoneNumber = $phoneNumber;
        $this->bypass = $bypass;

        $this->paymentIntegration = PaymentIntegration::first();
    }

    public function handle(): void
    {
        $this->buyDataFromSweetBill();
    }

    public function getMySweetBillBalance()
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

    public function buyDataFromSweetBill()
    {
        $user = User::find($this->userId);
        $balance = $this->getMySweetBillBalance();

        if ($this->amount > $balance) {
            TransactionPuller::create([
                'user_id' => $this->userId,
                'status' => 'error',
                'transaction_key' => $this->requestId,
                'title' => 'Error Occurred',
                'message' => "Insufficient balance.",
            ]);
            return;
        }

        $mobileData = MobileData::find($this->dataId);
        $network = $mobileData->api_code;
        $oldBalance = $user->balance;
        $newBalance = $oldBalance - $this->amount;
        $newCashback = $user->cashback_balance + $this->cashback;

        DB::beginTransaction();
        try {
            DB::table('users')
                ->where('id', $this->userId)
                ->update([
                    'balance' => $newBalance,
                    'cashback_balance' => $newCashback
                ]);

            $temporaryMessage = "Purchase of {$mobileData->network} {$mobileData->plan_size} Data to {$this->phoneNumber}";

            Transaction::create([
                'type' => 'data',
                'user_id' => $this->userId,
                'api_response' => $temporaryMessage,
                'status' => 'pending',
                'note' => $temporaryMessage,
                'phone_number' => $this->phoneNumber,
                'amount' => "₦" . number_format($this->amount, 2),
                'old_balance' => "₦" . number_format($oldBalance, 2),
                'new_balance' => "₦" . number_format($newBalance, 2),
                'cashback' => "₦" . number_format($this->cashback, 2),
                'reference_number' => $this->requestId,
                'plan_name' => $mobileData->plan_type,
                'network' => $mobileData->network,
            ]);

            $payload = [
                'data_id' => $network,
                'phone' => $this->phoneNumber,
                'requestId' => $this->requestId,
            ];

            $purchaseResponse = Http::withHeaders([
                'email' => $this->paymentIntegration->sweetbill_email,
                'password' => $this->paymentIntegration->sweetbill_password,
                'api_key' => $this->paymentIntegration->sweetbill_api_key,
                'Content-Type' => 'application/json',
            ])->post('https://sweetbill.ng/api/v1/buy-data', $payload);

            $responsePurchase = $purchaseResponse->json();

            

            $purchaseStatus = $responsePurchase['status'];
            $message = $responsePurchase['message'];

            if ($purchaseStatus == 'success') {
                DB::table('transactions')
                    ->where('reference_number', $this->requestId)
                    ->where('user_id', $this->userId)
                    ->update([
                        'status' => "successful",
                        'api_response' => $message,
                        'note' => "You've successfully purchased {$mobileData->network} {$mobileData->plan_size} Data to {$this->phoneNumber} on " . date("l jS \of F Y h:i:s A") . "."
                    ]);

                TransactionPuller::create([
                    'user_id' => $this->userId,
                    'status' => 'success',
                    'transaction_key' => $this->requestId,
                    'title' => 'Successful',
                    'message' => "You've successfully purchased {$mobileData->network} {$mobileData->plan_size} Data to {$this->phoneNumber} on " . date("l jS \of F Y h:i:s A") . ".",
                ]);
            } else {
                DB::table('users')
                    ->where('id', $this->userId)
                    ->update([
                        'balance' => $oldBalance,
                        'cashback_balance' => $user->cashback_balance,
                    ]);

                DB::table('transactions')
                    ->where('reference_number', $this->requestId)
                    ->where('user_id', $this->userId)
                    ->update([
                        'status' => "failed",
                        'api_response' => $message,
                        'new_balance' => "₦" . number_format($oldBalance, 2),
                        'cashback' => "₦0.00",
                        'note' => "Failed to purchase {$mobileData->network} {$mobileData->plan_size} Data to {$this->phoneNumber} on " . date("l jS \of F Y h:i:s A") . "."
                    ]);

                TransactionPuller::create([
                    'user_id' => $this->userId,
                    'status' => 'error',
                    'transaction_key' => $this->requestId,
                    'title' => 'Error Occurred',
                    'message' => "Something went wrong. Please try again!",
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle exception
            TransactionPuller::create([
                'user_id' => $this->userId,
                'status' => 'error',
                'transaction_key' => $this->requestId,
                'title' => 'Error Occurred',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
