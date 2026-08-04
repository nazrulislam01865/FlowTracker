# Job Details Documents component state fix

Fixed the Job Details -> Documents Blade component so Livewire-owned document UI state is passed through the component hierarchy.

- `jobDocumentUploads` is forwarded from `Livewire\\Jobs\\Index` -> `x-jobs.detail` -> `x-jobs.detail-documents`.
- `showDocumentPicker` is forwarded through the same hierarchy.
- The Documents component defines safe default props, preventing undefined-variable errors during partial Livewire renders.
- Existing upload, Choose from Documents, Task Pack requirement linking, and upload progress behavior are unchanged.

No database migration is required.
