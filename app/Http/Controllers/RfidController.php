<?php

namespace App\Http\Controllers;

use App\Models\Rfid;
use App\Models\User;
use Illuminate\Http\Request;

class RfidController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('rfid.register', compact('users'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'rfid' => 'required|string|max:255',
            'lockerNumber' => 'required|string|max:255',
            'username' => 'required|string|exists:users,username'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return back()->with('error', 'User not found');
        }

        $rfid = new Rfid();
        $rfid->user_id = $user->id;
        $rfid->rfid_tags = $request->rfid;
        $rfid->save();

        return redirect()->route('home')->with('success', 'RFID tag registered successfully');
    }
}
