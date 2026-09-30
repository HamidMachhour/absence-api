# School Onboarding Checklist (Printable)

## How to use this checklist
- Print this document or use it digitally.
- Mark each item as complete only after verification.
- Use the Owner and Date columns for accountability.
- Keep one copy per school deployment.

Legend:
- [ ] Not started
- [x] Completed
- [!] Blocked

---

## Deployment Info
- School name: ______________________________
- Deployment environment: ____________________
- Go-live date target: ________________________
- Project lead: _______________________________
- Technical lead: _____________________________

---

## Phase 1: Platform Bootstrap
| Status | Task | Owner | Date | Notes |
|---|---|---|---|---|
| [ ] | Create first admin account |  |  |  |
| [ ] | Confirm admin can log in |  |  |  |
| [ ] | Configure school settings |  |  |  |
| [ ] | Validate home page loads with school config |  |  |  |
| [ ] | Confirm language switching works (if required) |  |  |  |

Exit criteria:
- Admin access is stable and school settings are saved.

---

## Phase 2: Role and User Setup
| Status | Task | Owner | Date | Notes |
|---|---|---|---|---|
| [ ] | Register core staff users |  |  |  |
| [ ] | Assign admin backup account |  |  |  |
| [ ] | Assign manager role(s) |  |  |  |
| [ ] | Assign teacher role(s) |  |  |  |
| [ ] | Assign student role(s) where needed |  |  |  |
| [ ] | Verify each role dashboard opens correctly |  |  |  |

Exit criteria:
- All key users can log in and access the correct role workflow.

---

## Phase 3: Class and Student Data Setup
| Status | Task | Owner | Date | Notes |
|---|---|---|---|---|
| [ ] | Create classes manually or import CSV |  |  |  |
| [ ] | Verify class ownership/manager mapping |  |  |  |
| [ ] | Add/import students to classes |  |  |  |
| [ ] | Validate sample student profiles |  |  |  |
| [ ] | Export classes/users as backup snapshot |  |  |  |

Exit criteria:
- Class roster data is accurate and recoverable.

---

## Phase 4: Enrollment Workflow Validation
| Status | Task | Owner | Date | Notes |
|---|---|---|---|---|
| [ ] | Submit enrollment request using student account |  |  |  |
| [ ] | Verify request appears in enrollments list |  |  |  |
| [ ] | Approve one enrollment as manager/admin |  |  |  |
| [ ] | Confirm student is added to class roster |  |  |  |
| [ ] | Test duplicate enrollment handling |  |  |  |

Exit criteria:
- Enrollment request-to-approval flow works end to end.

---

## Phase 5: Seance and Attendance Setup
| Status | Task | Owner | Date | Notes |
|---|---|---|---|---|
| [ ] | Create seances with correct teacher/day/time |  |  |  |
| [ ] | Verify teacher sees assigned seances |  |  |  |
| [ ] | Open attendance checklist from seance |  |  |  |
| [ ] | Mark attendance entries manually |  |  |  |
| [ ] | Test QR attendance flow (if used) |  |  |  |
| [ ] | Verify attendance refresh/list updates |  |  |  |

Exit criteria:
- Teachers can run daily attendance without support.

---

## Phase 6: Absence Conversion and Follow-up
| Status | Task | Owner | Date | Notes |
|---|---|---|---|---|
| [ ] | Trigger attendance to absence conversion |  |  |  |
| [ ] | Verify absences appear in absence list |  |  |  |
| [ ] | Save one absence justification |  |  |  |
| [ ] | Remove one test/invalid absence entry |  |  |  |
| [ ] | Validate reporting/chart endpoints load |  |  |  |

Exit criteria:
- Absence lifecycle is working and auditable.

---

## Phase 7: Admin Operations and Recovery
| Status | Task | Owner | Date | Notes |
|---|---|---|---|---|
| [ ] | Test user import and export |  |  |  |
| [ ] | Test class import and export |  |  |  |
| [ ] | Run manual attendance periodic tasks |  |  |  |
| [ ] | Review logs for warnings/errors |  |  |  |
| [ ] | Validate rollback/contact procedure |  |  |  |

Exit criteria:
- Admin team can maintain and recover operations confidently.

---

## Phase 8: Training and Go-Live Readiness
| Status | Task | Owner | Date | Notes |
|---|---|---|---|---|
| [ ] | Deliver admin training |  |  |  |
| [ ] | Deliver manager training |  |  |  |
| [ ] | Deliver teacher training |  |  |  |
| [ ] | Share student enrollment instructions |  |  |  |
| [ ] | Share troubleshooting guide with support team |  |  |  |
| [ ] | Confirm support escalation contacts |  |  |  |

Exit criteria:
- Staff can operate independently with documented support paths.

---

## Final Go-Live Sign-off
- Technical validation complete: [ ] Yes  [ ] No
- Functional validation complete: [ ] Yes  [ ] No
- Training complete: [ ] Yes  [ ] No
- Risk review complete: [ ] Yes  [ ] No

Approvals:
- Project lead name/signature: ______________________________  Date: ____________
- School representative name/signature: _____________________  Date: ____________
- Technical lead name/signature: ____________________________  Date: ____________

---

## Post Go-Live (Week 1)
| Status | Task | Owner | Date | Notes |
|---|---|---|---|---|
| [ ] | Daily check: failed logins and permission errors |  |  |  |
| [ ] | Daily check: pending enrollments backlog |  |  |  |
| [ ] | Daily check: attendance conversion success |  |  |  |
| [ ] | Daily check: unresolved absences |  |  |  |
| [ ] | Capture issues and update troubleshooting notes |  |  |  |

Success criteria for week 1:
- No critical blockers remain open.
- Core attendance and enrollment workflows are stable.
