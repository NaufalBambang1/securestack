<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    use HasFactory;
    public $table ="access_logs";
    protected $fillable = [
        'UserID',
        'LockerID',
        'AccessTime',
        'AccessMethod',
        'AccessResult',
    ];
}