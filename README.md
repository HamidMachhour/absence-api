# absence-api

## MQTT Attendance Ack Worker

The backend can listen for MQTT attendance acknowledgements using:

```bash
php artisan mqtt:listen-attendance-acks
```

Useful options:

```bash
php artisan mqtt:listen-attendance-acks --once
```

Environment variables:

- `MQTT_HOST`, `MQTT_PORT`, `MQTT_AUTH_USERNAME`, `MQTT_AUTH_PASSWORD`
- `ATTENDANCE_ACK_TOPIC` (default: `attendance/ack/#`)
- `ATTENDANCE_SUBSCRIBER_QOS` (default: `1`)

To keep it running in production, run the command under Supervisor or systemd.
