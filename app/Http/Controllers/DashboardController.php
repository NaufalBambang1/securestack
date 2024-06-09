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
    protected $port = 8000;
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
        // Memeriksa apakah locker dengan LockerID yang diberikan ada
        $lockerid = Lockers::where('LockerID', $request->LockerID)->first();
        if (!$lockerid) {
            return response()->json(['error' => 'Locker tidak ditemukan.'], 404);
        }
        
        // Memeriksa apakah user locker dengan locker_id yang sesuai ada
        $userLocker = UserLocker::where('locker_id', $lockerid->LockerID)->first();
        if (!$userLocker) {
            return response()->json(['error' => 'User locker tidak ditemukan.'], 404);
        }
        
        // Mendapatkan ID sidik jari dari request
        $fingerprint_id = $request->FingerprintData;
        $fingerprint = Fingerprint::where('FingerprintData', $fingerprint_id)->first();
        if (!$fingerprint) {
            return response()->json(['error' => 'Data sidik jari tidak ditemukan.'], 404);
        }
        
        // Membandingkan data sidik jari
        if ($fingerprint->FingerprintData != $userLocker->FingerprintData) {
            $accessResult = 'Ditolak';
            $accessMethod = 'Fingerprint';
        } else {
            $accessResult = 'Diterima';
            $accessMethod = 'Fingerprint';
        }

        // Memeriksa apakah log akses dengan kombinasi UserID dan LockerID sudah ada
        $accessLog = AccessLog::where('UserID', $userLocker->UserID)
                            ->where('LockerID', $userLocker->locker_id)
                            ->first();
        
        if ($accessLog) {
            // Jika log akses sudah ada, perbarui entri yang sudah ada
            $accessLog->AccessMethodFingerprint = $accessMethod;
            $accessLog->AccessResultFingerprint = $accessResult;
            $accessLog->AccessTimeFingerprint = now();
        } else {
            // Jika log akses belum ada, buat entri baru
            $accessLog = new AccessLog();
            $accessLog->UserID = $userLocker->UserID;
            $accessLog->LockerID = $userLocker->locker_id;
            $accessLog->AccessMethodFingerprint = $accessMethod;
            $accessLog->AccessResultFingerprint = $accessResult;
            $accessLog->AccessTimeFingerprint = now();
        }
        
        $accessLog->save();

        return response()->json(['message' => 'Log akses berhasil dibuat atau diperbarui.'], 200);
    }

    public function accessWithRfid(Request $request)
    {
        $lockerid = Lockers::where('LockerID', $request->LockerID)->first();
        if (!$lockerid) {
            return response()->json(['error' => 'Locker tidak ditemukan.'], 404);
        }

        $userLocker = UserLocker::where('locker_id', $lockerid->LockerID)->first();
        if (!$userLocker) {
            return response()->json(['error' => 'User locker tidak ditemukan.'], 404);
        }

        $rfid_id = $request->RFIDTag;
        $rfidTag = Rfid::where('RFIDTag',$rfid_id)->first();
        if (!$rfidTag) {
            return response()->json(['error' => 'RFID tag not found.'], 404);
        }

        if ($rfidTag->RFIDTag != $userLocker->RFIDTag) {
            $accessResult = 'Ditolak';
            $accessMethod = 'RFID';
        } else {
            $accessResult = 'Diterima';
            $accessMethod = 'RFID';
        }
        $accessLog = AccessLog::where('UserID', $userLocker->UserID)
                ->where('LockerID', $userLocker->locker_id)
                ->first();

        if ($accessLog) {
            $accessLog->AccessResult = $accessResult;
            $accessLog->AccessMethod = $accessMethod;
            $accessLog->AccessResult = $accessResult;
            $accessLog->AccessTime = now();
        } else {
            $accessLog = new AccessLog();
            $accessLog->UserID = $userLocker->UserID;
            $accessLog->LockerID = $userLocker->locker_id;
            $accessLog->AccessMethod = $accessMethod;
            $accessLog->AccessResult = $accessResult;
            $accessLog->AccessTime = now();
        }        
        
        $accessLog->save();

        return response()->json(['message' => 'Log akses berhasil dibuat atau diperbarui.'], 200);
    }
    // public function accessWithRfid(Request $request)
    // {
    //     $userLocker = UserLocker::find($request->UserID);
    //     if (!$userLocker) {
    //         return response()->json(['error' => 'User locker not found.'], 404);
    //     }

      
    //     $rfid_id = $request->rfid_id;
    //     $rfidTag = Rfid::find($rfid_id);
    //     if (!$rfidTag) {
    //         return response()->json(['error' => 'RFID tag not found.'], 404);
    //     }

    //     if ($rfidTag->RFIDTag != $userLocker->RFIDTag) {
    //         $accessResult = 'Failed';
    //         $accessMethod = 'RFID';
    //     } else {
    //         $accessResult = 'Success';
    //         $accessMethod = 'RFID';
    //     }

      
    //     $accessLog = new AccessLog();
    //     $accessLog->UserID = $userLocker->UserID;
    //     $accessLog->LockerID = $userLocker->locker_id;
    //     $accessLog->AccessMethod = $accessMethod;
    //     $accessLog->AccessResult = $accessResult;
    //     $accessLog->AccessTime = now();
    //     $accessLog->save();

    //     return response()->json(['message' => 'Access log created successfully.'], 200);
    // }

    // public function verifyFingerprint(Request $request)
    // {
    //     $fingerprint = Fingerprint::where('FingerprintData', $request->FingerprintData)->first();

    //     if ($fingerprint) {
    //         return response()->json(['verified' => true, 'fingerprint_id' => $fingerprint->id]);
    //     } else {
    //         return response()->json(['verified' => false]);
    //     }
    // }
}
