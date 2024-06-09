<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLocker extends Model
{
    use HasFactory;
    public $table="users_locker";

    protected $primaryKey = 'UserID'; // Specify the primary key

    protected $fillable = [
        'UserID',
        'Username',
        'locker_id',
        'Role',
        'rfid_id',
        'RFIDTag',
        'fingerprint_id',
        'FingerprintData',
        'failed_attempts_fingerprint',
        'failed_attempts_rfid',
    ];

    public function rfid()
    {
        return $this->belongsTo(Rfid::class, 'rfid_id');
    } 
    public function fingerprint()
    {
        return $this->belongsTo(Fingerprint::class, 'fingerprint_id');
    } 

    public function lockers()
    {
        return $this->belongsTo(Lockers::class, 'locker_id');
    } 

}
