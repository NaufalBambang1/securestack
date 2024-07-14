<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLocker extends Model
{
    use HasFactory;
    public $table="users_locker";

    protected $primaryKey = 'UserLockerID'; // Specify the primary key

    protected $fillable = [
        'Username',
        'FingerprintData',
        'RFIDTag',
        'LockerNumber',
        'Attempts_fingerprint',
        'Attempts_rfid'
    ];

}
