<?php
// Dedicated Error Pages Bootstrap: Sets up autoloading, session, error logging, and configuration
if (!defined('TECHSCOUT_BOOTSTRAPPED')) {
    define('TECHSCOUT_BOOTSTRAPPED', true);
    
    $root = dirname(__DIR__);
    if (file_exists($root . '/app/autoload.php')) {
        require_once $root . '/app/autoload.php';
    }
    
    // Register platform error logger
    if (class_exists('\\App\\Core\\ErrorLogger')) {
        \App\Core\ErrorLogger::register();
    }
    
    // Timezone
    date_default_timezone_set('UTC');
    
    // Start session if not active
    if (class_exists('\\App\\Core\\Session')) {
        \App\Core\Session::start();
    }
    
    // Configure base URL dynamically
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
        || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on')
        || (!empty($_SERVER['HTTP_CF_VISITOR']) && str_contains($_SERVER['HTTP_CF_VISITOR'], 'https'))
        || ((int)($_SERVER['SERVER_PORT'] ?? 0) === 443);
    $protocol = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    // If scriptDir ends in /errors, climb up one level
    $basePath = preg_replace('#/(errors|public/errors)$#', '', $scriptDir);
    $detectedBaseUrl = rtrim($protocol . '://' . $host . $basePath, '/');
    
    if (class_exists('\\App\\Core\\Config')) {
        \App\Core\Config::set('app_url', $detectedBaseUrl);
    }
}
