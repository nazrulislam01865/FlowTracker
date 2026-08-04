# Task Pack & Workflow Setup redesign

Updated on 2026-08-04 to match the supplied FlowTrack reference screens.

- Task Pack Setup is now a dynamic two-column card view with live totals.
- Add/Edit Task Pack is a dedicated full-page Livewire form with dynamic task sequence add/remove/reorder and database-backed assignee, department, priority and document-category options.
- Workflow Setup now matches the supplied template/sidebar/table layout with live summary metrics.
- New/Edit Workflow is a dedicated page; New Workflow can start blank or copy the selected template's phase configuration.
- Workflow phases remain fully database-driven and are editable in the supplied modal layout, including Task Pack, required document, start/skip/automatic rules and sequence ordering.
- Existing SQL-aligned models/services and legacy compatibility mirrors are preserved. No new migration is required for this UI update.
