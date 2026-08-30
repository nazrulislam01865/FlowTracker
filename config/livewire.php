<?php

return [
    // Keep Livewire's temporary-upload gate format-neutral. Each component
    // validates its own permanent file types, including EPS / ESP attachments.
    // This prevents MIME guessing from rejecting those formats too early.
    'temporary_file_upload' => [
        'rules' => ['required', 'file', 'max:20480'],
    ],
    'navigate' => [
        'show_progress_bar' => false,
    ],

    // Central placeholder used only by page/section components that are safe
    // to defer or lazy-load. Route-sensitive Create/Detail/editor components
    // mount immediately and progressively load only their heavy inner sections.
    'component_placeholder' => 'livewire.shared.page-placeholder',

];
