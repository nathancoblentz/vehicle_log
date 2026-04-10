<?php
/**
 * includes/init.php — Universal Backend Core Logic
 * 
 * Centralizes session management, database connectivity, security helpers,
 * and form handling. This file is intended for LOGIC ONLY and provides
 * ZERO output, making it safe for use in redirects.
 */

// 1. Mandatory Session Initialization
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Global Path Constants (Standardized)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// 3. Database Connectivity (Provides $db PDO object)
require_once BASE_PATH . '/config.php';

// 4. Security & Authentication Guards (Permissions)
require_once __DIR__ . '/auth.php';

// 5. Utility & View Rendering Functions (The "Glue")
require_once __DIR__ . '/functions.php';

// 6. Global Communication Object (Success/Error Messages)
$feedback = null; 

// 7. Global Form Handler (Processes any POST requests found in functions.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    addHandlers();
}
