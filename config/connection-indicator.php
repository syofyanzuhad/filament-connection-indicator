<?php

// config/connection-indicator.php
return [
    /*
    |--------------------------------------------------------------------------
    | Indicator UI Style
    |--------------------------------------------------------------------------
    | The visual appearance of the connection indicator.
    | Options:
    |   - 'dot'  : Pulsing signal dot with ping animation (default)
    |   - 'bars' : 4-tier vertical signal bars (cellular / Wi-Fi style)
    */
    'style' => 'dot',

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
