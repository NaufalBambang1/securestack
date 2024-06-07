<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRfidTable extends Migration
{
    public function up()
    {
        Schema::create('rfids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('UserID')->constrained('users_locker', 'UserID')->onDelete('cascade');
            $table->string('RFIDTag', 255);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rfids');
    }
}
