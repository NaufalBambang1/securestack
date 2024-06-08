<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('users_locker', function (Blueprint $table) {
            $table->id('UserID');
            $table->string('Username');
            $table->unsignedBigInteger('locker_id')->nullable();
            $table->foreign('locker_id')->references('LockerID')->on('lockers');
            $table->string('Role');
            $table->unsignedBigInteger('rfid_id')->nullable();
            $table->foreign('rfid_id')->references('id')->on('rfids');
            $table->string('RFIDTag')->nullable();
            $table->unsignedBigInteger('fingerprint_id')->nullable();
            $table->foreign('fingerprint_id')->references('id')->on('fingerprint_auth');
            $table->string('FingerprintData')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_locker');
    }
};
