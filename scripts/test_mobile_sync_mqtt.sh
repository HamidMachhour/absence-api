#!/usr/bin/env bash
set -euo pipefail

# One-command local smoke test for MQTT + mobile attendance sync.
# It automates:
# 2) MQTT subscribe,
# 3) Laravel MQTT publish smoke test,
# 4) Roster fetch + attendance submit API flow.

if [[ $# -lt 3 ]]; then
  echo "Usage: $0 <teacher_email> <mqtt_username> <mqtt_password> [base_url] [mqtt_host] [mqtt_port]"
  echo "Example: $0 vivianne79@mckenzie.com hamid IZAMBO7IKHFAWN http://127.0.0.1:8000 127.0.0.1 1883"
  exit 1
fi

TEACHER_EMAIL="$1"
MQTT_USER="$2"
MQTT_PASS="$3"
BASE_URL="${4:-http://127.0.0.1:8000}"
MQTT_HOST="${5:-127.0.0.1}"
MQTT_PORT="${6:-1883}"

for cmd in php curl mosquitto_sub; do
  if ! command -v "$cmd" >/dev/null 2>&1; then
    echo "Missing required command: $cmd"
    exit 1
  fi
done

cd "$(dirname "$0")/.."

TMP_DIR="$(mktemp -d)"
SUB_LOG="$TMP_DIR/mqtt.log"
ROSTER_JSON="$TMP_DIR/roster.json"
SUBMIT_JSON="$TMP_DIR/submit.json"

cleanup() {
  if [[ -n "${SUB_PID:-}" ]] && kill -0 "$SUB_PID" >/dev/null 2>&1; then
    kill "$SUB_PID" >/dev/null 2>&1 || true
  fi
  rm -rf "$TMP_DIR"
}
trap cleanup EXIT

echo "[1/7] Clearing config cache"
php artisan config:clear >/dev/null

echo "[2/7] Starting MQTT subscriber (expecting 2 messages)"
mosquitto_sub \
  -h "$MQTT_HOST" \
  -p "$MQTT_PORT" \
  -u "$MQTT_USER" \
  -P "$MQTT_PASS" \
  -v \
  -t "attendance/updated/#" \
  -t "teacher/+/seances" \
  -C 2 \
  >"$SUB_LOG" 2>&1 &
SUB_PID=$!
sleep 1

echo "[3/7] Running Laravel publish smoke test"
php artisan tinker --execute='app(App\Services\MqttNotificationService::class)->publish("attendance/updated/102", ["event" => "attendance_updated", "seance_id" => "demo-seance", "salle" => "102", "absent_student_ids" => ["demo-student"], "updated_at" => now()->toDateTimeString()]);' >/dev/null

echo "[4/7] Creating Sanctum token for teacher"
TOKEN="$(php artisan tinker --execute='$u = App\Models\User::where("email", "'"$TEACHER_EMAIL"'")->firstOrFail(); echo $u->createToken("android-test")->plainTextToken . PHP_EOL;' | tail -n 1 | tr -d '\r')"
if [[ -z "$TOKEN" ]]; then
  echo "Failed to create token"
  exit 1
fi

echo "[5/7] Loading a seance for that teacher"
SEANCE_ID="$(php artisan tinker --execute='$u = App\Models\User::where("email", "'"$TEACHER_EMAIL"'")->firstOrFail(); $s = App\Models\Seance::where("teacherId", $u->id)->orderBy("created_at", "desc")->first(); if (!$s) { fwrite(STDERR, "No seance found\n"); exit(1);} echo $s->id . PHP_EOL;' | tail -n 1 | tr -d '\r')"
if [[ -z "$SEANCE_ID" ]]; then
  echo "No seance found for teacher: $TEACHER_EMAIL"
  exit 1
fi

echo "[6/7] Fetching roster from API"
curl -fsS \
  -H "Authorization: Bearer $TOKEN" \
  "$BASE_URL/api/v1/seances/$SEANCE_ID/roster" \
  > "$ROSTER_JSON"

ABSENT_STUDENT_ID="$(php -r '$json = json_decode(file_get_contents($argv[1]), true); if (!is_array($json) || empty($json["students"])) { fwrite(STDERR, "Roster empty or invalid\n"); exit(1);} echo $json["students"][0]["id"];' "$ROSTER_JSON")"

NOW_TS="$(date '+%Y-%m-%d %H:%M:%S')"

echo "[7/7] Submitting attendance with one absent student"
curl -fsS -X POST \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  "$BASE_URL/api/v1/seances/$SEANCE_ID/attendance" \
  -d "{\"absent_student_ids\":[\"$ABSENT_STUDENT_ID\"],\"timestamp\":\"$NOW_TS\"}" \
  > "$SUBMIT_JSON"

# Wait briefly for subscriber to complete naturally.
for _ in {1..20}; do
  if ! kill -0 "$SUB_PID" >/dev/null 2>&1; then
    break
  fi
  sleep 0.5
done

if kill -0 "$SUB_PID" >/dev/null 2>&1; then
  kill "$SUB_PID" >/dev/null 2>&1 || true
fi

echo
echo "=== API Roster (summary) ==="
php -r '$json = json_decode(file_get_contents($argv[1]), true); echo "seance_id=" . ($json["seance_id"] ?? "n/a") . PHP_EOL; echo "students=" . (isset($json["students"]) ? count($json["students"]) : 0) . PHP_EOL;' "$ROSTER_JSON"

echo
echo "=== API Submit Response ==="
cat "$SUBMIT_JSON"

echo
echo "=== MQTT Messages Captured ==="
if [[ -s "$SUB_LOG" ]]; then
  cat "$SUB_LOG"
else
  echo "No MQTT messages captured. Check broker auth, host/port, and Laravel MQTT env values."
fi

echo
echo "Done."
