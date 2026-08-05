# Client & Task UI fix

- Task property editors now open only from the pencil icon and use a prototype-style highlighted field with a readable popover editor.
- Client three-dot menus now provide View, Edit and Delete actions according to the role matrix.
- Added a dedicated client detail view with contact summary, edit/delete controls and active Jobs.
- Clients with Job history are archived instead of breaking the restrictive client foreign key; unused clients are deleted.
- New Job launched from a client detail page preselects that client.
