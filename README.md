# absence-api

## Documentation

- User help guide: docs/USER_HELP_GUIDE.md
- Admin manual: docs/ADMIN_HELP.md
- Manager manual: docs/MANAGER_HELP.md
- Teacher manual: docs/TEACHER_HELP.md
- Student manual: docs/STUDENT_HELP.md
- Terminal help: docs/TERMINAL_HELP.md
- Troubleshooting guide: docs/TROUBLESHOOTING.md
- School onboarding checklist: docs/SCHOOL_ONBOARDING_CHECKLIST.md
- Field one-page checklist: docs/FIELD_CHECKLIST_ONE_PAGE.md
- Field one-page checklist (EN/FR/AR): docs/FIELD_CHECKLIST_ONE_PAGE_EN_FR_AR.md
- Field one-page checklist print HTML: docs/FIELD_CHECKLIST_ONE_PAGE_PRINT.html
- Field one-page checklist print PDF: docs/FIELD_CHECKLIST_ONE_PAGE_PRINT.pdf

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
