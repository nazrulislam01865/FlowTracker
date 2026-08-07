# Board and mention fixes

## Board 500 error

`BoardService::jobs()` now eager-loads `latestActivity` with fully qualified
`activities.*` column names. This prevents MySQL from treating `subject_type`
and `subject_id` as ambiguous inside Laravel's `latestOfMany()` join.

## @mention autocomplete

- The suggestion popup is mounted under `document.body`, outside Livewire's
  morph-managed component markup.
- Mention inputs are initialized after normal page load, Livewire init,
  Livewire navigation, DOM mutation and Livewire morph updates.
- Removed inputs clean up their popup and event listeners.
- Job and task mention lists include all active users except the current user.
- The compiled public JavaScript asset has a new filename so browsers do not
  reuse the previous cached mention script.

## After copying the project

Run:

```bash
php artisan optimize:clear
```

If a Vite development server is already running, stop and start it again.
The production `public/build` asset is already included in this archive.
