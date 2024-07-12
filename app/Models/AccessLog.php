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
        'AccessMethodFingerprint',
        'AccessResultFingeprint',
        'AccessTimeFingerprint',
        'AccessMethod',
        'AccessResult',
        'AccessTime',
    ];
    protected $primaryKey = 'LogID';

    public function userLocker()
    {
        return $this->belongsTo(UserLocker::class, 'UserLockerID');
    } 
}