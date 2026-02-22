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

    private array $publishQueue = [];

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
            ->setLastWillTopic(null)
            ->setLastWillMessage(null)
            ->setLastWillQualityOfService(0)
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

        $client->subscribe($topic, function (string $topic, string $message) use ($client) {
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
            $recordedAt = now('Asia/Jakarta');

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
                if (!$tandon->parent_id && $height <= $tandon->height_warning) {
                    Alert::create([
                        'tandon_id' => $tandon->id,
                        'type' => 'WARNING_LEVEL',
                        'message' => "Tandon {$tandon->building_name} water level is running low: {$height} m. Need to be filled soon.",
                        'triggered_at' => now('Asia/Jakarta'),
                    ]);
                    return;
                }
                // Check if water level is below minimum threshold
                if ($height <= $tandon->height_min) {
                    // Create alert record
                    Alert::create([
                        'tandon_id' => $tandon->id,
                        'type' => 'LOW_LEVEL',
                        'message' => "Tandon {$tandon->building_name} water level is low: {$height} m. If pumps is not running, please check the system.",
                        'triggered_at' => now('Asia/Jakarta'),
                    ]);
                    
                    // Check parent tank's latest height before activating pump
                    $parent = $tandon->parent;
                    if ($parent) {
                        $parentLatestReading = $parent->readings()->latest('recorded_at')->first();
                        if ($parentLatestReading && $parentLatestReading->water_height > $parent->height_min) {
                            $pumpTopic = $topicParts[0] . '/' . $topicParts[1] . '/water_pump';
                            try {
                                $client->publish($pumpTopic, '1', 0);
                                Log::info('mqtt.pump_activated', [
                                    'tandon' => $tandon->name,
                                    'height' => $height,
                                    'topic' => $pumpTopic,
                                    'action' => 'activate',
                                    'parent_id' => $parent->id,
                                    'parent_height' => $parentLatestReading->water_height,
                                ]);
                            } catch (\Throwable $publishError) {
                                Log::error('mqtt.pump_publish_failed', [
                                    'topic' => $pumpTopic,
                                    'action' => 'activate',
                                    'error' => $publishError->getMessage(),
                                ]);
                            }
                        }
                        // If parent's latest height is not above min, do nothing
                    } // If no parent, do nothing
                    return;
                }

                // Check if water level is above maximum threshold
                if ($height >= $tandon->height_max) {
                    // Publish pump deactivation directly
                    $pumpTopic = $topicParts[0] . '/' . $topicParts[1] . '/water_pump';
                    try {
                        $client->publish($pumpTopic, '0', 0);
                        Log::info('mqtt.pump_deactivated', [
                            'tandon' => $tandon->name,
                            'height' => $height,
                            'topic' => $pumpTopic,
                            'action' => 'deactivate',
                        ]);
                    } catch (\Throwable $publishError) {
                        Log::error('mqtt.pump_publish_failed', [
                            'topic' => $pumpTopic,
                            'action' => 'deactivate',
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

        $this->info('Listening on '.$topic.' with QoS '.$qos);

        try {
            $client->loop(true); // Blocking loop - process all incoming messages
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
