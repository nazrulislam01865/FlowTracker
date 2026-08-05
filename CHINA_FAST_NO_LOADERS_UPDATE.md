# China Fast / No Loaders Update

- Removed all Livewire lazy page mounts and lazy/deferred islands.
- Removed placeholder infrastructure and visible Load More controls.
- Removed Quick Filter UI, state, count queries, and query branches from Jobs, Board, My Work, and Clients.
- Removed the external Pusher browser script; notifications use lightweight same-origin polling.
- Kept normal filters and a fixed 60-card cap on Board/My Work to avoid oversized responses.
