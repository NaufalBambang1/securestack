<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewData extends Model
{
    use HasFactory;
    public $table ="view_data";
    protected $fillable = [
        'UserID',
        'LockerID',
        'StatusLocker',
        'AccessMethodFingerprint',
        'AccessTimeFingerprint',
        'AccessResultFingeprint',
        'failed_attempts_fingerprint',
        'AccessMethod',
        'AccessTime',
        'AccessResult',
        'failed_attempts_rfid',
    ];
    protected $primaryKey = 'ViewLogID';
    public function accessLog()
    {
        return $this->belongsTo(AccessLog::class, 'LogID', 'LogID');
    }
}

