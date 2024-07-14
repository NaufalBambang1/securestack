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
            'users_locker.lockerNumber',
            'access_logs.StatusLocker',
            'access_logs.AccessMethodFingerprint',
            'access_logs.AccessResultFingerprint',
            'access_logs.AccessTimeFingerprint',
            'access_logs.failed_attempts_fingerprint',
            'access_logs.AccessMethod',
            'access_logs.AccessResult',
            'access_logs.AccessTime',
            'access_logs.failed_attempts_rfid',
            'access_logs.UserLockerID'
        )
            ->join('users_locker', 'access_logs.UserLockerID', '=', 'users_locker.UserLockerID')
            ->where('access_logs.UserLockerID', $UserLockerID)
            ->get();

        return view('viewdata', compact('dataView'));
    }
    public function accessWithFingerprint(Request $request)
    {
        // Mendapatkan ID sidik jari dari request
        $fingerprint_id = $request->FingerprintData;

        // Memeriksa apakah locker dengan LockerID yang diberikan ada
        $userLocker = UserLocker::where('UserLockerID', $request->LockerID)->first();
        $failed_attempts_fingerprint = $userLocker->Attempts_fingerprint;

        if($userLocker){
           
            $accessLog = new AccessLog();

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
                $failed_attempts_fingerprint++;
                $userLocker->Attempts_fingerprint = $failed_attempts_fingerprint;
                $userLocker->save();

                // Check if failed attempts reach 5 and block locker
                if ($failed_attempts_fingerprint >= 5) {
                    $accessLog->StatusLocker = 'Blocked';
                }else{
                    $accessLog->StatusLocker = 'Closed';
                }

            } else {
                // Reset failed attempts if access is granted
                $failed_attempts_fingerprint = 0;
                $userLocker->Attempts_fingerprint = $failed_attempts_fingerprint;
                $userLocker->save();
                $accessLog->StatusLocker = 'Closed';
            }

            
            $accessLog->UserLockerID = $userLocker->UserLockerID;
            $accessLog->AccessMethodFingerprint = $accessMethod;
            $accessLog->AccessResultFingerprint = $accessResult;
            $accessLog->failed_attempts_fingerprint = $failed_attempts_fingerprint;
            $accessLog->AccessTimeFingerprint = now();

            $accessLog->save();
    
            return response()->json([
                'message' => 'Log akses berhasil dibuat atau diperbarui.',
                'accessResultFingerprint' => $accessResult,
                'failedAttemptsFingerprint' => $failed_attempts_fingerprint,
                'lockerStatus' => $accessLog->StatusLocker
            ], 200);
            
        }else{
            return response()->json(['error' => 'User locker tidak ditemukan.'], 404);
        }
    }

    public function accessWithRfid(Request $request)
    {
        $rfid_id = $request->RFIDTag;
        
        $userLocker = UserLocker::where('UserLockerID', $request->LockerID)->first();
        $failed_attempts_rfid = $userLocker->Attempts_rfid;

        if($userLocker){

            $accessLog = new AccessLog();
            
            
            if ($rfid_id != $userLocker->RFIDTag) {
                $accessResult = 'Ditolak';
                $accessMethod = 'RFID';
            } else {
                $accessResult = 'Diterima';
                $accessMethod = 'RFID';
            }

            if ($accessResult == 'Ditolak') {
                $failed_attempts_rfid++;
                $userLocker->Attempts_rfid = $failed_attempts_rfid;
                $userLocker->save();
                // Check if failed attempts reach 5 and block locker
                if ($failed_attempts_rfid >= 5) {
                    $accessLog->StatusLocker = 'Blocked';
                }else{
                    $accessLog->StatusLocker = 'Closed';
                }

            } else if ($accessResult == 'Diterima') {
                $accessLog->StatusLocker = 'Opened';
            }else{
                // Reset failed attempts if access is granted
                $failed_attempts_rfid = 0;
                $userLocker->Attempts_rfid = $failed_attempts_rfid;
                $userLocker->save();
                $accessLog->StatusLocker = 'Closed';
            }

            
            $accessLog->UserLockerID = $userLocker->UserLockerID;
            $accessLog->AccessMethod = $accessMethod;
            $accessLog->AccessResult = $accessResult;
            $accessLog->failed_attempts_rfid = $failed_attempts_rfid;
            $accessLog->AccessTime = now();

            $accessLog->save();
        

            return response()->json([
                'message' => 'Log akses berhasil dibuat atau diperbarui.',
                'accessResultRFID' => $accessResult,
                'failedAttemptsRfid' => $failed_attempts_rfid,
                'lockerStatus' => $accessLog->StatusLocker
            ], 200);

        }else{
            return response()->json(['error' => 'User locker tidak ditemukan.'], 404);
        }   
    }

    // private function blockLocker($locker_id)
    // {
    //     $accesslog = AccessLog::where('UserLockerID', $locker_id)->first();
        
    //     if ($accesslog) {
    //         $accesslog->StatusLocker = 'Blocked';
    //         $accesslog->save();
    //         return response()->json(['message' => 'Status locker berhasil diblokir.'], 200);
    //     } else {
    //         return response()->json(['error' => 'Locker tidak ditemukan.2'], 404);
    //     }
    // }

    public function resetButton(Request $request)
    {
        $LockerID = $request->input('lockerID');
        
        $accesslog = AccessLog::where('UserLockerID', $request->LockerID)
                ->orderBy('created_at', 'desc')
                ->first();
        $userlocker = UserLocker::where('UserLockerID',  $request->LockerID)->first();
        if ($accesslog) {   
            if($accesslog->StatusLocker == 'Blocked'){
                $accessLog = new AccessLog();
                $accessLog->UserLockerID = $request->LockerID;
                $accesslog->StatusLocker = 'Closed';

                $accesslog->failed_attempts_fingerprint = 0;
                $accesslog->failed_attempts_rfid = 0;
                $accesslog->save();
                
                $userlocker->Attempts_fingerprint = 0;
                $userlocker->Attempts_rfid = 0;
                $userlocker->save();

                return response()->json([
                    'message' => 'Locker berhasil direset.',
                ], 200);
            }
            else{
                return response()->json([
                    'message' => 'Locker gagal direset',
                ], 200);
            }
        }
    }
    
    public function updateStatus(Request $request)
    {
        // Validasi input
        $request->validate([
            'LockerID' => 'required|integer',
            'Status' => 'required|string',
        ]);
        $accesslog = AccessLog::where('UserLockerID', $request->LockerID)
                ->orderBy('created_at', 'desc')
                ->first();

        if ($accesslog) {   
            $accesslog->StatusLocker = $request->Status;
            $accesslog->save();
        }
       
        return response()->json([
            'message' => 'Status locker berhasil diperbarui',
            'status' => $accesslog->StatusLocker,
        ]);
    }

    public function getData()
    {
        $data = AccessLog::select(
            'users_locker.UserLockerID',
            'users_locker.Username',
            'users_locker.lockerNumber',
            DB::raw('(SELECT access_logs.StatusLocker FROM access_logs WHERE access_logs.UserLockerID = users_locker.UserLockerID ORDER BY access_logs.LogID DESC LIMIT 1) AS StatusLocker'),
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(IF(access_logs.AccessMethodFingerprint = "Fingerprint", access_logs.AccessResultFingerprint, NULL) ORDER BY access_logs.LogID DESC), ",", 1) AS AccessResultFingerprint'),
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(IF(access_logs.AccessMethodFingerprint = "Fingerprint", access_logs.AccessTimeFingerprint, NULL) ORDER BY access_logs.LogID DESC), ",", 1) AS AccessTimeFingerprint'),
            DB::raw('MAX(IF(access_logs.AccessMethodFingerprint = "Fingerprint", "Fingerprint", NULL)) AS AccessMethodFingerprint'),
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(IF(access_logs.AccessMethod = "RFID", access_logs.AccessResult, NULL) ORDER BY access_logs.LogID DESC), ",", 1) AS AccessResult'),
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(IF(access_logs.AccessMethod = "RFID", access_logs.AccessTime, NULL) ORDER BY access_logs.LogID DESC), ",", 1) AS AccessTime'),
            DB::raw('MAX(IF(access_logs.AccessMethod = "RFID", "RFID", NULL)) AS AccessMethod')
        )
        ->join('users_locker', 'access_logs.UserLockerID', '=', 'users_locker.UserLockerID')
        ->groupBy('users_locker.UserLockerID', 'users_locker.Username', 'users_locker.lockerNumber')
        ->get();

    
        return response()->json($data);
    }
    
}
