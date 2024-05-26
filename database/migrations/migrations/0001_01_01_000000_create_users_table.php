<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique(); 
            $table -> string('username') -> nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('position')->default('user');
            $table->string('phone_number')->nullable();
            $table->longText('address')->nullable();
           
            $table->foreignIdFor(User::class)->nullable()->constrained();
            
            //Userpermission
            $table->boolean('add_user')->default(false)->comment('permission');
            $table->boolean('edit_user')->default(false)->comment('permission');
            $table->boolean('view_user')->default(false)->comment('permission');
            $table->boolean('delete_user')->default(false)->comment('permission');

            //Expensespermission
            $table->boolean('add_expenses')->default(false)->comment('permission');
            $table->boolean('edit_expenses')->default(false)->comment('permission');
            $table->boolean('view_expenses')->default(false)->comment('permission');
            $table->boolean('delete_expenses')->default(false)->comment('permission');

            //Savingspermission
            $table->boolean('add_savings')->default(false)->comment('permission');
            $table->boolean('edit_savings')->default(false)->comment('permission');
            $table->boolean('view_savings')->default(false)->comment('permission');
            $table->boolean('delete_savings')->default(false)->comment('permission');

            //Customerspermission
            $table->boolean('add_customer')->default(false)->comment('permission');
            $table->boolean('edit_customer')->default(false)->comment('permission');
            $table->boolean('view_customer')->default(false)->comment('permission');
            $table->boolean('delete_customer')->default(false)->comment('permission');

            //Announcementpermission
            $table->boolean('can_announcement')->default(false)->comment('permission');

            //PrivateMessagepermission
            $table->boolean('can_private_message')->default(false)->comment('permission');

            //ViewTransactionspermission
            $table->boolean('can_view_transactions')->default(false)->comment('permission');

            //ManageServicespermission
            $table->boolean('can_manage_services')->default(false)->comment('permission');

            //UpgradeCustomerpermission
            $table->boolean('can_upgrade_customer')->default(false)->comment('permission');

            //ResetPasswordpermission
            $table->boolean('can_reset_password')->default(false)->comment('permission');

            //Credit/DebitCustomerpermission
            $table->boolean('can_credit_customer')->default(false)->comment('permission');

            //SetPricepermission
            $table->boolean('can_set_price')->default(false)->comment('permission');

            //WidgetBalancepermission
            $table->boolean('widget_balance')->default(false)->comment('permission');

            //WidgetUserBalancepermission
            $table->boolean('widget_user_balance')->default(false)->comment('permission');

            //WidgetSavingspermission
            $table->boolean('widget_savings')->default(false)->comment('permission');

            //WidgetExpensespermission
            $table->boolean('widget_expenses')->default(false)->comment('permission');

            //WidgetRefundpermission
            $table->boolean('widget_refund')->default(false)->comment('permission');

            //WidgetCashflowpermission
            $table->boolean('widget_cashflow')->default(false)->comment('permission');

            //WidgetSalespermission
            $table->boolean('widget_sales')->default(false)->comment('permission');

            //PaymentMethodpermission
            $table->boolean('toggle_payment_method')->default(false)->comment('permission');

            //SetChargespermission
            $table->boolean('set_charges')->default(false)->comment('permission');

            //SetCashbpackpermission
            $table->boolean('set_cashback')->default(false)->comment('permission');

            //SetReferralpermission
            $table->boolean('set_referral')->default(false)->comment('permission');


            //Customer-based Info
            $table->double('balance')->default('00.00');
            $table->string('referral_code')->nullable();
            $table->string('package')->default('primary');
            $table->string('transaction_pin')->nullable();
            $table->string('bvn')->nullable()->comment('kyc info');
            $table->string('nin')->nullable()->comment('kyc info');
            $table->string('bvn_date_of_birth')->nullable()->comment('kyc info');
            $table->double('cashback_balance')->nullable()->comment('reward');
            $table->double('referral_balance')->nullable()->comment('reward');
            $table->string('monniepoint_acct')->nullable();
            $table->string('wema_acct')->nullable();
            $table->string('sterling_acct')->nullable();
            $table->string('gtb_acct')->nullable();
            $table->string('providus_acct')->nullable();
            $table->string('rehoboth_acct')->nullable();
            $table->string('fidelity_acct')->nullable();
            $table->string('paystack_acct')->nullable();
            $table->string('flutterwave_acct')->nullable();
            $table -> boolean('is_staff')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
