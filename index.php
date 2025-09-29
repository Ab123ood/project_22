<?php
/**
 * Application Entry Point
 * Cybersecurity Awareness Platform - درع
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

// Set up error handling
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    $error = [
        'type' => 'PHP Error',
        'severity' => $severity,
        'message' => $message,
        'file' => $file,
        'line' => $line,
        'time' => date('Y-m-d H:i:s')
    ];
    
    error_log('PHP Error: ' . json_encode($error));
    
    if (IS_DEVELOPMENT) {
        echo "<div style='background:#f8d7da;color:#721c24;padding:10px;margin:10px;border:1px solid #f5c6cb;border-radius:4px;'>";
        echo "<strong>PHP Error:</strong> {$message} in {$file} on line {$line}";
        echo "</div>";
    }
    
    return true;
});

// Set up exception handling
set_exception_handler(function ($exception) {
    $error = [
        'type' => 'Uncaught Exception',
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString(),
        'time' => date('Y-m-d H:i:s')
    ];
    
    error_log('Uncaught Exception: ' . json_encode($error));
    
    if (IS_DEVELOPMENT) {
        echo "<div style='background:#f8d7da;color:#721c24;padding:10px;margin:10px;border:1px solid #f5c6cb;border-radius:4px;'>";
        echo "<strong>Uncaught Exception:</strong> {$exception->getMessage()} in {$exception->getFile()} on line {$exception->getLine()}";
        echo "<pre style='margin-top:10px;font-size:12px;'>{$exception->getTraceAsString()}</pre>";
        echo "</div>";
    } else {
        http_response_code(500);
        echo 'An internal server error occurred.';
    }
});

// Autoloader for classes
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/core/' . $class . '.php',
        __DIR__ . '/controllers/' . $class . '.php',
        __DIR__ . '/models/' . $class . '.php',
        __DIR__ . '/middleware/' . $class . '.php',
        __DIR__ . '/services/' . $class . '.php',
    ];
    
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    // Log missing class in development
    if (IS_DEVELOPMENT) {
        error_log("Class not found: {$class}");
    }
});

// Calculate base path for subdirectory installations
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') {
    $basePath = '';
}

// Initialize router and load routes
try {
    $router = new Router();
    require __DIR__ . '/routes.php';
} catch (Exception $e) {
    error_log('Router initialization failed: ' . $e->getMessage());
    http_response_code(500);
    echo 'Application initialization failed.';
    exit;
}

// Parse and normalize URI
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Remove base path from URI if present
if ($basePath && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
    if ($uri === '') {
        $uri = '/';
    }
}

// Apply route guards for access control
try {
    RouteGuards::enforce($uri, $basePath);
} catch (Exception $e) {
    error_log('Route guard enforcement failed: ' . $e->getMessage());
    http_response_code(500);
    echo 'Access control error.';
    exit;
}

// Dispatch the request
try {
    $router->dispatch($uri, $method);
} catch (Exception $e) {
    error_log('Request dispatch failed: ' . $e->getMessage());
    http_response_code(500);
    echo 'Request processing failed.';
    exit;
}
