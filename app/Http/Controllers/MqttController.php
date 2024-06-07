<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Bluerhinos\phpMQTT;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Rfid;
use App\Models\UserLocker;

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

        $topics['fingerprint'] = ['qos' => 0, 'function' => [$this, 'processMessage']];
        $topics['rfid'] = ['qos' => 0, 'function' => [$this, 'processRFID']];
        $topics['keypad'] = ['qos' => 0, 'function' => [$this, 'processKeypad']]; // Subscribe to keypad topic
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

    public function processKeypad($topic, $msg)
{
    Log::info("Keypad Message received on topic $topic: $msg");

    // Process the message from keypad
    // Example: Extract data, validate, and update database if necessary

    // For example, if your message format is "Keypad code: <code>"
    $keypadCode = str_replace("Keypad code: ", "", $msg);

    // Find user based on the keypad code
    $user = UserLocker::where('KeypadCode', $keypadCode)->first();

    if (!$user) {
        Log::warning("No user found for Keypad code: $keypadCode");
        echo "No user found for Keypad code: $keypadCode<br>";
        $response = "No user found for Keypad code: $keypadCode";
    } else {
        // Update some data based on keypad input
        // Example: Update some flag or timestamp in the user's record
        $user->last_keypad_used_at = now(); // Example of updating a field
        $user->save();

        Log::info("Keypad code processed for user: {$user->Username}, Keypad code: $keypadCode");
        echo "Keypad code processed for user: {$user->Username}, Keypad code: $keypadCode<br>";
        $response = "Keypad code processed for user: {$user->Username}, Keypad code: $keypadCode";
    }

    echo "Sending response back to Arduino: $response<br>";
    $this->sendResponse($response);
}

    

    protected function sendResponse($message)
    {
        echo "Sending response: $message<br>";
    }
}
