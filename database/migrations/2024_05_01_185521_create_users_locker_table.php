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
        Schema::create('users_locker', function (Blueprint $table) {
            $table->id('UserID');
            $table->string('Username');
            $table->string('Password');
            $table->string('Role');
            $table->string('RFIDTag')->nullable();
            $table->string('FingerprintId')->nullable();
            $table->string('KeypadCode')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_locker');
    }
};
