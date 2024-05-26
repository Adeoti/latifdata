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
        Schema::create('electricity_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            //Charges
            $table->string('primary_charges')->nullable();
            $table->string('agent_charges')->nullable();
            $table->string('special_charges')->nullable();
            $table->string('api_charges')->nullable();


            

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
        Schema::dropIfExists('electricity_integrations');
    }
};
