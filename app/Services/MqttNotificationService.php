<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MqttNotificationService
{
    public function publish(string $topic, array $payload): bool
    {
        $payloadJson = json_encode($payload);

        if ($payloadJson === false) {
            Log::warning('MQTT publish skipped because payload could not be encoded.', [
                'topic' => $topic,
            ]);

            return false;
        }

        $mqttFacade = '\\PhpMqtt\\Client\\Facades\\MQTT';

        if (!class_exists($mqttFacade)) {
            Log::warning('MQTT facade is unavailable. Install php-mqtt/laravel-client to enable broker publishing.', [
                'topic' => $topic,
                'payload' => $payload,
            ]);

            return false;
        }

        try {
            $connectionName = config('mqtt-client.default_connection', 'default');
            $qualityOfService = (int) config('attendance_sync.mqtt_qos', 1);
            $retain = (bool) config('attendance_sync.mqtt_retain', false);

            $connection = $mqttFacade::connection($connectionName);
            $connection->publish($topic, $payloadJson, $qualityOfService, $retain);
            $connection->disconnect();

            return true;
        } catch (\Throwable $exception) {
            Log::error('MQTT publish failed.', [
                'topic' => $topic,
                'payload' => $payload,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
