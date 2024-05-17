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
        Schema::table('payment_integrations', function (Blueprint $table) {
            //
            $table->string('monnify_base_url')->nullable();
            $table->string('vtpass_api_key')->nullable();
            $table->string('vtpass_public_key')->nullable();
            $table->string('vtpass_secret_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_integrations', function (Blueprint $table) {
            //
            $table->dropColumn('monnify_base_url');
            $table->dropColumn('vtpass_api_key');
            $table->dropColumn('vtpass_public_key');
            $table->dropColumn('vtpass_secret_key');
            
        });
    }
};
