<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. Feel free to adjust these settings as needed.
    |
    */

    'paths' => ['api/*'], // Specify API paths or '*' for all routes.

    'allowed_methods' => ['*'], // '*' allows all methods (GET, POST, etc.).

    'allowed_origins' => ['*'], // Frontend URLs.

    'allowed_origins_patterns' => [], // Regex patterns for matching origins.

    'allowed_headers' => ['*'], // '*' allows all headers.

    'exposed_headers' => [], // Headers exposed to the browser.

    'max_age' => 0, // Cache duration in seconds (0 disables caching).

    'supports_credentials' => false, // Enable or disable cookies/auth.
];
