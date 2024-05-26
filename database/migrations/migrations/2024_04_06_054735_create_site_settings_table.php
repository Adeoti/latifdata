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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->double('agent_charges');
            $table->double('special_charges');
            $table->double('api_charges');
            $table->double('portal_dev_charges');
            $table->double('wallet_to_charges');
            $table->double('cashbak_cap_amount');
            $table->double('referral_cap_amount');
            $table->double('refferal_commision');
            $table->boolean('refferal_status');
            $table->string('default_theme')->default('dark');
            $table->text('whatsapp_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
