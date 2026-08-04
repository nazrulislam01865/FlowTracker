# Role/access, notification and Job status audit

- Role-based access is enforced by `AccessControlService`, route middleware and record-scoped queries.
- Non-admin Job notifications now go only to the assigned Job owner. For legacy Jobs without an owner, the coordinator is the fallback assigned user.
- Admin and Super Admin users receive Job notifications regardless of assignment.
- Generic Job members and task assignees no longer receive Job-level notifications only because they can view the Job.
- Manual Job workflow/status changes (phase transition, cancellation and deactivation) require both the role's Job edit permission and ownership of the Job. Admin/Super Admin bypass this ownership restriction.
- Configured automatic workflow transitions remain system-driven and can still run after Task Pack requirements are satisfied.
