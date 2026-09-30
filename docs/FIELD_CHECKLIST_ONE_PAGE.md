# Field Team One-Page Checklist

## Scope
Use this quick checklist for on-site school go-live validation.

Deployment date: ____________
School: ____________________
Field lead: _________________

---

## A) Access and Roles (10 min)
- [ ] Admin can log in and open dashboard
- [ ] Manager account can access classes and enrollments
- [ ] Teacher account can access assigned seances
- [ ] Student account can log in and open classes list
- [ ] Role mismatches corrected before continuing

Pass criteria:
- All four role paths work on first attempt.

---

## B) Core Data Ready (10 min)
- [ ] School settings are configured
- [ ] At least 1 class exists
- [ ] At least 1 teacher assigned to a seance
- [ ] At least 1 student appears in class roster

Pass criteria:
- Class, teacher, and student data are all present.

---

## C) Enrollment Flow (10 min)
- [ ] Student submits enrollment request
- [ ] Request appears in enrollments list
- [ ] Manager/Admin approves request
- [ ] Student appears in class roster after approval

Pass criteria:
- Enrollment request-to-approval works end to end.

---

## D) Attendance Flow (15 min)
- [ ] Teacher opens seance checklist
- [ ] Attendance record loads/creates successfully
- [ ] Teacher adds/removes one student entry
- [ ] QR flow tested (if enabled)
- [ ] Attendance list refresh shows changes

Pass criteria:
- Teacher can complete attendance without support.

---

## E) Absence and Reporting (10 min)
- [ ] Attendance-to-absence conversion runs
- [ ] Absence appears in absences list
- [ ] One justification update tested
- [ ] Chart/report pages load

Pass criteria:
- Absence lifecycle is functional and visible.

---

## F) Admin Safety Checks (10 min)
- [ ] Import/export opens and runs with sample file
- [ ] Manual attendance periodic tasks run
- [ ] No critical errors in logs

Log file:
- storage/logs/laravel.log

Pass criteria:
- Operations team can maintain the system safely.

---

## Go/No-Go Decision
- [ ] GO
- [ ] NO-GO

Reason (if NO-GO): ______________________________________________

Open blockers:
1. ______________________________________________________________
2. ______________________________________________________________
3. ______________________________________________________________

Sign-off:
- Field lead: __________________  Date: __________
- School representative: _______  Date: __________
- Technical support: ___________  Date: __________
