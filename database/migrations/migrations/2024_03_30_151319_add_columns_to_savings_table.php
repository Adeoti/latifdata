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
        Schema::table('savings', function (Blueprint $table) {
            //
            $table -> text('title');
            $table -> longText('note') -> nullable();
            $table -> double('amount');
            $table -> foreignIdFor(User::class);
            $table -> date('dated');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('savings', function (Blueprint $table) {
            //
            $table -> dropColumn('title');
            $table -> dropColumn('note');
            $table -> dropColumn('amount');
            $table -> dropColumn('user_id');
            $table -> dropColumn('dated');
        });
    }
};
