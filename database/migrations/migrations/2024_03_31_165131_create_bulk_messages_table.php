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
        Schema::create('bulk_messages', function (Blueprint $table) {
            $table->id();
            $table->text('title')->nullable();
            $table->foreignIdFor(User::class);
            $table->longText('message');
            $table->string('style')->default('native');
            $table->string('target');
            $table->text('graphics')->nullable();
            $table->json('user_who_read')->nullable()->comment('Read Status Tracker..');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_messages');
    }
};
