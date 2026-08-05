# Board sticky-header data-source update

## Behaviour

- **My Work** and **Operations Board → Task Board** now build their sticky status lanes only from active **Master Data → Task Statuses** records, preserving `sort_order` and then name order from `MasterDataService`.
- A configured **Not Start** or **Not Started** status is always displayed as the first lane.
- When no not-start status exists in active master data, the board prepends the canonical **Not Started** lane so not-yet-started tasks remain visible and draggable.
- Task statuses found only in task rows are no longer appended to the board header, preventing stale or accidental values from changing board columns.
- **Operations Board → Job Board** continues to use active phases from the selected workflow, ordered deterministically by phase `sequence` and then phase ID.

## Files

- `app/Support/BoardLaneResolver.php`
- `app/Livewire/MyWork/Index.php`
- `app/Livewire/Board/Index.php`
- `app/Services/BoardService.php`
- `resources/views/livewire/my-work/index.blade.php`
- `tests/Unit/BoardLaneResolverTest.php`

## Deployment

```bash
php artisan optimize:clear
php artisan view:clear
php artisan test --filter=BoardLaneResolverTest
```
