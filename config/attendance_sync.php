<?php

return [
    'mqtt_qos' => env('ATTENDANCE_MQTT_QOS', 1),
    'mqtt_retain' => env('ATTENDANCE_MQTT_RETAIN', false),
    'ack_topics' => [
        env('ATTENDANCE_ACK_TOPIC', 'attendance/ack/#'),
    ],
    'subscriber_qos' => env('ATTENDANCE_SUBSCRIBER_QOS', 1),
];
