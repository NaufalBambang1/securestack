<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRfidTable extends Migration
{
    public function up()
    {
        Schema::create('rfids', function (Blueprint $table) {
            $table->id(); // Gunakan 'id()' untuk primary key
            $table->string('RFIDTag', 255);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rfids');
    }
}
