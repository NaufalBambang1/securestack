<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisterlockerController extends Controller
{
    public function index(){
        return view('auth.register-user-locker');
    }
}
