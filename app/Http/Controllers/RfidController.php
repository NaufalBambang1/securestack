<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rfid;
use App\Models\UserLocker;

class RfidController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'rfid_tag' => 'required|string|max:255',
        ]);

        // Cari RFID tag dalam database
        $rfid = UserLocker::where('rfid_tag', $validatedData['rfid_tag'])->first();

        if ($rfid) {
            // Dapatkan informasi pengguna terkait jika ada
            $user = UserLocker::find($rfid->UserID);

            if ($user) {
                return response()->json([
                    'message' => 'RFID tag ditemukan',
                    'user' => $user
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Pengguna tidak ditemukan untuk RFID tag ini',
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'RFID tag tidak terdaftar',
            ], 404);
        }
    }
}
