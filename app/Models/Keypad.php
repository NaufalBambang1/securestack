<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keypad extends Model
{
    use HasFactory;
    public $table="keypad_auth";
    protected $fillable = [
        'KeyPadCode',
    ];
}
