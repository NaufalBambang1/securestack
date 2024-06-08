<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLocker;
use App\Models\Lockers;
use App\Models\AccessLog;
use App\Models\Rfid;
use App\Models\Fingerprint;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $data = AccessLog::select(
                            'access_logs.LogID',
                            'users_locker.username',
                            'lockers.lockerNumber',
                            'lockers.StatusLocker',
                            'access_logs.AccessMethodFingerprint',
                            'access_logs.AccessResultFingerprint',
                            'access_logs.AccessTimeFingerprint',
                            'access_logs.AccessMethod',
                            'access_logs.AccessResult',
                            'access_logs.AccessTime'
                        )
                        ->join('users_locker', 'access_logs.UserID', '=', 'users_locker.UserID')
                        ->join('lockers', 'access_logs.LockerID', '=', 'lockers.LockerID')
                        ->get();

        return view('dashboard', compact('data'));
    }

    public function accessWithFingerprint(Request $request)
    {
        $failedAttempts = session('failed_attempts_fingerprint', 0);

        if ($failedAttempts >= 5) {
            $accessLog = new AccessLog();
            $accessLog->UserID = $request->UserID;
            $accessLog->LockerID = $request->LockerID;
            $accessLog->AccessMethodFingerprint = 'Fingerprint';
            $accessLog->AccessResultFingerprint = 'Blocked';
            $accessLog->AccessTimeFingerprint = Carbon::now();
            $accessLog->save();
            return response()->json(['message' => 'Access blocked due to excessive failed attempts.'], 403);
        }

        $userLocker = UserLocker::find($request->UserID);
        $fingerprint = Fingerprint::where('FingerprintData', $request->FingerprintData)->first();

        $accessLog = new AccessLog();
        $accessLog->UserID = $request->UserID;
        $accessLog->LockerID = $request->LockerID;
        $accessLog->AccessMethodFingerprint = 'Fingerprint';

        if ($fingerprint && $userLocker->fingerprint_id == $fingerprint->id) {
            $accessLog->AccessResultFingerprint = 'Diterima';
            session(['failed_attempts_fingerprint' => 0]); 
        } else {
            $accessLog->AccessResultFingerprint = 'Ditolak';
            $failedAttempts++;
            session(['failed_attempts_fingerprint' => $failedAttempts]); 
        }

        $accessLog->AccessTimeFingerprint = Carbon::now();
        $accessLog->save();

        return response()->json(['message' => 'Access logged with fingerprint', 'result' => $accessLog->AccessResultFingerprint]);
    }

    public function accessWithRfid(Request $request)
    {
        $failedAttempts = session('failed_attempts_rfid', 0);

        if ($failedAttempts >= 5) {
            $accessLog = new AccessLog();
            $accessLog->UserID = $request->UserID;
            $accessLog->LockerID = $request->LockerID;
            $accessLog->AccessMethod = 'RFID';
            $accessLog->AccessResult = 'Blocked';
            $accessLog->AccessTime = Carbon::now();
            $accessLog->save();
            return response()->json(['message' => 'Access blocked due to excessive failed attempts.'], 403);
        }

        $fingerprintAccess = session('fingerprint_access');

        if (!$fingerprintAccess) {
            return response()->json(['message' => 'Please authenticate with fingerprint first.'], 403);
        }

        $userLocker = UserLocker::find($request->UserID);
        $rfid = Rfid::where('RFIDTag', $request->RFIDTag)->first();

        $accessLog = new AccessLog();
        $accessLog->UserID = $request->UserID;
        $accessLog->LockerID = $request->LockerID;
        $accessLog->AccessMethod = 'RFID';

        if ($rfid && $userLocker->rfid_id == $rfid->id) {
            $accessLog->AccessResult = 'Diterima';
            session(['failed_attempts_rfid' => 0]);
        } else {
            $accessLog->AccessResult = 'Ditolak';
            $failedAttempts++;
            session(['failed_attempts_rfid' => $failedAttempts]); 
        }

        $accessLog->AccessTime = Carbon::now();
        $accessLog->save();

        return response()->json(['message' => 'Access logged with RFID', 'result' => $accessLog->AccessResult]);
    }
}
