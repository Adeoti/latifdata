<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cable_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('name')->nullable();
            $table->string('plan_type')->nullable();

            //Price
            $table->string('price')->nullable();
            
            //Charges
            $table->string('primary_charges')->nullable();
            $table->string('agent_charges')->nullable();
            $table->string('special_charges')->nullable();
            $table->string('api_charges')->nullable();


            
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
        Schema::dropIfExists('cable_subscriptions');
    }
};
