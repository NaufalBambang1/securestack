<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Bluerhinos\phpMQTT;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // Import Str facade
use App\Models\RFID;
use App\Models\User; // Import User model

class MqttController extends Controller
{
    protected $server = 'broker.hivemq.com'; // MQTT broker address
    protected $port = 1883; // MQTT broker port
    protected $client_id;

    public function __construct()
    {
        $this->client_id = 'laravel_client_' . Str::random(10);
    }

    public function subscribe()
    {
        set_time_limit(300); // Set maximum execution time to 300 seconds

        echo "Starting MQTT connection...<br>";

        $mqtt = new phpMQTT($this->server, $this->port, $this->client_id);

        if (!$mqtt->connect()) {
            Log::error('MQTT connection failed');
            echo "MQTT connection failed<br>";
            return response()->json(['status' => 'error', 'message' => 'MQTT connection failed']);
        }

        Log::info('MQTT connected');
        echo "MQTT connected...<br>";

        $topics['fingerprint'] = array('qos' => 0, 'function' => [$this, 'processMessage']); // Adjust the topic as needed
        $topics['rfid'] = array('qos' => 0, 'function' => [$this, 'processRFID']); // Adjust the topic as needed
        $mqtt->subscribe($topics, 0);

        echo "Processing MQTT messages...<br>";
        Log::info('Processing MQTT messages...');

        $startTime = time();
        $timeout = 290; // Set a timeout period, e.g., 290 seconds

        while ($mqtt->proc()) {
            // Check if the loop has been running for too long
            if (time() - $startTime > $timeout) {
                Log::warning('MQTT processing taking too long, breaking the loop');
                echo "MQTT processing taking too long, breaking the loop<br>";
                break;
            }

            // Simulate some delay to avoid a tight loop, if needed
            usleep(500000); // Sleep for 0.5 seconds
        }

        $mqtt->close();
        Log::info('MQTT disconnected');
        echo "MQTT disconnected...<br>";

        return response()->json(['status' => 'success', 'message' => 'MQTT subscribed and processed']);
    }

    public function processMessage($topic, $msg)
    {
        Log::info("Message received on topic $topic: $msg");

        if ($topic == 'fingerprint') {
            $fingerprintData = (int) str_replace("Fingerprint data: ", "", $msg);
            echo "Fingerprint data saved: $fingerprintData<br>";
            // Save fingerprint data logic goes here if needed
        }

        // Respond to the Arduino
        $response = "Received data: $msg";
        echo "Sending response back to Arduino: $response<br>";
        $this->sendResponse($response);
    }

    public function processRFID($topic, $msg)
    {
        Log::info("RFID Message received on topic $topic: $msg");

        $rfidData = str_replace("RFID tag: ", "", $msg);

        // Find the user associated with this RFID tag
        $user = User::where('rfid_tags', $rfidData)->first();

        if ($user) {
            RFID::create([
                'user_id' => $user->id,
                'rfid_tags' => $rfidData,
            ]);
            echo "RFID data saved: $rfidData for user: $user->id<br>";
        } else {
            Log::error("User not found for RFID tag: $rfidData");
        }

        // Respond to the Arduino
        $response = "Received RFID data: $rfidData";
        echo "Sending response back to Arduino: $response<br>";
        $this->sendResponse($response);
    }

    protected function sendResponse($message)
    {
        // Here you can send a response back to the Arduino via MQTT if needed
        // For demonstration purposes, we will just print the message
        echo "Sending response: $message<br>";
    }
}
