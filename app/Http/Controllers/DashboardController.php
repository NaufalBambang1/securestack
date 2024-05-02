<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLocker;
use App\Models\Lockers;
use App\Models\AccessLog;
use App\Models\Rfid;
use App\Models\Keypad;
use App\Models\Fingerprint;

class DashboardController extends Controller
{
    public function index(){
        // $usernames = UserLocker::pluck('username');
        // $lockerInformation = Lockers::pluck('lockerNumber','StatusLocker');
        // $accesslogInformation = AccessLog::all();

        // SELECT LogID, Username, lockerNumber, StatusLocker, AccessTime, AccessMethod, AccessResult
        // from access_logs
        // Join users_locker on access_logs.UserID = users_locker.UserID
        // join lockers on access_logs.LockerID = lockers.LockerID
        
        $data = AccessLog::select('LogID','users_locker.username','lockers.lockerNumber','lockers.StatusLocker','access_logs.AccessTime','access_logs.AccessMethod','access_logs.AccessResult')
                        ->join('users_locker','access_logs.UserID', '=', 'users_locker.UserID')
                        ->join('lockers','access_logs.LockerID','=', 'lockers.LockerID')
                        ->get();

        return view('dashboard', compact('data'));
    }
}
