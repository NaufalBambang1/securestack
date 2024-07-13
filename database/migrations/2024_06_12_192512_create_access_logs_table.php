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
            $table->foreignId('UserLockerID')->constrained('users_locker', 'UserLockerID')->onDelete('cascade');
            $table->string('StatusLocker');
            $table->string('AccessMethodFingerprint')->nullable();
            $table->string('AccessResultFingerprint')->nullable();
            $table->dateTime('AccessTimeFingerprint')->nullable();
            $table->unsignedInteger('failed_attempts_fingerprint')->default(0); // Kolom percobaan gagal fingerprint
            $table->string('AccessMethod')->nullable();
            $table->string('AccessResult')->nullable();
            $table->dateTime('AccessTime')->nullable();
            $table->unsignedInteger('failed_attempts_rfid')->default(0); // Kolom percobaan gagal RFID
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
