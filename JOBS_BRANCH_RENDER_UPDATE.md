# Jobs branch-specific rendering update

The Jobs Livewire component now renders and queries only the active screen.

## Jobs list

The list branch loads only:

- Paginated Jobs and summary counts
- Job filters and filter options
- Client, phase, owner, priority, health and status options used by the table

Create-form defaults, workflows, starting phases, products, categories and attachments are not initialized or rendered while the list is showing.

## Create Job

Create data is initialized only when `openCreate()` is called or the page is opened with `?create=1`. The create branch then loads:

- Visible active clients
- Active workflows, phases and Task Pack templates
- Assignable users
- Products, categories and priorities

The Jobs table and its datasets are not queried or rendered in this branch. Closing the form clears all create-only state before returning to the list.

## Job and Task details

Job detail and Task detail retain separate data methods, so list/create queries do not run while a detail screen is open. Stable `wire:key` values prevent Livewire from morphing one screen's DOM into another.

No database migration is required.
