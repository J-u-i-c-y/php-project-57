<?php

// Define commonly used host constants to avoid magic literals.
if (! defined('LOCALHOST')) {
    define('LOCALHOST', '127.0.0.1');
}

if (! defined('LARAVEL_LOG_FILE')) {
    define('LARAVEL_LOG_FILE', env('LARAVEL_LOG_FILE', 'logs/laravel.log'));
}

if (! defined('PROFILE_URI')) {
    define('PROFILE_URI', '/profile');
}
