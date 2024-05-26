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
        Schema::create('mobile_data', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('network')->nullable();
            $table->string('plan_size')->nullable();
            $table->string('plan_type')->nullable();
            $table->string('validity')->nullable();

            //Pricing
            $table->string('primary_price')->nullable();
            $table->string('agent_price')->nullable();
            $table->string('special_price')->nullable();
            $table->string('api_price')->nullable();

            //Cashback
            $table->string('primary_cashback')->nullable();
            $table->string('agent_cashback')->nullable();
            $table->string('special_cashback')->nullable();
            $table->string('api_cashback')->nullable();



            $table->string('country_code')->nullable();

            $table->string('api_code')->nullable();
            $table->string('service_id')->nullable();
            $table->text('endpoint')->nullable();
            $table->text('vendor_name')->nullable();
            $table->boolean('active_status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_data');
    }
};
