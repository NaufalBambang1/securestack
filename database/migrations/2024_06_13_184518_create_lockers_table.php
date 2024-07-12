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
        Schema::create('lockers', function (Blueprint $table) {
            $table->unsignedBigInteger('UserLockerID'); // PK dan FK
            $table->string('lockerNumber');
            $table->string('StatusLocker');
            $table->timestamps();

            // Set UserLockerID sebagai primary key
            $table->primary('UserLockerID');

            // Setup foreign key constraint
            $table->foreign('UserLockerID')
                ->references('UserLockerID')
                ->on('users_locker')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lockers');
    }
};