# Session, realtime and responsive controls update

- Documents filters are responsive with equal-height controls and no horizontal scrolling.
- Documents, Job Board, Task Board and My Work use double-chevron icon controls for expand/collapse.
- Login is throttled and one account can have only one active device session. A newer login signs out the previous browser with a clear message.
- Session lifetime defaults to 30 minutes and the browser also logs out after 30 minutes of human inactivity, so background polling cannot keep an idle session alive forever.
- Dashboard, Board and My Work use Livewire AJAX polling as a fallback, while Pusher events refresh Dashboard/Board/My Work/Job detail immediately for permitted users.
- Active Master Data lookups are cached for 5 minutes with write invalidation. Dashboard snapshots are cached for 20 seconds and invalidated by notifications.
