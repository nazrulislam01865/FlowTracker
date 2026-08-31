# Inquiry participant visibility — 2026-08-31

## Requirement

The Inquiry list must not expose every Inquiry to regular users. A non-administrator may see an Inquiry only when either:

- they created the Inquiry (`inquiries.created_by`), or
- at least one non-deleted Inquiry task is assigned directly to them (`inquiry_tasks.assignee_id`).

Admin and super-admin users retain workspace-wide Inquiry visibility.

## Implementation

`AccessControlService::applyInquiryScope()` is the canonical visibility boundary for Inquiry reads. It now applies the participant rule above to every non-administrator, regardless of a role's configured `all_records`, `department`, owner, or assigned-jobs scope.

Because the Inquiry list, list metrics, exports, detail reads, policies, and dashboard Inquiry reads already pass through this boundary, they now remain consistent and cannot expose a row in one surface while hiding it in another.

Ownership alone does not grant Inquiry visibility to a regular user. Deleted Inquiry tasks do not grant visibility because the `Inquiry::tasks()` relationship uses the `SoftDeletes` global scope.

## Validation

A regression test was added in `tests/Feature/InquiryParticipantVisibilityTest.php` covering:

- creator visibility,
- task-assignee visibility,
- owner-only exclusion,
- unrelated Inquiry exclusion,
- regular-role `all_records` not widening visibility,
- matching list metric visibility,
- admin full visibility,
- super-admin full visibility.

No database migration is required.
