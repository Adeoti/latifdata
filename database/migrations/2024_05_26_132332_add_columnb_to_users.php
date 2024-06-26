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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('account_reference')->nullable()->comment('Automated Account Reference');
            $table->boolean('filled_kyc')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('account_reference');
            $table->dropColumn('filled_kyc');
            //$table->dropColumn('filled_kyc');
            //$table->dropColumn('filled_kyc');
        });
    }
};
