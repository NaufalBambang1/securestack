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
        Schema::create('keypad_auth', function (Blueprint $table) {
            $table->foreignId('UserID')->primary()->constrained('users_locker', 'UserID')->onDelete('cascade');
            $table->string('KeyPadCode');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keypad_auth');
    }
};
