<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RFID extends Model
{
    use HasFactory;

    protected $table = 'rfid'; // Specify the correct table name
    protected $fillable = ['user_id', 'RFIDTag']; // Specify the fillable fields
}
