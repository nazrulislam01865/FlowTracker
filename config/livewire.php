<?php

return [
    // Keep Livewire's temporary-upload gate aligned with FlowTrack's document
    // validation. Otherwise Livewire can reject a valid <=20 MB document before
    // the component has a chance to show the field-level validation message.
    'temporary_file_upload' => [
        'rules' => ['required', 'file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv,ai'],
    ],
    'navigate' => [
        'show_progress_bar' => false,
    ],
];
