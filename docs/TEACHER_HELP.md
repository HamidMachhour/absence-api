# Teacher Help Manual

## Purpose
This manual explains the teacher workflow for daily attendance and student behavior tracking.

---

## 1) Teacher responsibilities
Teachers usually:
- Open scheduled seances
- Run attendance checklists
- Use QR check when enabled
- Convert attendance to absences when session ends
- Report misconduct when needed

---

## 2) Start of day
### Use case: Open timetable and seance list
1. Log in.
2. Open seances list.
3. Select the current seance.

Expected result:
- Only assigned seances should be used for attendance actions.

---

## 3) Attendance checklist workflow
### Use case: Open checklist and mark attendance
1. Open seance.
2. Open attendance checklist.
3. System auto-creates attendance record if missing.
4. Add students to attendance/absence list according to your class process.
5. Save updates.

Expected result:
- Attendance list reflects who is absent/present according to your workflow.

---

## 4) QR code attendance workflow
### Use case: Scan student code
1. Open QR check page for the attendance.
2. Scan student QR/barcode data.
3. Confirm success response.
4. Repeat for remaining students.

Expected result:
- Entries are updated in near real time.

---

## 5) Correcting entries
### Use case: Remove a mistaken attendance entry
1. Open attendance details.
2. Remove selected attendance-student entry.
3. Confirm list is refreshed.

Expected result:
- Mistaken scans or entries are corrected immediately.

---

## 6) End-of-seance workflow
### Use case: Convert attendance to absences
1. Open attendance.
2. Run attendance to absence conversion.
3. Verify generated absences.

Expected result:
- Absence records are generated for follow-up.

---

## 7) Misconduct reporting (if used)
### Use case: Report misconduct from a seance
1. Open misconduct report section.
2. Select seance and student.
3. Add act details and submit.

Expected result:
- Report appears in misconduct reports for review.

---

## 8) Common issues and fixes
- Not authorized on checklist:
  - Ensure seance belongs to your teacher account.
- QR scan does not update:
  - Refresh attendance list and retry scan.
  - Verify scanned data matches expected student identifier.
- Conversion fails:
  - Confirm attendance record exists and session is valid.

---

## 9) Quick route map (for support)
- Seances: seances.index, seances.show
- Attendance checklist: attendances.checklist
- QR check: attendances.qrcodeCheck
- Attendance updates: attendances.pushStudent, attendances.popStudent, attendances.clear
- Convert to absence: attendances.attendance_to_absence
- Misconduct reports: misconduct_reports.*
