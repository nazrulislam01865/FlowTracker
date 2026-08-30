<?php

return [
    'workspace_id' => (int) env('FLOWTRACK_WORKSPACE_ID', 1),
    'document_disk' => env('FLOWTRACK_DOCUMENT_DISK', 'flowtrack_private'),
    'quarantine_disk' => env('FLOWTRACK_QUARANTINE_DISK', 'flowtrack_quarantine'),
    'legacy_document_disks' => array_values(array_filter(array_map('trim', explode(',', env('FLOWTRACK_LEGACY_DOCUMENT_DISKS', 'public,local'))))),
    'upload_security' => [
        'scanner' => env('FLOWTRACK_MALWARE_SCANNER', 'basic'),
        'clamav_binary' => env('FLOWTRACK_CLAMAV_BINARY', 'clamdscan'),
        'scan_timeout_seconds' => (int) env('FLOWTRACK_UPLOAD_SCAN_TIMEOUT', 30),
        'max_file_bytes' => (int) env('FLOWTRACK_UPLOAD_MAX_BYTES', 52428800),
        'zip_max_entries' => (int) env('FLOWTRACK_ZIP_MAX_ENTRIES', 1000),
        'zip_max_uncompressed_bytes' => (int) env('FLOWTRACK_ZIP_MAX_UNCOMPRESSED_BYTES', 536870912),
        'zip_max_ratio' => (int) env('FLOWTRACK_ZIP_MAX_RATIO', 150),
        'quarantine_retention_hours' => (int) env('FLOWTRACK_QUARANTINE_RETENTION_HOURS', 72),
    ],
    'super_admin' => [
        'name' => env('SUPER_ADMIN_NAME', 'FlowTrack Super Admin'),
        'email' => env('SUPER_ADMIN_EMAIL', 'admin@example.com'),
        'password' => env('SUPER_ADMIN_PASSWORD'),
    ],
];
