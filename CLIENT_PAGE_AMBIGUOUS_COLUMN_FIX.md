# Client page ambiguous column fix

Fixed the `/clients` SQL error caused by the `Client::tasks()` HasManyThrough relation joining `tasks` and `flow_jobs`, both of which contain columns such as `completed_at`.

Changes:
- Qualified task aggregate/filter columns as `tasks.completed_at`, `tasks.due_date`, `tasks.status`, and `tasks.needs_attention`.
- Qualified job aggregate columns as `flow_jobs.completed_at`, `flow_jobs.delivery_date`, `flow_jobs.needs_attention`, and `flow_jobs.health` where appropriate.
- Qualified the task access-scope assignee condition as `tasks.assignee_id` so it is safe in joined relationship queries.

No migration required.
