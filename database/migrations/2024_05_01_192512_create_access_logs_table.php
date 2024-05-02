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
            $table->dateTime('AccessTime');
            $table->enum('AccessMethod', ['fingerprint', 'keypad', 'RFID']);
            $table->enum('AccessResult', ['granted', 'denied']);
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
