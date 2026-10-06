# Absence App Help Guide (Use Cases)

## 1) Goal of this document
This guide explains the most common end-user and admin workflows in the web app, starting from registration and role assignment, then continuing with class enrollment and attendance operations.

Use this as:
- A support document for school staff
- A training document for new users
- A QA checklist for verifying expected flows

---

## 2) Roles and what they do
The app uses role-based behavior in dashboards and permissions.

Main roles used in the app:
- admin: full configuration and administration
- manager: manages classes and class-level operations
- teacher: handles attendance for assigned seances
- student: can enroll to classes and view student-related data
- guest: limited access until role/permissions are updated
- terminal: kiosk-like classroom terminal mode

Notes:
- First app setup depends on creating the first admin account.
- Many management routes require authenticated users, and some require admin-specific middleware.

---

## 3) First-time setup (bootstrap flow)
### Use case: There is no admin yet
1. Open the home page.
2. If no admin exists, the app redirects to admin registration.
3. Create the first admin account.
4. Log in with that admin account.
5. Configure school settings.

Expected result:
- The app has one admin and school settings are saved.

---

## 4) User registration and login
### Use case: New user creates an account
1. Go to the registration page.
2. Submit user details.
3. User is redirected to login page after successful registration.
4. User logs in with credentials.

Expected result:
- User session is created.
- User lands on dashboard according to role.

### Use case: Existing user logs in
1. Go to login page.
2. Submit credentials.
3. On success, user is redirected to role-specific dashboard.

Expected result:
- Admin users go to admin dashboard.
- Other users go to user dashboard.

---

## 5) Assigning a role to a user (Admin flow)
### Use case: Admin assigns one or more roles after registration
1. Admin opens users list.
2. Admin opens target user profile.
3. Admin goes to user roles page.
4. Admin adds role (for example: student, teacher, manager).
5. Save role assignment.

Expected result:
- User has the new role.
- User dashboard and permissions change accordingly.

Good practice:
- Assign only required roles.
- Review existing roles before adding duplicates.

---

## 6) Student enrollment flow
### Use case: Student requests enrollment in a class
Preconditions:
- User has student role.
- Target class exists.

Steps:
1. Student opens class page.
2. Student submits enrollment request.
3. Enrollment record is created in pending state.

Expected result:
- Enrollment appears in enrollment management list.

### Use case: Manager/Admin approves enrollment
1. Manager or admin opens enrollments list.
2. Filter by manager or class if needed.
3. Accept one enrollment (or accept all).
4. System creates student record in class and removes pending enrollment.

Expected result:
- Student becomes officially enrolled in class.
- Enrollment queue is reduced.

---

## 7) Class and seance preparation
### Use case: Manager/Admin creates class
1. Create class.
2. Set class owner/manager if applicable.
3. Optionally import students by CSV.

Expected result:
- Class is ready for scheduling and enrollment.

### Use case: Admin schedules seances
1. Open seance management.
2. Create seances with teacher and day/time.
3. Save timetable.

Expected result:
- Teachers can access attendance checklist for their seances.

---

## 8) Attendance lifecycle (Teacher flow)
### Use case: Teacher opens attendance checklist
1. Open assigned seance.
2. Open checklist.
3. System loads or creates attendance record.
4. Teacher marks absences or updates attendance entries.

Expected result:
- Attendance list reflects current class session.

### Use case: QR-based attendance check
1. Open QR check page for attendance.
2. Scan student code.
3. Add or remove student from attendance entries.

Expected result:
- Real-time success/error response per scan action.

### Use case: End of seance conversion
1. At seance end, attendance can be converted to absences.
2. Teacher or periodic tasks trigger conversion flow.

Expected result:
- Absence records are produced from attendance list.

---

## 9) Absence follow-up
### Use case: Review and justify absences
1. Open absences list.
2. Open one absence record.
3. Add/update student absence justification.
4. Remove invalid student entries if needed.

Expected result:
- Absence records are accurate and documented.

---

## 10) Recommended help document format for your team
To keep the help guide maintainable, use this structure for each feature:

- Title: one workflow (example: Assign role to new user)
- Actors: who can perform this action
- Preconditions: what must already exist
- Steps: exact UI actions (numbered)
- Expected result: what success looks like
- Errors and fixes: common failures + resolution
- Route reference (optional): route name(s) for debugging

Example template:

Title: Student enrollment request
Actors: student
Preconditions: user role includes student; class exists
Steps:
1. Open classes list
2. Open class details
3. Click enroll
4. Confirm request
Expected result: pending enrollment created
Errors and fixes:
- "not authorized": verify user role is student
- "already enrolled": check class roster first

---

## 11) Operations checklist (quick)
Use this checklist when onboarding a new school:

1. Create first admin account.
2. Configure school settings.
3. Create base roles (if missing).
4. Register users.
5. Assign each user role(s).
6. Create classes.
7. Enroll students (request + approval).
8. Create seances and map teachers.
9. Run first attendance session.
10. Verify absence conversion and reporting.

---

## 12) Suggested next documentation files
You can split this guide into role-based manuals:

- docs/ADMIN_HELP.md
- docs/MANAGER_HELP.md
- docs/TEACHER_HELP.md
- docs/STUDENT_HELP.md
- docs/TROUBLESHOOTING.md

This keeps the main guide short and easier to translate.
