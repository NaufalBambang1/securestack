<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLocker;
use App\Models\RFID;
use App\Models\Lockers;
use Illuminate\Support\Facades\Log;


class LockerController extends Controller
{
    public function showRegisterUserForm()
    {
        return view('register-user');
    }

    public function registerUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
        ]);

        UserLocker::create([
            'username' => $request->username,
        ]);

        return redirect()->route('register.user.form')->with('success', 'User registered successfully.');
    }

    public function showRegisterRfidForm()
    {
        $users = UserLocker::all();
        return view('register-rfid', compact('users'));
    }

    public function registerRfid(Request $request)
    {
        $request->validate([
            'rfid' => 'required|string|max:255',
            'lockerNumber' => 'required|string|max:255',
            'username' => 'required|string|max:255',
        ]);

        $user = UserLocker::where('username', $request->username)->first();

        if ($user) {
            RFID::create([
                'UserID' => $user->UserID,
                'RFIDTag' => $request->rfid,
            ]);
            echo "RFID data saved: {$request->rfid} for user: {$user->UserID}<br>";
        } else {
            Log::error("User not found for username: {$request->username}");
            return redirect()->route('register.rfid.form')->with('error', 'User not found.');
        }

        return redirect()->route('register.rfid.form')->with('success', 'RFID Tag registered successfully.');
    }

    public function showRegisterFingerprintForm()
    {
        $users = UserLocker::all();
        return view('register-fingerprint', compact('users'));
    }

    public function registerFingerprint(Request $request)
    {
        $request->validate([
            'fingerprint' => 'required|string|max:255',
            'lockerNumber' => 'required|string|max:255',
            'username' => 'required|string|max:255',
        ]);

        $user = UserLocker::where('username', $request->username)->first();

        if ($user) {
            Lockers::create([
                'fingerprint' => $request->fingerprint,
                'lockerNumber' => $request->lockerNumber,
                'username' => $request->username,
            ]);
            echo "Fingerprint data saved: {$request->fingerprint} for user: {$user->UserID}<br>";
        } else {
            Log::error("User not found for username: {$request->username}");
            return redirect()->route('register.fingerprint.form')->with('error', 'User not found.');
        }

        return redirect()->route('register.fingerprint.form')->with('success', 'Fingerprint Tag registered successfully.');
    }
}
