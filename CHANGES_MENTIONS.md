# User mentions update

Added `@` user mentions to:

- Create Job request description
- Job detail description
- Task detail description
- Job comments
- Task comments

Behavior:

- Typing `@` opens a filtered user suggestion menu.
- Selecting a user inserts a unique handle such as `@john.smith.12`.
- Saved mentions render as the user's current display name.
- Mentioned users receive a dedicated notification when they can access the related Job or Task.
- Generic participant notifications exclude mentioned users to avoid duplicates.
- Mention data is parsed without a new database migration.

Performance:

- The existing branch-specific Livewire `render()` refactor remains in place.
- Mention options load only for create, Job-detail or Task-detail branches.
- Mention rendering uses one request-scoped active-user lookup rather than one query per activity entry.
- A Livewire loading overlay and stable `wire:key` values are included.

Validation:

- PHP syntax checked across all application and test PHP files.
- Source and public-build JavaScript syntax checked with Node.
- CSS source was split into the four generated production CSS files.
- The existing public build assets were refreshed directly because the supplied package registry did not provide `laravel-vite-plugin` during this session.
