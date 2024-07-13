<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLocker;
use App\Models\Lockers;
use App\Models\AccessLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $port = 8000;
    public function index()
    {

        return view('dashboard');
    }

    public function indexViewData($UserLockerID)
    {
        $dataView = AccessLog::select(
            'access_logs.LogID',
            'users_locker.Username',
            'lockers.lockerNumber',
            'lockers.StatusLocker',
            'access_logs.AccessMethodFingerprint',
            'access_logs.AccessResultFingerprint',
            'access_logs.AccessTimeFingerprint',
            'users_locker.failed_attempts_fingerprint',
            'access_logs.AccessMethod',
            'access_logs.AccessResult',
            'access_logs.AccessTime',
            'users_locker.failed_attempts_rfid',
            'access_logs.UserLockerID'
        )
            ->join('users_locker', 'access_logs.UserLockerID', '=', 'users_locker.UserLockerID')
            ->join('lockers', 'users_locker.UserLockerID', '=', 'lockers.UserLockerID')
            ->where('access_logs.UserLockerID', $UserLockerID)
            ->get();

        return view('viewdata', compact('dataView'));
    }
    public function accessWithFingerprint(Request $request)
    {
        // Mendapatkan ID sidik jari dari request
        $fingerprint_id = $request->FingerprintData;

        // Memeriksa apakah locker dengan LockerID yang diberikan ada
        $locker = Lockers::where('UserLockerID', $request->LockerID)->first();
        
        if($locker){
            $userLocker = $locker->userLocker;

            if($userLocker){
                // Membandingkan data sidik jari langsung dengan data di $userLocker
                if($fingerprint_id != $userLocker->FingerprintData){
                    $accessResult = 'Ditolak';
                    $accessMethod = 'Fingerprint';
                } else {
                    $accessResult = 'Diterima';
                    $accessMethod = 'Fingerprint';
                }

                // Check if access is denied and increment failed attempts
                if ($accessResult == 'Ditolak') {
                    $userLocker->failed_attempts_fingerprint += 1;
                    $userLocker->save();

                    // Check if failed attempts reach 5 and block locker
                    if ($userLocker->failed_attempts_fingerprint >= 5) {
                        $this->blockLocker($lockerid->LockerID);
                    }

                } else {
                    // Reset failed attempts if access is granted
                    $userLocker->failed_attempts_fingerprint = 0;
                    $userLocker->save();
                }

                $accessLog = new AccessLog();
                $accessLog->UserLockerID = $userLocker->UserLockerID;
                $accessLog->AccessMethodFingerprint = $accessMethod;
                $accessLog->AccessResultFingerprint = $accessResult;
                $accessLog->AccessTimeFingerprint = now();

                $accessLog->save();
        
                return response()->json([
                    'message' => 'Log akses berhasil dibuat atau diperbarui.',
                    'accessResultFingerprint' => $accessResult,
                    'failedAttemptsFingerprint' => $userLocker->failed_attempts_fingerprint,
                    'lockerStatus' => $locker->StatusLocker
                ], 200);
                
            }else{
                return response()->json(['error' => 'User locker tidak ditemukan.'], 404);
            }
        }else{
            return response()->json(['error' => 'locker tidak ditemukan.'], 404);
        }
        
        
    }

    public function accessWithRfid(Request $request)
    {
        $rfid_id = $request->RFIDTag;
        
        $locker = Lockers::where('UserLockerID', $request->LockerID)->first();

        if($locker){
            $userLocker = $locker->userLocker;

            if($userLocker){
                if ($rfid_id != $userLocker->RFIDTag) {
                    $accessResult = 'Ditolak';
                    $accessMethod = 'RFID';
                } else {
                    $accessResult = 'Diterima';
                    $accessMethod = 'RFID';
                }

                if ($accessResult == 'Ditolak') {
                    $userLocker->failed_attempts_rfid += 1;
                    $userLocker->save();
    
                    // Check if failed attempts reach 5 and block locker
                    if ($userLocker->failed_attempts_rfid >= 5) {
                        $this->blockLocker($lockerid->LockerID);
                    }
    
                } else if ($accessResult == 'Diterima') {
                    $locker->StatusLocker = 'Opened';
                    $locker->save();
                }else{
                    // Reset failed attempts if access is granted
                    $userLocker->failed_attempts_rfid = 0;
                    $userLocker->save();
                }

                $accessLog = new AccessLog();
                $accessLog->UserLockerID = $userLocker->UserLockerID;
                $accessLog->AccessMethod = $accessMethod;
                $accessLog->AccessResult = $accessResult;
                $accessLog->AccessTime = now();

                $accessLog->save();
            

                return response()->json([
                    'message' => 'Log akses berhasil dibuat atau diperbarui.',
                    'accessResultRFID' => $accessResult,
                    'failedAttemptsRfid' => $userLocker->failed_attempts_rfid,
                    'lockerStatus' => $locker->StatusLocker
                ], 200);

            }else{
                return response()->json(['error' => 'User locker tidak ditemukan.'], 404);
            }
        }else{
            return response()->json(['error' => 'User locker tidak ditemukan.'], 404);
        }

        
    }

    private function blockLocker($locker_id)
    {
        $locker = Lockers::where('UserLockerID', $locker_id)->first();
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
        $LockerID = $request->input('lockerID');
        // Cari data user locker berdasarkan LockerID
        $lockerid = Lockers::where('UserLockerID', $request->LockerID)->first();
        if($lockerid){
            $userLocker = $lockerid->userLocker;

            $userLocker->failed_attempts_fingerprint = 0;
            $userLocker->failed_attempts_rfid = 0;
            $userLocker->save();

            $lockerid->StatusLocker = 'Closed';
            $lockerid->save();
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
        $locker = Lockers::where('UserLockerID', $request->LockerID)->first();

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

    public function getData()
    {
        $data = AccessLog::select(
            'lockers.UserLockerID',
            'users_locker.Username',
            'lockers.lockerNumber',
            'lockers.StatusLocker',
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(IF(access_logs.AccessMethodFingerprint = "Fingerprint", access_logs.AccessResultFingerprint, NULL) ORDER BY access_logs.LogID DESC), ",", 1) AS AccessResultFingerprint'),
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(IF(access_logs.AccessMethodFingerprint = "Fingerprint", access_logs.AccessTimeFingerprint, NULL) ORDER BY access_logs.LogID DESC), ",", 1) AS AccessTimeFingerprint'),
            DB::raw('MAX(IF(access_logs.AccessMethodFingerprint = "Fingerprint", "Fingerprint", NULL)) AS AccessMethodFingerprint'),
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(IF(access_logs.AccessMethod = "RFID", access_logs.AccessResult, NULL) ORDER BY access_logs.LogID DESC), ",", 1) AS AccessResult'),
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(IF(access_logs.AccessMethod = "RFID", access_logs.AccessTime, NULL) ORDER BY access_logs.LogID DESC), ",", 1) AS AccessTime'),
            DB::raw('MAX(IF(access_logs.AccessMethod = "RFID", "RFID", NULL)) AS AccessMethod')
        )
        ->join('users_locker', 'access_logs.UserLockerID', '=', 'users_locker.UserLockerID')
        ->join('lockers', 'users_locker.UserLockerID', '=', 'lockers.UserLockerID')
        ->groupBy('lockers.UserLockerID', 'users_locker.Username', 'lockers.lockerNumber')
        ->get();

    
        return response()->json($data);
    }
    
}
