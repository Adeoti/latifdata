<?php

namespace App\Livewire;

use Exception;
use App\Models\User;
use Livewire\Component;
use Filament\Forms\Form;
use App\Models\Transaction;
use App\Models\SiteSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use App\Mail\SweetBillNotificationEmail;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;

class ShareWallet extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public $ngn = "₦";

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        $user_balance = number_format(User::find(auth()->id())->balance, 2);

        return $form
            ->schema([
                Section::make("👉 Your balance is: $this->ngn" . $user_balance)
                    ->description("👉 You will be charged $this->ngn" . SiteSettings::first()->wallet_to_charges . " for this operation")
                    ->schema([
                        TextInput::make('email')
                            ->required()
                            ->columnSpan(1)
                            ->prefix('@')
                            ->placeholder('Beneficiary\'s email'),
                        TextInput::make('amount')
                            ->required()
                            ->placeholder('Amount to share')
                            ->numeric()
                            ->prefix('NGN'),
                        TextInput::make('transaction_pin')
                            ->required()
                            ->prefix('PIN')
                            ->password()
                            ->revealable()
                            ->placeholder('Transaction Pin'),
                        RichEditor::make('note')
                            ->toolbarButtons([
                                'bold', 'italic', 'link', 'redo', 'strike', 'underline', 'undo',
                            ])
                            ->columnSpanFull()
                    ])->columns(3)
            ])
            ->statePath('data');
    }

    public function sendEmail($toEmail, $subject, $email_message, $emailRecipient)
    {
        try {
            Mail::to($toEmail)->send(new SweetBillNotificationEmail($subject, $email_message, $emailRecipient));
        } catch (Exception $e) {
            Log::error('Unable to send email ' . $e->getMessage());
        }
    }

    public function shareWallet(): void
    {
        $user_email = $this->form->getState()['email'];
        $amount = $this->form->getState()['amount'];
        $note = $this->form->getState()['note'];
        $transaction_pin = $this->form->getState()['transaction_pin'];
        $transfer_charges = SiteSettings::first()->wallet_to_charges;

        // Check if the recipient is actually a user
        $recipient = User::where('email', $user_email)->first();
        $sender = User::find(auth()->id());

        if (!$recipient || !$sender) {
            $this->dispatch(
                'alert',
                type: 'error',
                title: 'Invalid Operation',
                text: 'You entered an invalid Email Address',
                button: 'Got it!'
            );
            return;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            $this->dispatch(
                'alert',
                type: 'error',
                title: 'Invalid Operation',
                text: 'Enter a valid amount!',
                button: 'Got it!'
            );
            return;
        }

        if ($transaction_pin != $sender->transaction_pin) {
            $this->dispatch(
                'alert',
                type: 'error',
                title: 'Incorrect Pin',
                text: 'Enter your correct 4-digit pin!',
                button: 'Got it!'
            );
            return;
        }

        if ($recipient->id == $sender->id) {
            $this->dispatch(
                'alert',
                type: 'error',
                title: 'Invalid Operation',
                text: "You can't share funds with yourself",
                button: 'Got it!'
            );
            return;
        }

        // Calculate total amount to be billed
        $amount_to_bill = $amount + $transfer_charges;

        // Check if the sender has enough balance
        if ($sender->balance < $amount_to_bill) {
            $amount_remain = number_format($amount_to_bill - $sender->balance, 2);
            $this->dispatch(
                'alert',
                type: 'warning',
                title: 'Insufficient Fund!',
                text: "You need $this->ngn$amount_remain more to perform this operation. Kindly top up your wallet and try again.",
                button: 'Got it!'
            );
            return;
        }

        DB::beginTransaction();

        try {
            $sender->balance -= $amount_to_bill;
            $recipient->balance += $amount;

            $sender->save();
            $recipient->save();

            // Log the transaction
            $ref_number = "Transfer_" . date('YmdHis') . uniqid();

            Transaction::create([
                'user_id' => $sender->id,
                'type' => 'Transfer',
                'note' => $note,
                'operator_id' => $sender->id,
                'status' => 'successful',
                'amount' => $this->ngn . number_format($amount, 2),
                'old_balance' => $this->ngn . number_format($sender->getOriginal('balance'), 2),
                'new_balance' => $this->ngn . number_format($sender->balance, 2),
                'reference_number' => $ref_number
            ]);

            $additional_note = " || You received " . $this->ngn . number_format($amount, 2) . " from " . $sender->email;
            Transaction::create([
                'user_id' => $recipient->id,
                'type' => 'Transfer',
                'note' => $note . $additional_note,
                'operator_id' => $sender->id,
                'status' => 'successful',
                'amount' => $this->ngn . number_format($amount, 2),
                'old_balance' => $this->ngn . number_format($recipient->getOriginal('balance'), 2),
                'new_balance' => $this->ngn . number_format($recipient->balance, 2),
                'reference_number' => $ref_number
            ]);

            DB::commit();

            // Send notifications
            Notification::make()
                ->title("You've received  $this->ngn" . number_format($amount, 2) . " from " . $sender->name)
                ->icon('heroicon-m-wallet')
                ->iconColor('success')
                ->sendToDatabase($recipient);

            if ($sender->balance <= 100) {
                Notification::make()
                    ->title("Your wallet is low. Kindly top up your wallet")
                    ->icon('heroicon-m-wallet')
                    ->iconColor('warning')
                    ->sendToDatabase($sender);
            }

            // Send email notification
            $message = "You received " . $this->ngn . number_format($amount, 2) . " from " . $sender->email . " on " . date("l jS \of F Y h:i:s A") . ". Your new balance is " . $this->ngn . number_format($recipient->balance, 2) . ".";
            $subject = "You just earned " . $this->ngn . number_format($amount, 2);
            $this->sendEmail($user_email, $subject, $message, $recipient->name);

            $this->dispatch(
                'alert',
                type: 'success',
                title: 'Successful!',
                text: "You've successfully shared $this->ngn" . number_format($amount, 2) . " with " . $user_email . ". Your balance is $this->ngn" . number_format($sender->balance, 2),
                button: 'Got it!'
            );
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Transaction failed: ' . $e->getMessage());
            $this->dispatch(
                'alert',
                type: 'error',
                title: 'Transaction Failed',
                text: 'An error occurred while processing your request. Please try again later.',
                button: 'Got it!'
            );
        }
    }

    public function render()
    {
        return view('livewire.share-wallet', [
            'user_balance' => User::find(auth()->id())->first()->balance
        ]);
    }
}
