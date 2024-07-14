<?php

namespace App\Http\Controllers;

use App\Models\UserLocker;
use Illuminate\Http\Request;

class FingerprintController extends Controller
{
    public function index()
    {
        $users = UserLocker::all();
        return view('fingerprint.register', compact('users'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|exists:users_locker,Username' // Ubah validasi untuk sesuai dengan tabel users_locker
        ]);

        $user = UserLocker::where('Username', $request->username)->first();

        if (!$user) {
            return back()->with('error', 'User not found');
        }

        // Simpan data fingerprint
        $user->FingerprintData = $request->fingerprintData; // Sesuaikan dengan nama field yang benar di form Anda
        $user->save();

        return redirect()->route('home')->with('success', 'Fingerprint authentication registered successfully');
    }
}
