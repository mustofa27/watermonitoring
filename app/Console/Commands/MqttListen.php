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

                    // Queue pump control message instead of publishing directly
                    $pumpTopic = $topicParts[0] . '/' . $topicParts[1] . '/water_pump';
                    $this->publishQueue[] = [
                        'topic' => $pumpTopic,
                        'message' => '1',
                        'context' => [
                            'tandon' => $tandon->name,
                            'height' => $height,
                            'action' => 'activate',
                        ],
                    ];

                    Log::info('mqtt.pump_queued', [
                        'tandon' => $tandon->name,
                        'height' => $height,
                        'topic' => $pumpTopic,
                        'action' => 'activate',
                    ]);
                }

                // Check if water level is above maximum threshold
                if ($height >= $tandon->height_max) {
                    // Queue pump deactivation message
                    $pumpTopic = $topicParts[0] . '/' . $topicParts[1] . '/water_pump';
                    $this->publishQueue[] = [
                        'topic' => $pumpTopic,
                        'message' => '0',
                        'context' => [
                            'tandon' => $tandon->name,
                            'height' => $height,
                            'action' => 'deactivate',
                        ],
                    ];

                    Log::info('mqtt.pump_queued', [
                        'tandon' => $tandon->name,
                        'height' => $height,
                        'topic' => $pumpTopic,
                        'action' => 'deactivate',
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('mqtt.persist_failed', [
                    'topic' => $topic,
                    'height' => $height,
                    'error' => $e->getMessage(),
                ]);
            }
        }, $qos);

        $this->info('Starting MQTT loop...');

        try {
            // Use non-blocking loop to process publish queue
            while (true) {
                $client->loop(false); // Non-blocking loop
                
                // Process queued publish messages
                while (!empty($this->publishQueue)) {
                    $item = array_shift($this->publishQueue);
                    
                    try {
                        $client->publish($item['topic'], $item['message'], 0);
                        Log::info('mqtt.pump_published', $item['context']);
                    } catch (\Throwable $e) {
                        Log::error('mqtt.pump_publish_failed', [
                            'topic' => $item['topic'],
                            'error' => $e->getMessage(),
                            'context' => $item['context'],
                        ]);
                    }
                }
                
                usleep(100000); // Sleep 100ms between iterations
            }
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
