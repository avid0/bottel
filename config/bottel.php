<?php

return [
    'token' => env('BOTTEL_TOKEN'),
    'api_tls' => env('BOTTEL_API_TLS', true),
    'api_host' => env('BOTTEL_API_HOST', 'api.telegram.org'),
    'api_port' => env('BOTTEL_API_PORT', 443),
    'api_driver' => env('BOTTEL_API_DIRVER', 'curl'),
    'logging_channel' => 'stderr',
];

