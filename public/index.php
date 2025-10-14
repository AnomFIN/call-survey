<?php
/**
 * Call-Survey ULTRALIGHT - Main Entry Point
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/../config.php';

// Set timezone
date_default_timezone_set($config['app']['timezone']);

// Initialize database
$db = \CallSurvey\Database::getInstance($config['database']);

// Simple router
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove query string
$request = strtok($request, '?');

// Route handling
if ($request === '/' || $request === '/index.php') {
    require __DIR__ . '/dashboard.php';
} elseif (strpos($request, '/survey/') === 0) {
    require __DIR__ . '/survey.php';
} elseif (strpos($request, '/webhook/') === 0) {
    require __DIR__ . '/webhook.php';
} elseif (strpos($request, '/api/') === 0) {
    require __DIR__ . '/api.php';
} else {
    http_response_code(404);
    echo '404 - Page Not Found';
}
