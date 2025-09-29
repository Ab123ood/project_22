<?php
/**
 * Application Configuration
 * Central configuration file for the cybersecurity platform
 */

// Environment detection
$isDevelopment = ($_SERVER['SERVER_NAME'] ?? '') === 'localhost' || 
                 ($_SERVER['HTTP_HOST'] ?? '') === 'localhost' ||
                 str_contains($_SERVER['SERVER_NAME'] ?? '', 'localhost');

// Error reporting based on environment
if ($isDevelopment) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
}

// Application constants
define('APP_PATH', realpath(__DIR__ . '/..'));
define('VIEW_PATH', APP_PATH . '/views');
define('IS_DEVELOPMENT', $isDevelopment);

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', !$isDevelopment);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Timezone
date_default_timezone_set('Asia/Riyadh');

// Database configuration
$dbConfig = [
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'port' => (int)($_ENV['DB_PORT'] ?? 3306),
    'name' => $_ENV['DB_NAME'] ?? 'cybersecurity_platform',
    'user' => $_ENV['DB_USER'] ?? 'root',
    'pass' => $_ENV['DB_PASS'] ?? '',
    'charset' => 'utf8mb4'
];

// Application configuration
$config = [
    'app' => [
        'name' => 'درع - منصة الوعي السيبراني',
        'version' => '1.0.0',
        'environment' => $isDevelopment ? 'development' : 'production',
        'debug' => $isDevelopment,
        'timezone' => 'Asia/Riyadh',
        'locale' => 'ar',
        'charset' => 'utf-8'
    ],
    'db' => $dbConfig,
    'security' => [
        'session_lifetime' => 3600, // 1 hour
        'csrf_token_lifetime' => 1800, // 30 minutes
        'password_min_length' => 8,
        'max_login_attempts' => 5,
        'lockout_duration' => 900 // 15 minutes
    ],
    'upload' => [
        'max_file_size' => 5 * 1024 * 1024, // 5MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
        'upload_path' => APP_PATH . '/uploads'
    ],
    'pagination' => [
        'default_per_page' => 20,
        'max_per_page' => 100
    ]
];

// Store configuration in global variable
$GLOBALS['config'] = $config;

return $config;
