<?php

namespace App\Http\Controllers;

use App\Models\Fingerprint;
use App\Models\User;
use Illuminate\Http\Request;

class FingerprintController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('fingerprint.register', compact('users'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|exists:users,username'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return back()->with('error', 'User not found');
        }

        $fingerprintAuth = new Fingerprint();
        $fingerprintAuth->user_id = $user->id;
        $fingerprintAuth->save();

        return redirect()->route('home')->with('success', 'Fingerprint authentication registered successfully');
    }
}
