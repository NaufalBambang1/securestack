<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lockers extends Model
{
    use HasFactory;
    public $table = "lockers";
    protected $fillable = [
        'UserLockerID',
        'lockerNumber',
        'StatusLocker'
    ];
    protected $primaryKey = 'UserLockerID';
    public $incrementing = false; // Karena UserLockerID bukan auto-increment

    public function userLocker()
    {
        return $this->belongsTo(UserLocker::class, 'UserLockerID');
    }
}
