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
        Schema::table('mobile_airtimes', function (Blueprint $table) {
            //
            $table->integer('minimum_amount')->after('api_cashback')->comment('Minimum allowed amount');
            $table->integer('maximum_amount')->after('api_cashback')->comment('Maximum allowed amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobile_airtimes', function (Blueprint $table) {
            //
            $table->dropColumn('minimum_amount');
            $table->dropColumn('maximum_amount');
        });
    }
};
