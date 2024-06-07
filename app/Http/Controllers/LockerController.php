<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Locker;
use PhpMqtt\Client\Facades\MQTT;
use PhpMqtt\Client\Message;
use PhpMqtt\Client\ConnectionSettings;

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
            // Add other necessary validations
        ]);

        User::create([
            'username' => $request->username,
            // Add other necessary fields
        ]);

        return redirect()->route('register.user.form')->with('success', 'User registered successfully.');
    }

    public function showRegisterRfidForm()
    {
        $users = User::all();
        return view('register-rfid', compact('users'));
    }

    public function registerRfid(Request $request)
    {
        $request->validate([
            'rfid' => 'required|string|max:255',
            'lockerNumber' => 'required|string|max:255',
            'username' => 'required|string|max:255',
        ]);

        Locker::create([
            'rfid' => $request->rfid,
            'lockerNumber' => $request->lockerNumber,
            'username' => $request->username,
        ]);

        return redirect()->route('register.rfid.form')->with('success', 'RFID Tag registered successfully.');
    }

    public function showRegisterFingerprintForm()
    {
        $users = User::all();
        return view('register-fingerprint', compact('users'));
    }

    public function registerFingerprint(Request $request)
    {
        $request->validate([
            'fingerprint' => 'required|string|max:255',
            'lockerNumber' => 'required|string|max:255',
            'username' => 'required|string|max:255',
        ]);

        Locker::create([
            'fingerprint' => $request->fingerprint,
            'lockerNumber' => $request->lockerNumber,
            'username' => $request->username,
        ]);

        return redirect()->route('register.fingerprint.form')->with('success', 'Fingerprint Tag registered successfully.');
    }
    
}
