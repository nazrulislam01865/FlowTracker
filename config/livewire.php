<?php

return [
    // Keep Livewire's temporary-upload gate aligned with FlowTrack's document
    // validation, including Branding's WebP/ICO assets. Otherwise Livewire can
    // reject an allowed file before the component can run its own validation.
    'temporary_file_upload' => [
        'rules' => ['required', 'file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp,ico,zip,txt,csv,ai'],
    ],
    'navigate' => [
        'show_progress_bar' => false,
    ],
];
