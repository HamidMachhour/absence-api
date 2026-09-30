# Student Help Manual

## Purpose
This manual explains what a student can do in the app, especially enrollment and absence follow-up.

---

## 1) Student capabilities
Students can typically:
- Register and log in
- Request enrollment in classes
- View class and profile-related information
- Review their absence history (depending on visibility settings)

---

## 2) Register and login
### Use case: Create account
1. Open registration page.
2. Submit personal details.
3. Go to login page after success.

### Use case: Log in
1. Open login page.
2. Enter credentials.
3. Open dashboard.

Expected result:
- Student account is active and session is created.

---

## 3) Ensure student role is assigned
### Use case: Role verification
1. If enrollment actions are unavailable, contact admin.
2. Ask admin to assign student role.
3. Re-login after role update.

Expected result:
- Enrollment features become available.

---

## 4) Request class enrollment
### Use case: Enroll in a class
1. Open classes list.
2. Open target class details.
3. Submit enrollment request.

Expected result:
- Request enters pending enrollment queue.
- Manager/admin must approve.

---

## 5) After enrollment approval
### Use case: Confirm you were accepted
1. Check class-related pages.
2. Confirm your membership appears in class context.

Expected result:
- You are attached to the class roster.

---

## 6) Absence and justifications
### Use case: Follow up on your absences
1. Open absences view (if enabled for your account).
2. Review entries and details.
3. Provide required justification through school process.

Expected result:
- Attendance issues can be resolved with valid context.

---

## 7) Common issues and fixes
- Cannot enroll:
  - Verify your account has student role.
  - Check you are logged in.
- Enrollment already exists:
  - Wait for approval or contact manager/admin.
- Login fails:
  - Verify credentials.
  - Use account recovery process through admin if needed.

---

## 8) Quick route map (for support)
- Register: users.register, users.store
- Login: users.login, users.authenticate
- Classes: classes.index, classes.show
- Enrollment submission: enrollments.store
- Dashboard: users.dashboard
