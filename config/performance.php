<?php

return [
    'enabled' => env('PERFORMANCE_MONITORING_ENABLED', true),
    'sample_rate' => (float) env('PERFORMANCE_SAMPLE_RATE', 1.0),
    'slow_request_ms' => (int) env('PERFORMANCE_SLOW_REQUEST_MS', 750),
    'slow_query_ms' => (int) env('PERFORMANCE_SLOW_QUERY_MS', 150),
    'slow_query_total_ms' => (int) env('PERFORMANCE_SLOW_QUERY_TOTAL_MS', 400),
    'slow_outgoing_ms' => (int) env('PERFORMANCE_SLOW_OUTGOING_MS', 500),
    'log_all_requests' => env('PERFORMANCE_LOG_ALL_REQUESTS', false),
    'server_timing' => env('PERFORMANCE_SERVER_TIMING', false),
    'include_query_sql' => env('PERFORMANCE_INCLUDE_QUERY_SQL', false),
];
