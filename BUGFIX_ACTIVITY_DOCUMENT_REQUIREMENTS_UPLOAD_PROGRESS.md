# Activity, Task Pack document requirements and upload progress fix

- Reworked Task and Job activity feeds into readable audit entries with actor, change/comment type, human-readable event label, relative time and exact timestamp.
- Comments remain separated from history while the All tab combines both.
- Added Livewire upload percentage bars to Task attachments, Job Documents, New Job attachments and the Documents upload modal.
- Job document requirements now read explicit Task Pack item requirements and compatible generated-task Task Pack requirements.
- Legacy phase-level document requirements are promoted into the mapped Task Pack when that pack has no explicit requirement, preserving Task Pack as the runtime source of truth.
- Missing legacy document categories such as `Purchase Order` are promoted into dynamic Master Data before being linked to the Task Pack.
