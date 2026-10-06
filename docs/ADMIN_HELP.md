# Admin Help Manual

## Purpose
This manual covers the workflows reserved for administrators.

---

## 1) Core responsibilities
Admin users are responsible for:
- Initial platform bootstrap
- User lifecycle and role governance
- School settings
- Global data operations (import/export, cleanup)
- Oversight of enrollments, classes, seances, attendance outcomes

---

## 2) First-time bootstrap
### Use case: Create the first admin
1. Open the app root page.
2. If no admin exists, complete admin registration.
3. Log in as admin.
4. Open school settings.
5. Save required school configuration.

Expected result:
- Platform becomes operational for normal users.

---

## 3) User administration
### Use case: Review users
1. Open users list.
2. Filter/search users if needed.
3. Open user profile details.

### Use case: Activate or refute account
1. Open target user.
2. Trigger activate or refute action.
3. Confirm status changed.

### Use case: Edit or delete account
1. Open target user edit page.
2. Save updates or remove account if policy allows.

Expected result:
- Only valid and approved users remain active.

---

## 4) Role governance
### Use case: Assign role(s) to a user
1. Open target user.
2. Open user roles area.
3. Add one role at a time.
4. Confirm role appears in assigned list.

### Use case: Remove role(s)
1. Open user roles area.
2. Remove incorrect role assignment.
3. Confirm permissions are updated.

Good practice:
- Keep least-privilege access.
- Avoid giving admin role unless strictly required.

---

## 5) Global school settings
### Use case: Update school profile/settings
1. Open school settings.
2. Update values.
3. Save and verify changes from the home/dashboard experience.

Expected result:
- Branding and operational settings are reflected across the app.

---

## 6) Classes and timetable governance
### Use case: Create or import classes
1. Open class management.
2. Create class manually or import CSV.
3. Verify class owner/manager.

### Use case: Create seances
1. Open seance management.
2. Add teacher, day, start and end time.
3. Save and verify listing.

Expected result:
- Teachers can run attendance against scheduled seances.

---

## 7) Enrollment approvals oversight
### Use case: Handle pending enrollments
1. Open enrollments list.
2. Filter by manager or class.
3. Accept individual requests or accept all.
4. Remove invalid requests.

Expected result:
- Approved students become class members.

---

## 8) Attendance and absences oversight
### Use case: Verify attendance pipeline
1. Confirm seances are starting correctly.
2. Check attendance records are created.
3. Confirm end-of-seance conversion to absences.

### Use case: Manual periodic task trigger
1. Run attendance periodic tasks route from admin area.
2. Review execution output and logs.

Expected result:
- Attendance lifecycle works from checklist to absence generation.

---

## 9) Data operations
### Use case: Import/export users and classes
1. Open import/export forms.
2. Run import with validated CSV.
3. Export data for backup/reporting.

Expected result:
- Data loads consistently and can be audited externally.

---

## 10) Common issues and fixes
- Not authorized:
  - Verify current user has admin role.
  - Re-login if session is stale.
- Role assignment fails:
  - Confirm role exists in roles list.
  - Check if role already assigned.
- Enrollment acceptance fails:
  - Confirm class and user are valid.
  - Check if student already exists in class.
- Attendance task errors:
  - Verify seance day/time configuration.
  - Check logs for controller exceptions.

---

## 11) Quick route map (for support)
- User management: users.*
- Role management: roles.* and userRoles.*
- Enrollments: enrollments.*
- Classes: classes.*
- Seances: seances.*
- Attendance: attendances.*
- School settings: settings.school.*
- Admin dashboard: admins.dashboard
