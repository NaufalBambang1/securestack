<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    use HasFactory;
    public $table ="access_logs";
    protected $fillable = [
        'UserLockerID',
        'StatusLocker',
        'AccessMethodFingerprint',
        'AccessResultFingeprint',
        'AccessTimeFingerprint',
        'failed_attempts_fingerprint',
        'AccessMethod',
        'AccessResult',
        'AccessTime',
        'failed_attempts_rfid',
    ];
    protected $primaryKey = 'LogID';

    public function userLocker()
    {
        return $this->belongsTo(UserLocker::class, 'UserLockerID');
    } 
}