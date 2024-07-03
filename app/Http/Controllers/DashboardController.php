<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLocker;
use App\Models\Lockers;
use App\Models\AccessLog;
use App\Models\Rfid;
use App\Models\Fingerprint;
use App\Models\ViewData;
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
                            'access_logs.AccessTime',
                            'access_logs.LockerID',
                        )
                        ->join('users_locker', 'access_logs.UserID', '=', 'users_locker.UserID')
                        ->join('lockers', 'access_logs.LockerID', '=', 'lockers.LockerID')
                        ->get();

        return view('dashboard', compact('data'));
    }

    public function indexViewData($logID)
    {
        $dataView = ViewData::select(
                    'view_data.ViewLogID',
                    'access_logs.LogID',
                    'users_locker.username',
                    'lockers.lockerNumber',
                    'view_data.StatusLocker',
                    'view_data.AccessMethodFingerprint',
                    'view_data.AccessResultFingerprint',
                    'view_data.AccessTimeFingerprint',
                    'view_data.failed_attempts_fingerprint',
                    'view_data.AccessMethod',
                    'view_data.AccessResult',
                    'view_data.AccessTime',
                    'view_data.failed_attempts_rfid',
                )
                ->join('access_logs', 'view_data.LogID', '=', 'access_logs.LogID')
                ->join('users_locker', 'access_logs.UserID', '=', 'users_locker.UserID')
                ->join('lockers', 'access_logs.LockerID', '=', 'lockers.LockerID')
                ->where('access_logs.LogID', $logID)
                ->get();

        return view('viewdata', compact('dataView'));
    }
    public function accessWithFingerprint(Request $request)
    {
        
        // Memeriksa apakah locker dengan LockerID yang diberikan ada
        $lockerid = Lockers::where('LockerID', $request->LockerID)->first();
        if (!$lockerid) {
            return response()->json(['error' => 'Locker tidak ditemukan.1'], 404);
        }
        
        // Memeriksa apakah user locker dengan locker_id yang sesuai ada
        $userLocker = UserLocker::where('locker_id', $lockerid->LockerID)->first();
        if (!$userLocker) {
            return response()->json(['error' => 'User locker tidak ditemukan.'], 404);
        }
        
// Mendapatkan ID sidik jari dari request
        $fingerprint_id = $request->FingerprintData;

// Membandingkan data sidik jari langsung dengan data di $userLocker
        if ($fingerprint_id != $userLocker->FingerprintData) {
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

        $detailLog = new ViewData();
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

        // Check if access is denied and increment failed attempts
        if ($accessResult == 'Ditolak') {
            $userLocker->failed_attempts_fingerprint = $userLocker->failed_attempts_fingerprint + 1;
            $userLocker->save();
            $detailLogFailed = $detailLog->failed_attempts_fingerprint = $detailLog->failed_attempts_fingerprint + 1;

            // Check if failed attempts reach 5 and block locker
            if ($userLocker->failed_attempts_fingerprint >= 5) {
                $this->blockLocker($lockerid->LockerID);
            }
        } else {
            // Reset failed attempts if access is granted
            $userLocker->failed_attempts_fingerprint = 0;
            $userLocker->save();
            $detailLogFailed = $detailLog->failed_attempts_fingerprint = 0;
        }

        
        $accessLog->save();

        
        $detailLog->LogID = $accessLog->LogID;
        $detailLog->UserID = $userLocker->UserID;
        $detailLog->LockerID = $userLocker->locker_id;
        $detailLog->AccessMethodFingerprint = $accessMethod;
        $detailLog->AccessResultFingerprint = $accessResult;
        $detailLog->failed_attempts_fingerprint =$detailLogFailed;
        $detailLog->AccessTimeFingerprint = now();
        $detailLog->save();
 
        return response()->json([
            'message' => 'Log akses berhasil dibuat atau diperbarui.',
            'accessResultFingerprint' => $accessResult,
            'failedAttemptsFingerprint' => $userLocker->failed_attempts_fingerprint,
            'lockerStatus' => $lockerid->StatusLocker
        ], 200);
    }

    public function accessWithRfid(Request $request)
    {
        $lockerid = Lockers::where('LockerID', $request->LockerID)->first();
        if (!$lockerid) {
            return response()->json(['error' => 'Locker tidak ditemukan.3'], 404);
        }

        $userLocker = UserLocker::where('locker_id', $lockerid->LockerID)->first();
        if (!$userLocker) {
            return response()->json(['error' => 'User locker tidak ditemukan.'], 404);
        }

        $rfid_id = $request->RFIDTag;

        if ($rfid_id != $userLocker->RFIDTag) {
            $accessResult = 'Ditolak';
            $accessMethod = 'RFID';
        } else {
            $accessResult = 'Diterima';
            $accessMethod = 'RFID';
        }
        $accessLog = AccessLog::where('UserID', $userLocker->UserID)
                ->where('LockerID', $userLocker->locker_id)
                ->first();
        $detailLog = new ViewData();

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

        // Check if access is denied and increment failed attempts
        if ($accessResult == 'Ditolak') {
            $userLocker->failed_attempts_rfid = $userLocker->failed_attempts_rfid + 1;
            $userLocker->save();
            $detailLogFailed = $detailLog->failed_attempts_rfid = $detailLog->failed_attempts_rfid + 1;
            // Check if failed attempts reach 5 and block locker
            if ($userLocker->failed_attempts_rfid >= 5) {
                $this->blockLocker($lockerid->LockerID);
            }
        } else {
            // Reset failed attempts if access is granted
            $userLocker->failed_attempts_rfid = 0;
            $userLocker->save();
            $detailLogFailed =  $detailLog->failed_attempts_rfid = 0;
        }

        if($accessResult == 'Diterima'){
            $lockerid->StatusLocker = 'Opened';
            
            $lockerid->save();
        }

        $accessLog->save();
        
        
        $detailLog->LogID = $accessLog->LogID;
        $detailLog->UserID = $userLocker->UserID;
        $detailLog->LockerID = $userLocker->locker_id;
        $detailLog->AccessMethod = $accessMethod;
        $detailLog->AccessResult = $accessResult;
        $detailLog->failed_attempts_rfid = $detailLogFailed;
        $detailLog->AccessTime = now();
        $detailLog->save();

        return response()->json([
            'message' => 'Log akses berhasil dibuat atau diperbarui.',
            'accessResultRFID' => $accessResult,
            'failedAttemptsRfid' => $userLocker->failed_attempts_rfid,
            'lockerStatus' => $lockerid->StatusLocker
        ], 200);
    }
    private function blockLocker($locker_id)
    {
        $locker = Lockers::where('LockerID', $locker_id)->first();
        if ($locker) {
            $locker->StatusLocker = 'Blocked';
            $locker->save();
            return response()->json(['message' => 'Status locker berhasil diblokir.'], 200);
        } else {
            return response()->json(['error' => 'Locker tidak ditemukan.2'], 404);
        }
    }

    public function resetButton(Request $request)
    {
        $LockerID = $request->input('LockerID');
        
        // Cari data user locker berdasarkan LockerID
        $userLocker = UserLocker::where('locker_id', $LockerID)->first();
        if (!$userLocker) {
            return response()->json(['error' => 'User locker tidak ditemukan.'], 404);
        }
    
        // Reset failed attempts
        $userLocker->failed_attempts_fingerprint = 0;
        $userLocker->failed_attempts_rfid = 0;
        $userLocker->save();
    
        // Update status locker menjadi 'Closed'
        $locker = Lockers::where('LockerID', $userLocker->locker_id)->first();
        if ($locker) {
            $locker->StatusLocker = 'Closed';
            $locker->save();
        }
    
        return response()->json([
            'message' => 'Locker berhasil direset.',
        ], 200);
    }
    


    public function updateStatus(Request $request)
    {
        // Validasi input
        $request->validate([
            'LockerID' => 'required|integer',
            'Status' => 'required|string',
        ]);

        // Ambil locker berdasarkan ID
        $locker = Lockers::where('LockerID', $request->LockerID)->first();

        // Jika locker ditemukan, update statusnya
        if ($locker) {
            $locker->StatusLocker = $request->Status;
            $locker->save();

            return response()->json([
                'message' => 'Status locker berhasil diperbarui',
                'status' => $locker->StatusLocker,
            ]);
        } else {
            return response()->json([
                'message' => 'Locker tidak ditemukan',
            ], 404);
        }
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
