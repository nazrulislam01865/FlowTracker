# Task Pack assignee + Job document linking fix

- Generated Job tasks now take their initial assignee from the Task Pack item (explicit default assignee, or the configured default department resolution).
- Existing unassigned/generated tasks are repaired by migration `2026_08_04_001000_sync_current_task_pack_assignments.php` without overwriting deliberate manual reassignments.
- Editing a Task Pack immediately synchronizes generated tasks that still follow the Task Pack assignment.
- Job Details document uploads are now finalized automatically after Livewire's temporary upload finishes, so files cannot appear uploaded and then disappear because they remained temporary.
- Uploaded documents are persisted to the configured FlowTrack document disk and linked to the exact selected Task Pack task/requirement.
- After upload, Job Details re-reads required-document state and selects the next missing requirement.
