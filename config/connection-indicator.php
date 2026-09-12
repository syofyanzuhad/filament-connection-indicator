<?php

// config/connection-indicator.php
return [
    /*
    |--------------------------------------------------------------------------
    | Tooltip Labels
    |--------------------------------------------------------------------------
    | Override per locale or publish this file with `vendor:publish`.
    */
    'labels' => [
        'checking' => 'Checking...',
        'online' => 'Online',
        'moderate' => 'Slow Connection',
        'slow' => 'Very Slow',
        'offline' => 'Offline',
    ],

    /*
    |--------------------------------------------------------------------------
    | Poll Interval (ms)
    |--------------------------------------------------------------------------
    | How often the client re-checks the connection in the background.
    | Default: 30 000 ms (30 s).
    */
    'poll_interval' => 30000,
];
