<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\Tandon;
use App\Models\TandonReading;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\MqttClient;

class MqttListen extends Command
{
    protected $signature = 'mqtt:listen {--qos=1 : QoS level (0,1,2)}';

    protected $description = 'Listen to MQTT topics for tandon readings';

    public function handle(): int
    {
        $config = config('services.mqtt');

        $client = new MqttClient(
            $config['host'],
            (int) $config['port'],
            $config['client_id']
        );

        $settings = (new ConnectionSettings())
            ->setUsername($config['username'] ?? null)
            ->setPassword($config['password'] ?? null)
            ->setKeepAliveInterval(60)
            ->setUseTls((bool) ($config['use_tls'] ?? false));

        try {
            $client->connect($settings, true);
        } catch (MqttClientException $e) {
            $this->error('Failed to connect to MQTT broker: '.$e->getMessage());
            Log::error('mqtt.connect_failed', ['message' => $e->getMessage()]);

            return self::FAILURE;
        }

        $topic = $config['topic'];
        $qos = (int) $this->option('qos');

        $this->info("Subscribed to {$topic} with QoS {$qos}");

        $client->subscribe($topic, function (string $topic, string $message) {
            // Payload adalah plain text nilai water_height
            $height = (float) trim($message);

            if ($height === 0.0 && $message !== '0' && $message !== '0.0') {
                Log::warning('mqtt.invalid_height_value', ['topic' => $topic, 'message' => $message]);
                return;
            }

            // Extract client_id from topic: project_id/client_id/topic_code
            $topicParts = explode('/', $topic);
            if (count($topicParts) < 2) {
                Log::warning('mqtt.invalid_topic_format', ['topic' => $topic]);
                return;
            }

            $clientId = $topicParts[1]; // Extract client_id (device_id)
            $recordedAt = now();

            // Find tandon by client_id (assuming id column or custom device_id column)
            $tandon = Tandon::where('name', $clientId)->first();
            if (!$tandon) {
                Log::warning('mqtt.tandon_not_found', [
                    'client_id' => $clientId,
                    'topic' => $topic,
                ]);
                return;
            }

            // Calculate volume: height × area
            $area = (float) $tandon->cross_section_area;
            $volume = $height * $area;

            try {
                TandonReading::create([
                    'tandon_id' => $tandon->id,
                    'water_height' => $height,
                    'water_volume' => $volume,
                    'recorded_at' => $recordedAt,
                ]);

                // Check if water level is below minimum threshold
                if ($height <= $tandon->height_min) {
                    // Create alert record
                    Alert::create([
                        'tandon_id' => $tandon->id,
                        'type' => 'LOW_LEVEL',
                        'message' => "Tandon {$tandon->building_name} water level is low: {$height} m. If pumps is not running, please check the system.",
                        'triggered_at' => now(),
                    ]);

                    // Send pump control message via MQTT
                    $pumpTopic = $topicParts[0] . '/' . $topicParts[1] . '/water_pump';
                    try {
                        $client->publish($pumpTopic, '1', 0); // Send '1' to activate pump
                        Log::info('mqtt.pump_activated', [
                            'tandon' => $tandon->name,
                            'height' => $height,
                            'topic' => $pumpTopic,
                        ]);
                    } catch (\Throwable $publishError) {
                        Log::error('mqtt.pump_publish_failed', [
                            'topic' => $pumpTopic,
                            'error' => $publishError->getMessage(),
                        ]);
                    }
                }

                // Check if water level is above maximum threshold
                if ($height >= $tandon->height_max) {
                    // Send pump deactivation message via MQTT
                    $pumpTopic = $topicParts[0] . '/' . $topicParts[1] . '/water_pump';
                    try {
                        $client->publish($pumpTopic, '0', 0); // Send '0' to deactivate pump
                        Log::info('mqtt.pump_deactivated', [
                            'tandon' => $tandon->name,
                            'height' => $height,
                            'topic' => $pumpTopic,
                        ]);
                    } catch (\Throwable $publishError) {
                        Log::error('mqtt.pump_publish_failed', [
                            'topic' => $pumpTopic,
                            'error' => $publishError->getMessage(),
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                Log::error('mqtt.persist_failed', [
                    'topic' => $topic,
                    'height' => $height,
                    'error' => $e->getMessage(),
                ]);
            }
        }, $qos);

        try {
            $client->loop(true);
        } catch (MqttClientException $e) {
            $this->error('MQTT connection error: '.$e->getMessage());
            Log::error('mqtt.loop_error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error('Unexpected error in MQTT loop: '.$e->getMessage());
            Log::error('mqtt.unexpected_error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
