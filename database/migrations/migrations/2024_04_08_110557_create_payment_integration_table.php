<?php

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
        Schema::create('payment_integrations', function (Blueprint $table) {
            $table->id();

            $table->string('monnify_api_key')->nullable();
            $table->string('monnify_secret_key')->nullable();
            $table->string('monnify_contract_code')->nullable();
            $table->string('monnify_bvn')->nullable();
            
            $table->string('paystack_secret_key')->nullable();
            $table->string('paystack_live_key')->nullable();

            $table->text('manual_bank_name')->nullable();
            $table->text('manual_account_name')->nullable();
            $table->text('manual_account_number')->nullable();




            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_integration');
    }
};
