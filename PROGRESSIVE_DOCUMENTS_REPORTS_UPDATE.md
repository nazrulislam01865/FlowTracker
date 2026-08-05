# Progressive Documents and Reports rendering

## Documents

- heading, metrics and filter controls render first.
- paginated document groups and relationships load in one `wire:init` request.
- document details and version history load only after a file is selected.
- upload-only Task options load only while the upload modal is open and a Job is selected.
- the four metric queries were replaced by one 30-second per-user aggregate.
- document rows now load a scoped `tasks_count` for each Job instead of loading every Task model just to count them.
- results remain paginated at 10, 25 or 50 rows per page, and Job lookups remain capped at 250.

## Reports

- KPI metrics and Delivery Performance render first.
- Active Jobs by Phase and Team Workload load together in one secondary request.
- chart lists use a bounded, scrollable area so larger datasets do not stretch the entire page.
- KPI and performance grids collapse to one column on narrow phones.

Responsive skeletons preserve the final layout while data loads. Reduced-motion preferences disable skeleton animation. No database migration is required.
