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
        Schema::create('view_data', function (Blueprint $table) {
            $table->id('LogID');
            $table->foreignId('UserID')->constrained('users_locker', 'UserID')->onDelete('cascade');
            $table->foreignId('LockerID')->constrained('lockers', 'LockerID')->onDelete('cascade');
            $table->string('AccessMethodFingerprint')->nullable();
            $table->dateTime('AccessTimeFingerprint')->nullable();
            $table->string('AccessResultFingerprint')->nullable();
            $table->unsignedInteger('failed_attempts_fingerprint')->default(0); // Kolom percobaan gagal fingerprint
            $table->string('AccessMethod')->nullable();
            $table->dateTime('AccessTime')->nullable();
            $table->string('AccessResult')->nullable();
            $table->unsignedInteger('failed_attempts_rfid')->default(0); // Kolom percobaan gagal RFID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_data');
    }
};
