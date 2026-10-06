# Terminal / Kiosk Mode Help

## Purpose
This guide explains how to use the terminal mode for classroom screens and kiosk-style attendance display.

Terminal mode is intended for:
- A shared classroom display
- A room-based kiosk for attendance monitoring
- Quick access to the active seance in a room

---

## 1) Access requirements
Before using terminal mode:
- The user must be authenticated.
- The user must pass the `terminal` middleware.
- The room must have at least one active seance at the current time.

If you do not meet those conditions, the terminal pages will redirect or show no attendance data.

---

## 2) Main terminal flow
### Use case: Open terminal mode
1. Open the terminal room selection page.
2. Choose the classroom room (`salle`).
3. Open the terminal dashboard for that room.
4. The dashboard shows the active attendance record for the current seance.

Expected result:
- The terminal dashboard loads the live attendance state for the selected room.

---

## 3) Room selection
### Use case: Pick an active room
1. Open the room selection page.
2. Select one of the listed rooms.
3. Continue to the dashboard.

Important behavior:
- The room list is filtered to rooms with a currently active seance.
- This helps avoid selecting a room with no live attendance data.

---

## 4) Dashboard behavior
### Use case: View live attendance
1. Open the terminal dashboard for a room.
2. Review the active attendance or read-only attendance data.
3. Wait for the dashboard refresh cycle to update the view.

Expected result:
- The dashboard shows the current attendance state for the room.
- If the session has already been finalized, the terminal may show read-only data.

---

## 5) Find attendance by room
### Use case: Jump directly to a room attendance record
1. Use the room search field on the terminal screen.
2. Submit the room number.
3. The system redirects to the dashboard for that room.

Expected result:
- The dashboard opens without manual navigation through the room list.

---

## 6) Check the next seance
### Use case: Determine whether another seance is available in the room
1. Trigger the next-seance check from the terminal dashboard.
2. Review the response.
3. If another attendance exists, the dashboard can move forward to it.

Expected result:
- The terminal can detect whether the current room has a future seance with an attendance record.

---

## 7) Common terminal issues and fixes
### Symptom: No rooms appear
Possible causes:
- No active seance is scheduled right now.
- The room data is missing or invalid.

Fix:
- Verify the seance timetable.
- Confirm the room (`salle`) value is filled in for the seance.
- Check current day/time and timezone settings.

### Symptom: No attendance data
Possible causes:
- The selected room has no current attendance.
- The seance has not started yet.
- The attendance has already been finalized.

Fix:
- Select a room with a live seance.
- Verify the seance start and end times.
- Confirm whether the session is now read-only.

### Symptom: Redirect back to room selection
Possible causes:
- Missing `salle` query parameter.
- No matching attendance record.

Fix:
- Re-select the room.
- Confirm the dashboard URL includes the room parameter.

---

## 8) Recommended terminal usage
- Place the terminal page on a large classroom screen.
- Assign a staff member to monitor the room view.
- Use only rooms with a live seance.
- Keep the terminal logged in with a terminal-enabled account.

---

## 9) Quick route map
- Room selection: `terminal.selectRoom`
- Direct room lookup: `terminal.find`
- Room dashboard: `terminal.dashboard`
- Next seance check: `terminal.checkNext`

---

## 10) Support checklist
When terminal mode fails, verify:
1. User is authenticated.
2. User has terminal access.
3. Room exists and has a valid `salle` value.
4. Seance day and time are correct.
5. Attendance record exists for the room.
6. Timezone matches the school schedule.
