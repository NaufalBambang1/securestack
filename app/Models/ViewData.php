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
        'AccessMethodFingerprint',
        'AccessTimeFingerprint',
        'AccessResultFingeprint',
        'failed_attempts_fingerprint',
        'AccessMethod',
        'AccessTime',
        'AccessResult',
        'failed_attempts_rfid',
    ];
    protected $primaryKey = 'LogID';
}

