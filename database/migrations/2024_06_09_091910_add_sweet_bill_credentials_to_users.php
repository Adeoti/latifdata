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
            $table->text('sweetbill_api_key')->nullable();
            $table->text('sweetbill_email')->nullable();
            $table->text('sweetbill_password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_integrations', function (Blueprint $table) {
            //
            $table->dropColumn('sweetbill_api_key');
            $table->dropColumn('sweetbill_email');
            $table->dropColumn('sweetbill_password');

        });
    }
};
