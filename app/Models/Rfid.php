<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rfid extends Model
{
    use HasFactory;
    public $table="rfid_auth";
    protected $fillable = [
        'RFIDTags',
    ];
}
