<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLocker extends Model
{
    use HasFactory;
    public $table="users_locker";
    protected $fillable = [
        'UserID',
        'Username',
        'Password',
        'Role',
        'RFIDTag',
    ];

    protected $hidden = [
        'password',
    ];

}
