<?php

return [
    'workspace_id' => (int) env('FLOWTRACK_WORKSPACE_ID', 1),
    'document_disk' => env('FLOWTRACK_DOCUMENT_DISK', 'public'),
    'super_admin' => [
        'name' => env('SUPER_ADMIN_NAME', 'FlowTrack Super Admin'),
        'email' => env('SUPER_ADMIN_EMAIL', 'admin@example.com'),
        'password' => env('SUPER_ADMIN_PASSWORD'),
    ],
];
