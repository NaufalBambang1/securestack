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
        $topics['fingerprint/enroll'] = ['qos' => 0, 'function' => [$this, 'processFingerprint']];
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

        $response = "Received data: $msg";
        echo "Sending response back to Arduino: $response<br>";
        $this->sendResponse($response);
    }

    public function processRFID($topic, $msg)
    {
        Log::info("RFID Message received on topic $topic: $msg");

        $rfidData = str_replace("RFID tag: ", "", $msg);

        $rfid = Rfid::where('RFIDTag', $rfidData)->first();

        if (!$rfid) { 
            $rfid = Rfid::create([
                'RFIDTag' => $rfidData 
            ]);
        }

        $user = UserLocker::where('RFIDTag', $rfidData)->first();

        if (!$user) {
            $user = UserLocker::whereNull('RFIDTag')->first();
        }

        if (!$user) {
            Log::warning("No user found for RFID tag: $rfidData");
            echo "No user found for RFID tag: $rfidData<br>";
            $response = "No user found for RFID tag: $rfidData";
        } else {
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

        $fingerprintId = $msg;

        $fingerprint = Fingerprint::where('FingerprintData', $fingerprintId)->first();

        if (!$fingerprint) {
            $fingerprint = Fingerprint::create([
                'FingerprintData' => $fingerprintId
            ]);
        }

        $user = UserLocker::where('FingerprintData', $fingerprintId)->first();

        if (!$user) {
            $user = UserLocker::whereNull('FingerprintData')->first();
        }

        if (!$user) {
            Log::warning("No user found for Fingerprint ID: $fingerprintId");
            echo "No user found for Fingerprint ID: $fingerprintId<br>";
            $response = "No user found for Fingerprint ID: $fingerprintId";
        } else {
            $user->FingerprintData = $fingerprintId;
            $user->fingerprint_id = $fingerprint->id;
            $user->save();

            Log::info("Fingerprint ID updated for user: {$user->Username}, Fingerprint ID: $fingerprintId");
            echo "Fingerprint ID updated for user: {$user->Username}, Fingerprint ID: $fingerprintId<br>";
            $response = "Fingerprint ID updated for user: {$user->Username}, Fingerprint ID: $fingerprintId";
        }

        echo "Sending response back to Arduino: $response<br>";
        $this->sendResponse($response);
    }

    public function publishFingerprintData()
    {
        $mqtt = new phpMQTT($this->server, $this->port, $this->client_id);

        if (!$mqtt->connect()) {
            Log::error('MQTT connection failed');
            return response()->json(['status' => 'error', 'message' => 'MQTT connection failed']);
        }

        Log::info('MQTT connected for publishing');

        $fingerprints = Fingerprint::all();
        
        foreach ($fingerprints as $fingerprint) {
            $payload = json_encode(['fingerprint_id' => $fingerprint->FingerprintData]);
            $mqtt->publish('fingerprint/read', $payload, 0);
            Log::info("Published fingerprint data: $payload");
        }

        $mqtt->close();
        Log::info('MQTT disconnected after publishing');

        return response()->json(['status' => 'success', 'message' => 'Fingerprint data published to MQTT']);
    }

    protected function sendResponse($message)
    {
        echo "Sending response: $message<br>";
    }
}
