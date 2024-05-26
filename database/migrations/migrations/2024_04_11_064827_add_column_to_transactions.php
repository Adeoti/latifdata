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
        Schema::table('transactions', function (Blueprint $table) {
            //
            
            $table->string('iuc_number')->after('new_balance')->nullable();
            $table->string('customer_name')->after('new_balance')->nullable();
            $table->string('charges')->after('new_balance')->nullable();
            $table->string('cashback')->after('new_balance')->nullable();
            $table->string('amount_paid')->after('new_balance')->nullable();
            $table->string('token')->after('new_balance')->nullable();
            $table->string('disco_name')->after('new_balance')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
            $table->dropColumn('amount_paid');
            $table->dropColumn('iuc_number');
            $table->dropColumn('cashback');
            $table->dropColumn('charges');
            $table->dropColumn('customer_name');
            $table->dropColumn('token');
            $table->dropColumn('disco_name');
        });
    }
};
