# Client List Popup and Full-Width Update

## Request

- Use the existing client summary card as a popup when a user selects a client.
- Let the client list use the full page width instead of reserving a permanent right-side preview column.

## Implementation

### Full-width list

`resources/views/livewire/clients/index.blade.php` now renders the metrics, filters, and client table as the only content inside the client layout. The former permanent detail sidebar was removed from the grid.

`resources/css/flowtrack.css` adds a full-width layout override through `ft-clients-layout-full`.

### Client summary modal

Selecting a client row now calls `openClient()` and displays the existing summary information in an accessible modal. The popup includes:

- Client name, country, health, contact, and account manager
- Active Job, open Task, overdue, and outstanding totals
- Active Jobs
- Tasks needing attention
- Links to the full Client, Jobs, Tasks, and all client work

The modal closes through:

- The close button
- Clicking the backdrop
- Pressing Escape

Client rows also support Enter and Space for keyboard opening.

### State handling

`App\Livewire\Clients\Index` now has a dedicated `showClientPreview` state. The page no longer automatically selects the first client, preventing unnecessary detail queries and preventing a popup from appearing on initial load.

The existing **Open client** action still switches to the full client-detail screen. Create, edit, delete, and back-navigation flows reset popup state consistently.

## Regression coverage

`tests/Feature/ClientPreviewModalTest.php` covers:

- Full-width initial list with no preselected client
- Opening and closing the client summary modal
- Switching from the modal to the full client-detail screen

## Deployment

```bash
php artisan optimize:clear
php artisan view:clear
npm run build
php artisan test --filter=ClientPreviewModalTest
```
