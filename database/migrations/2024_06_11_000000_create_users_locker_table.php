<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('users_locker', function (Blueprint $table) {
            $table->id('UserLockerID');
            $table->string('Username');
            $table->string('FingerprintData')->nullable();
            $table->string('RFIDTag')->nullable();
            $table->string('LockerNumber');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_locker');
    }
};
