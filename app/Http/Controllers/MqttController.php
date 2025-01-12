<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Bluerhinos\phpMQTT;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\UserLocker;


class MqttController extends Controller
{
    // Server Mqtt  dan port
    protected $server = 'broker.hivemq.com';
    protected $port = 1883;
    protected $client_id;

    // Konstruktor untuk menginisialisasi client_id dengan nilai acak
    public function __construct()
    {
        $this->client_id = 'laravel_client_' . Str::random(10);
    }

    // Function untuk subscribe topik MQTT dan memproses message
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
        
        // Daftar topik yang akan di subscribe
        $topics['rfid'] = ['qos' => 0, 'function' => [$this, 'processRFID']];
        $topics['fingerprint/enroll'] = ['qos' => 0, 'function' => [$this, 'processFingerprint']];
        $mqtt->subscribe($topics, 0);

        echo "Processing MQTT messages...<br>";
        Log::info('Processing MQTT messages...');

        // Mulai proses pesan dengan batas waktu
        $startTime = time();
        $timeout = 290;

        while ($mqtt->proc()) {
            if (time() - $startTime > $timeout) {
                Log::warning('MQTT processing taking too long, breaking the loop');
                echo "MQTT processing taking too long, breaking the loop<br>";
                break;
            }
            usleep(500000); // Jeda 500ms untuk mengurasi beban CPU
        }

        $mqtt->close();
        Log::info('MQTT disconnected');
        echo "MQTT disconnected...<br>";

        return response()->json(['status' => 'success', 'message' => 'MQTT subscribed and processed']);
    }

    public function processMessage($topic, $msg)
    {
        Log::info("Message received on topic $topic: $msg");

        $response = "Received data: $msg";
        echo "Sending response back to Arduino: $response<br>";
        $this->sendResponse($response);
    }

    // Function untuk RFID
public function processRFID($topic, $msg)
{
    Log::info("RFID Message received on topic $topic: $msg");

    $rfidData = str_replace("RFID tag: ", "", $msg);
    $hashedRFIDdata = hash('sha256', $rfidData); // Hashing the RFID data

    // Cari user yang terhubung dengan RFID
    $user = UserLocker::whereNull('RFIDTag')->first();

    if (!$user) {
        Log::warning("No available user for RFID tag assignment");
        echo "No available user for RFID tag assignment<br>";
        $response = "No available user for RFID tag assignment";
    } else {
        // Perbarui data user dengan RFID
        $user->RFIDTag = $hashedRFIDdata;
        $user->save();

        Log::info("RFID tag assigned to user: {$user->Username}, RFID tag: $hashedRFIDdata");
        echo "RFID tag assigned to user: {$user->Username}, RFID tag: $hashedRFIDdata<br>";
        $response = "RFID tag assigned to user: {$user->Username}, RFID tag: $hashedRFIDdata";
    }

    echo "Sending response back to Arduino: $response<br>";
    $this->sendResponse($response);
}


    // Function untuk memproses Fingerprint
    public function processFingerprint($topic, $msg)
    {
        Log::info("Fingerprint Message received on topic $topic: $msg");
    
        $fingerprintId = $msg;
        $hashedFingerprintId = hash('sha256', $fingerprintId); // Hashing the fingerprint ID
    
        // Find the first user in UserLocker that does not have FingerprintData assigned
        $user = UserLocker::whereNull('FingerprintData')->first();
    
        if (!$user) {
            Log::warning("No user found in UserLocker table");
            echo "No user found in UserLocker table<br>";
            $response = "No user found in UserLocker table";
        } else {
            $user->FingerprintData = $hashedFingerprintId; // Store hashed ID instead
            $user->save();
    
            Log::info("Fingerprint ID updated for user: {$user->Username}, Fingerprint ID: $hashedFingerprintId");
            echo "Fingerprint ID updated for user: {$user->Username}, Fingerprint ID: $hashedFingerprintId<br>";
            $response = "Fingerprint ID updated for user: {$user->Username}, Fingerprint ID: $hashedFingerprintId";
        }
    
        echo "Sending response back to Arduino: $response<br>";
        $this->sendResponse($response);
    }


    protected function sendResponse($message)
    {
        echo "Sending response: $message<br>";
    }
}
