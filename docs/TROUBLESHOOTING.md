# Troubleshooting Guide

## Purpose
This guide helps staff resolve the most common issues in the Absence App quickly.

Use it when users report:
- Access denied or not authorized messages
- Registration or login failure
- Enrollment submission/approval problems
- Attendance checklist or QR issues
- Missing absences after seance end

---

## 1) Quick triage flow
1. Identify user role (admin, manager, teacher, student).
2. Confirm user is authenticated (active session).
3. Confirm route/action belongs to that role.
4. Check whether target record exists (user, class, seance, attendance).
5. Review application logs for exact exception details.

Log path:
- storage/logs/laravel.log

---

## 2) Authentication and authorization issues

### Symptom
User sees not authorized or gets redirected unexpectedly.

### Common causes
- User is not logged in.
- User has wrong role for the action.
- User tries to access another owner-only resource.
- Session/token expired.

### Fix
1. Log out and log back in.
2. Verify assigned roles from user role management.
3. Confirm ownership rules:
   - Teacher actions require assigned seance.
   - Manager actions require class-level ownership.
   - Admin-only screens require admin role.
4. Re-test using correct account.

Related areas:
- user role checks across controllers
- middleware-protected routes

---

## 3) Registration and login failures

### Symptom
Registration fails or login returns failure message.

### Common causes
- Invalid input fields.
- Duplicate or invalid user data.
- API login rejects credentials.

### Fix
1. Re-check required fields and input formats.
2. Verify credentials and retry login.
3. If still failing, inspect controller response and log details.
4. For admin bootstrap: ensure first admin exists or register it.

---

## 4) Role assignment issues

### Symptom
User cannot access expected dashboard/features after registration.

### Common causes
- User still has guest role only.
- Role assignment was not saved.
- Duplicate role attempt rejected.

### Fix
1. Open user roles for that user.
2. Add required role (student, teacher, manager).
3. Remove incorrect role(s) if necessary.
4. Ask user to re-login so UI/permissions refresh.

---

## 5) Enrollment problems

### Symptom A
Student cannot submit enrollment.

### Causes
- Account is not student role.
- Target class is invalid or unavailable.

### Fix
1. Verify student role.
2. Re-open class page and submit again.
3. Check for existing pending/accepted enrollment.

### Symptom B
Manager/admin cannot accept enrollment.

### Causes
- Request is already processed.
- Student is already attached to class.
- Permission mismatch for current user.

### Fix
1. Refresh enrollment list.
2. Verify student is not already in class roster.
3. Retry as class owner or admin.

---

## 6) Class and seance configuration issues

### Symptom
Attendance features are missing or inaccessible for teacher.

### Common causes
- Seance not assigned to that teacher.
- Day/time configuration is incorrect.
- Class or teacher mapping is incomplete.

### Fix
1. Check seance teacher assignment.
2. Verify day and start/end times.
3. Confirm class exists and has students.
4. Re-test from teacher account.

---

## 7) Attendance checklist and QR issues

### Symptom A
Checklist does not load expected students.

### Causes
- Attendance already contains students marked absent.
- Class has no enrolled students.
- Wrong seance selected.

### Fix
1. Verify selected seance and class.
2. Confirm class roster is populated.
3. Refresh attendance list and re-open checklist.

### Symptom B
QR scan does not add/remove student.

### Causes
- Scanned payload does not match expected student data.
- Unauthorized access to attendance endpoint.
- Attendance context mismatch.

### Fix
1. Confirm scan data format and student identity.
2. Verify teacher is owner of the seance.
3. Retry on the same attendance record.

---

## 8) Attendance to absence conversion issues

### Symptom
End-of-seance conversion fails or absences are missing.

### Common causes
- Attendance record was not created.
- Time window logic did not capture seance.
- Exception occurred during periodic task execution.

### Fix
1. Confirm attendance record exists for seance.
2. Check seance start/end times and day.
3. Run admin manual periodic task trigger.
4. Inspect logs for conversion exceptions.

Related admin action:
- manual run attendance tasks from admin section

---

## 9) Import/export issues (users/classes)

### Symptom
CSV import fails or records are incomplete.

### Common causes
- Invalid CSV schema/headers.
- Duplicate or malformed values.
- Permission mismatch.

### Fix
1. Validate CSV headers and required fields.
2. Test with a small sample file first.
3. Run import with admin account.
4. Re-export data to verify successful persistence.

---

## 10) Dashboard mismatch by role

### Symptom
User lands on wrong dashboard or missing widgets.

### Common causes
- Primary role changed recently.
- Role not assigned correctly.
- Session stale after role update.

### Fix
1. Verify assigned role(s).
2. Log out and log in again.
3. Re-test role-specific dashboard view.

---

## 11) Debug checklist for support teams
1. Capture user ID, role, and timestamp.
2. Capture exact page/action and error text.
3. Confirm permissions and ownership constraints.
4. Check if object exists (user/class/seance/attendance/enrollment).
5. Read recent log entries around timestamp.
6. Reproduce with admin test account if required.
7. Document resolution for future incidents.

---

## 12) Preventive best practices
- Assign roles immediately after registration.
- Keep seance timetable clean and validated weekly.
- Review pending enrollments daily.
- Monitor periodic attendance task output.
- Use staged CSV imports before full batch operations.
