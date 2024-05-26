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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->foreignIdFor(User::class)->constrained();
            $table->longText('api_response')->nullable();
            $table->string('status')->default('processing');
            $table->text('note')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('provider')->nullable();
            $table->double('amount')->default(0);
            $table->double('old_balance')->nullable();
            $table->double('new_balance')->nullable();
            $table->text('reference_number')->nullable();
            $table->string('plan_name')->nullable();
            $table->string('network')->nullable();
            $table->string('meter_type')->nullable();
            $table->text('meter_number')->nullable();
            $table->text('meter_name')->nullable();
            $table->text('cable_plan')->nullable();
            $table->integer('operator_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
