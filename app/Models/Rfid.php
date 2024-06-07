<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rfid extends Model
{
    use HasFactory;

    protected $table = 'rfids'; // Specify the correct table name
    protected $fillable = ['RFIDTag']; // Specify the fillable fields
}
