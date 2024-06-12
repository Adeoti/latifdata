<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Transaction;
use App\Models\MyJobTracker;
use App\Models\MobileAirtime;
use Illuminate\Bus\Queueable;
use App\Models\TransactionPuller;
use App\Models\PaymentIntegration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessAirtimePurchase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $requestId;
    protected $airtimeId;
    protected $airtimeAmount;
    protected $totalAmount;
    protected $totalCashback;
    protected $phoneNumber;
    protected $validatePhoneNumber;
    protected $paymentIntegration;

    public function __construct($userId, $requestId, $airtimeId, $airtimeAmount, $totalAmount, $totalCashback, $phoneNumber, $validatePhoneNumber)
    {
        $this->userId = $userId;
        $this->requestId = $requestId;
        $this->airtimeId = $airtimeId;
        $this->airtimeAmount = $airtimeAmount;
        $this->totalAmount = $totalAmount;
        $this->totalCashback = $totalCashback;
        $this->phoneNumber = $phoneNumber;
        $this->validatePhoneNumber = $validatePhoneNumber;

        $this->paymentIntegration = PaymentIntegration::first();
    }

    public function handle(): void
    {
        $this->buyAirtimeFromSweetBill();
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

    public function buyAirtimeFromSweetBill()
    {
        $user = User::find($this->userId);
        $balance = $this->getMySweetBillBalance();

        if ($this->airtimeAmount > $balance) {
            TransactionPuller::create([
                'user_id' => $this->userId,
                'status' => 'warning',
                'transaction_key' => $this->requestId,
                'title' => 'Transaction Failed',
                'message' => 'Something went wrong and we will fix it soon',
            ]);
            return;
        }

        $mobileAirtime = MobileAirtime::find($this->airtimeId);
        $network = $mobileAirtime->api_code;
        $planType = $mobileAirtime->service_id;

        $oldBalance = $user->balance;
        $newBalance = $oldBalance - $this->totalAmount;

        $oldCashback = $user->cashback_balance;
        $newCashback = $oldCashback + $this->totalCashback;

        $ngn = "₦";

        $temporaryMessage = "Purchase of " . strtoupper($mobileAirtime->network) . " Airtime to {$this->phoneNumber}";

        DB::beginTransaction();
        try {
            DB::table('users')
                ->where('id', $this->userId)
                ->update([
                    'balance' => $newBalance,
                    'cashback_balance' => $newCashback,
                ]);

            Transaction::create([
                'type' => 'airtime',
                'user_id' => $this->userId,
                'api_response' => $temporaryMessage,
                'status' => 'pending',
                'note' => $temporaryMessage,
                'phone_number' => $this->phoneNumber,
                'amount' => "$ngn" . number_format($this->airtimeAmount, 2),
                'amount_paid' => "$ngn" . number_format($this->totalAmount, 2),
                'old_balance' => "$ngn" . number_format($oldBalance, 2),
                'new_balance' => "$ngn" . number_format($newBalance, 2),
                'cashback' => "$ngn" . number_format($this->totalCashback, 2),
                'reference_number' => $this->requestId,
                'plan_name' => $planType,
                'network' => strtoupper($mobileAirtime->network),
            ]);

            $payload = [
                'network' => $network,
                'phone' => $this->phoneNumber,
                'bypass' => $this->validatePhoneNumber,
                'amount' => $this->airtimeAmount,
                'requestId' => $this->requestId,
            ];

            $purchaseResponse = Http::withHeaders([
                'email' => $this->paymentIntegration->sweetbill_email,
                'password' => $this->paymentIntegration->sweetbill_password,
                'api_key' => $this->paymentIntegration->sweetbill_api_key,
                'Content-Type' => 'application/json',
            ])->post('https://sweetbill.ng/api/v1/buy-airtime', $payload);

            $responsePurchase = $purchaseResponse->json();

            if ($responsePurchase['status'] === 'success') {
                DB::table('transactions')
                    ->where('reference_number', $this->requestId)
                    ->where('user_id', $this->userId)
                    ->update([
                        'status' => 'successful',
                        'api_response' => $responsePurchase['message'],
                        'note' => "You've successfully purchased " . strtoupper($mobileAirtime->network) . " Airtime of $ngn" . number_format($this->airtimeAmount, 2) . " to {$this->phoneNumber} on " . date("l jS \of F Y h:i:s A") . ".",
                    ]);

                TransactionPuller::create([
                    'user_id' => $this->userId,
                    'status' => 'success',
                    'transaction_key' => $this->requestId,
                    'title' => 'Successful',
                    'message' => "You've successfully purchased " . strtoupper($mobileAirtime->network) . " Airtime of $ngn" . number_format($this->airtimeAmount, 2) . " to {$this->phoneNumber} on " . date("l jS \of F Y h:i:s A") . ".",
                ]);
            } else {
                DB::table('users')
                    ->where('id', $this->userId)
                    ->update([
                        'balance' => $oldBalance,
                        'cashback_balance' => $oldCashback,
                    ]);

                DB::table('transactions')
                    ->where('reference_number', $this->requestId)
                    ->where('user_id', $this->userId)
                    ->update([
                        'status' => 'failed',
                        'api_response' => $responsePurchase['message'],
                        'new_balance' => "$ngn" . number_format($oldBalance, 2),
                        'cashback' => "$ngn" . "0.00",
                        'amount_paid' => "$ngn" . "0.00",
                        'note' => "Failed to purchase " . strtoupper($mobileAirtime->network) . " Airtime to {$this->phoneNumber} on " . date("l jS \of F Y h:i:s A") . ".",
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
