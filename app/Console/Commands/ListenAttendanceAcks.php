<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ListenAttendanceAcks extends Command
{
    protected $signature = 'mqtt:listen-attendance-acks {--once : Subscribe and process until one message is received}';
    protected $description = 'Listen for MQTT attendance acknowledgement messages and log their payloads';

    public function handle(): int
    {
        $mqttFacade = '\\PhpMqtt\\Client\\Facades\\MQTT';

        if (!class_exists($mqttFacade)) {
            $this->error('MQTT facade is unavailable. Ensure php-mqtt/laravel-client is installed.');
            return self::FAILURE;
        }

        $topics = array_values(array_filter((array) config('attendance_sync.ack_topics', ['attendance/ack/#'])));
        $qos = (int) config('attendance_sync.subscriber_qos', 1);

        if (empty($topics)) {
            $this->error('No MQTT ack topics configured. Set attendance_sync.ack_topics or ATTENDANCE_ACK_TOPIC.');
            return self::FAILURE;
        }

        $this->info('Connecting to MQTT broker and subscribing to: ' . implode(', ', $topics));

        try {
            $connection = $mqttFacade::connection(config('mqtt-client.default_connection', 'default'));
            $stopAfterFirst = (bool) $this->option('once');
            $received = 0;

            foreach ($topics as $topic) {
                $connection->subscribe($topic, function (string $topic, string $message) use (&$received, $stopAfterFirst, $connection) {
                    $decoded = json_decode($message, true);
                    $received++;

                    Log::info('MQTT attendance ack received.', [
                        'topic' => $topic,
                        'payload' => is_array($decoded) ? $decoded : $message,
                    ]);

                    $line = '[' . now()->toDateTimeString() . '] topic=' . $topic . ' payload=' . (is_array($decoded) ? json_encode($decoded) : $message);
                    $this->line($line);

                    if ($stopAfterFirst && $received >= 1) {
                        $connection->interrupt();
                    }
                }, $qos);
            }

            $connection->loop(true);
            $connection->disconnect();

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            Log::error('MQTT attendance ack listener failed.', [
                'error' => $exception->getMessage(),
            ]);

            $this->error('MQTT listener failed: ' . $exception->getMessage());
            return self::FAILURE;
        }
    }
}
