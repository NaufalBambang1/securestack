<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RegisterlockerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MqttController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/registernewuser',[RegisterlockerController::class,'index']);
Route::get('/addlocker',function(){
    return view('auth.add-locker');
});
Route::get('/userprofile',function(){
    return view('profile.edit-user-locker');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])
    ->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
Route::get('/viewdetail/{logID}', [DashboardController::class, 'indexViewData'])->name('viewdata');
Route::get('/access/fingerprint', [DashboardController::class, 'accessWithFingerprint']);
Route::get('/access/rfid', [DashboardController::class, 'accessWithRfid']);
Route::post('/access/fingerprint', [DashboardController::class, 'accessWithFingerprint']);
Route::post('/access/rfid', [DashboardController::class, 'accessWithRfid']);
Route::get('/resetButton',[DashboardController::class, 'resetButton'])->name('resetButton');
Route::get('/locker/updateStatus', [DashboardController::class, 'updateStatus']);
Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



use App\Http\Controllers\LockerController;

Route::get('/register-user', [LockerController::class, 'showRegisterUserForm'])->name('register.user.form');
Route::post('/register-user', [LockerController::class, 'registerUser'])->name('register.user');
Route::get('/register-rfid', [LockerController::class, 'showRegisterRfidForm'])->name('register.rfid.form');
Route::post('/register-rfid', [LockerController::class, 'registerRfid'])->name('register.rfid');
Route::get('/register-fingerprint', [LockerController::class, 'showRegisterFingerprintForm'])->name('register.fingerprint.form');
Route::post('/register-fingerprint', [LockerController::class, 'registerFingerprint'])->name('register.fingerprint');

Route::get('/subscribe', [MqttController::class, 'subscribe']);
Route::post('/register/rfid', [MqttController::class, 'registerRfid'])->name('register.rfid');

require __DIR__.'/auth.php';
