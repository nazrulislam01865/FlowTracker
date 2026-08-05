# FlowTrack Role Matrix + Documents Update

## Implemented
- Database-driven Roles & Permissions using the supplied SQL-aligned `roles`, `role_module_access`, `workspace_memberships`, and `permissions` structures.
- Administrator / Super Admin / Administrator roles retain unrestricted access and can manage role assignments and the permission matrix.
- Permission enforcement is applied at navigation, route, query, record, and mutation levels.
- Normal task access remains assignee-strict; normal Job access is limited to Jobs assigned through owner/coordinator/member/task assignment unless a role explicitly grants a broader scope.
- Action permissions include View, Create, Edit own, Edit all, Delete, Assign, Approve, Link, Export, Override, and Manage.
- Master Data, Task Pack, and Workflow mutations are protected server-side by the role matrix.
- Access changes, role assignments, security changes, and matrix changes are recorded in the access audit log.

## Intentionally not implemented in this update
As requested, these prototype sections are not implemented:
- Client & Job Access
- Approval Authority
- Access Requests
- Access Simulator

## Documents page
- Redesigned to the supplied FlowTrack document-library layout.
- Dynamic summary cards, search, Job/Client/Phase/type/status filters, expand/collapse, Job grouping, pagination, selected-document detail panel, versions, open, and permission-controlled download/delete.
- Uploads are stored using `config('flowtrack.document_disk')` and linked to the selected visible Job/task.
- New-file upload requires Documents → Create.
- Linking an existing document requires Documents → Link.
- Delete and export/download are independently permission controlled.
- Document queries are scoped to the current user's effective access.

## Deploy
```bash
php artisan migrate
php artisan optimize:clear
npm install
npm run build
```
Do not run `migrate:fresh` on an existing installation.
