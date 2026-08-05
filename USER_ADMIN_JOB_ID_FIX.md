# User administration and Job ID display fix

- Keeps Job IDs on a single line in the Jobs table.
- Users & Assignments now supports Add, Edit and Delete actions for administrators.
- Add User uses Password + Confirm password (no temporary-password field).
- Edit User can change name, email, role, department, status and password.
- Password changes require confirmation; leaving both password fields blank preserves the current password.
- Password changes invalidate that user's existing sessions.
- User deletion clears compatibility assignee references and preserves historical Job/Task records through existing null/cascade foreign-key rules.
- Signed-in users cannot delete themselves and Super Admin accounts cannot be deleted.
