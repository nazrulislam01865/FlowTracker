# Workflow phase edit + Job workflow task open fixes

## Fixed
- Editing an existing workflow phase no longer requires the UI to submit `sequence`; the service preserves the phase's current sequence.
- Creating a new workflow phase still appends it after the current last phase when no sequence is supplied.
- Opening a task from Job Details > Workflow no longer fails because Blade component upload state is missing.
- Task document upload/picker Livewire state is explicitly passed into the task-detail component and upload counts are null-safe.
- Task document transient state is reset when a different task is opened.

No database migration is required for these fixes.
