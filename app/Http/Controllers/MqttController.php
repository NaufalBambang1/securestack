<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Bluerhinos\phpMQTT;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Rfid;
use App\Models\UserLocker;
use App\Models\Fingerprint;

class MqttController extends Controller
{
    protected $server = 'broker.hivemq.com';
    protected $port = 1883;
    protected $client_id;

    public function __construct()
    {
        $this->client_id = 'laravel_client_' . Str::random(10);
    }

    public function subscribe()
    {
        set_time_limit(300);

        echo "Starting MQTT connection...<br>";

        $mqtt = new phpMQTT($this->server, $this->port, $this->client_id);

        if (!$mqtt->connect()) {
            Log::error('MQTT connection failed');
            echo "MQTT connection failed<br>";
            return response()->json(['status' => 'error', 'message' => 'MQTT connection failed']);
        }

        Log::info('MQTT connected');
        echo "MQTT connected...<br>";

        $topics['rfid'] = ['qos' => 0, 'function' => [$this, 'processRFID']];
        $topics['fingerprint/enroll'] = ['qos' => 0, 'function' => [$this, 'processFingerprint']]; // New function
        $mqtt->subscribe($topics, 0);

        echo "Processing MQTT messages...<br>";
        Log::info('Processing MQTT messages...');

        $startTime = time();
        $timeout = 290;

        while ($mqtt->proc()) {
            if (time() - $startTime > $timeout) {
                Log::warning('MQTT processing taking too long, breaking the loop');
                echo "MQTT processing taking too long, breaking the loop<br>";
                break;
            }
            usleep(500000);
        }

        $mqtt->close();
        Log::info('MQTT disconnected');
        echo "MQTT disconnected...<br>";

        return response()->json(['status' => 'success', 'message' => 'MQTT subscribed and processed']);
    }

    public function processMessage($topic, $msg)
    {
        Log::info("Message received on topic $topic: $msg");

        // Process the message based on the topic
        // Example: Log the message or take some action based on the topic

        // Respond to the Arduino
        $response = "Received data: $msg";
        echo "Sending response back to Arduino: $response<br>";
        $this->sendResponse($response);
    }




    public function processRFID($topic, $msg)
    {
        Log::info("RFID Message received on topic $topic: $msg");
    
        $rfidData = str_replace("RFID tag: ", "", $msg);
    
        // Cari RFID tag yang cocok di tabel rfids
        $rfid = Rfid::where('RFIDTag', $rfidData)->first();
    
        // Jika tidak ada, buat baru
        if (!$rfid) { 
            $rfid = Rfid::create([
                'RFIDTag' => $rfidData 
            ]);
        }
    
        // Cari user yang memiliki RFIDTag yang cocok
        $user = UserLocker::where('RFIDTag', $rfidData)->first();
    
        if (!$user) {
            // Cari user yang memiliki RFIDTag NULL
            $user = UserLocker::whereNull('RFIDTag')->first();
        }
    
        if (!$user) {
            Log::warning("No user found for RFID tag: $rfidData");
            echo "No user found for RFID tag: $rfidData<br>";
            $response = "No user found for RFID tag: $rfidData";
        } else {
            // Jika ditemukan user, update RFIDTag dan rfid_id
            $user->RFIDTag = $rfidData;
            $user->rfid_id = $rfid->id;
            $user->save();
    
            if ($user->wasRecentlyCreated) {
                Log::info("RFID tag updated for new user: {$user->Username}, RFID tag: $rfidData");
                echo "RFID tag updated for new user: {$user->Username}, RFID tag: $rfidData<br>";
                $response = "RFID tag updated for new user: {$user->Username}, RFID tag: $rfidData";
            } else {
                Log::info("RFID tag updated for user: {$user->Username}, RFID tag: $rfidData");
                echo "RFID tag updated for user: {$user->Username}, RFID tag: $rfidData<br>";
                $response = "RFID tag updated for user: {$user->Username}, RFID tag: $rfidData";
            }
        }
    
        echo "Sending response back to Arduino: $response<br>";
        $this->sendResponse($response);
    }

    public function processFingerprint($topic, $msg)
    {
        Log::info("Fingerprint Message received on topic $topic: $msg");
    
        // Assuming Arduino sends the fingerprint ID as a number
        $fingerprintId = $msg;

        $fingerprint = Fingerprint::where('FingerprintData',$fingerprintId)->first();

        if(!$fingerprint){
            $fingerprint = Fingerprint::create([
                'FingerprintData' => $fingerprintId
            ]);
        }
        
        // Find the user based on the fingerprint ID
        $user = UserLocker::where('FingerprintData', $fingerprintId)->first();

        if (!$user) {
            // Cari user yang memiliki RFIDTag NULL
            $user = UserLocker::whereNull('FingerprintData')->first();
        }

        if (!$user) {
            // If user not found, log a warning
            Log::warning("No user found for Fingerprint ID: $fingerprintId");
            echo "No user found for Fingerprint ID: $fingerprintId<br>";
            $response = "No user found for Fingerprint ID: $fingerprintId";
        } else {
            // Update user data with the fingerprint ID and save
            $user->FingerprintData = $fingerprintId;
            $user->fingerprint_id = $fingerprint->id;
            $user->save();
    
            // Log the successful update
            Log::info("Fingerprint ID updated for user: {$user->Username}, Fingerprint ID: $fingerprintId");
            echo "Fingerprint ID updated for user: {$user->Username}, Fingerprint ID: $fingerprintId<br>";
            $response = "Fingerprint ID updated for user: {$user->Username}, Fingerprint ID: $fingerprintId";
        }
    
        // Send response back to Arduino
        echo "Sending response back to Arduino: $response<br>";
        $this->sendResponse($response);
    }
    
    

    

    

    protected function sendResponse($message)
    {
        echo "Sending response: $message<br>";
    }
}


    // public function processRFID($topic, $msg)
    // {
    //     Log::info("RFID Message received on topic $topic: $msg");

    //     $rfidData = str_replace("RFID tag: ", "", $msg);
        
    //     $rfid = Rfid::where('RFIDTag', $rfidData)->first();

    //     if (!$rfid) { 
    //         $dataRfid = [
    //             'RFIDTag' => $rfidData 
    //         ];
    //         Rfid::create($dataRfid);
    //     } else {
    //             Log::warning("RFID tag already exists for user:, RFID tag: $rfidData");
    //             echo "RFID tag already exists for user:, RFID tag: $rfidData<br>";
    //     }
           
    //     // $user = Rfid::where('RFIDTag', $rfidData)->first();

    //     // if ($user) {
    //     //     UserLocker::create([
    //     //         'UserID' => $user->UserID,
    //     //         'RFIDTag' => $rfidData,
    //     //     ]);
    //     //     echo "RFID data saved: $rfidData for user: $user->UserID<br>";
    //     // } else {
    //     //     Log::error("User not found for RFID tag: $rfidData");
    //     // }

    //     $response = "Received RFID data: $rfidData";
    //     echo "Sending response back to Arduino: $response<br>";
    //     $this->sendResponse($response);
    // }
