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
        'Password',
        'Role',
        'RFIDTags',
        'FingerprintData',
        'KeyPadCode'
    ];

    protected $hidden = [
        'password',
    ];

    public function rfid()
    {
        return $this->belongsTo(Rfid::class, 'rfid_id');
    } 


}
