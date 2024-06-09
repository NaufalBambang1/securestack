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
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id('LogID');
            $table->foreignId('UserID')->constrained('users_locker', 'UserID')->onDelete('cascade');
            $table->foreignId('LockerID')->constrained('lockers', 'LockerID')->onDelete('cascade');
            $table->string('AccessMethodFingerprint')->nullable();
            $table->string('AccessResultFingerprint')->nullable();
            $table->dateTime('AccessTimeFingerprint')->nullable();
            $table->string('AccessMethod')->nullable();
            $table->string('AccessResult')->nullable();
            $table->dateTime('AccessTime')->nullable();
            $table->integer('RejectCount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};
