<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFingerprintAuthTable extends Migration
{
    public function up()
    {
        Schema::create('fingerprint_auth', function (Blueprint $table) {
            $table->id();
            $table->string('FingerprintData');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fingerprint_auth');
    }
}
