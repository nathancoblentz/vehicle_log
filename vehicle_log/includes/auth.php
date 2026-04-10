<?php
/**
 * includes/auth.php - Authentication Guards
 * 
 * Centralized logic for requiring user login and administrator roles.
 * Updated to support context-aware redirects (from subdirectories).
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Ensures the user is logged in before proceeding.
 * @param string $loginPath Relative path to the login.php page.
 * @param string|null $msg Optional message code to pass to the login page (e.g. 'auth').
 */
function requireLogin(string $loginPath = 'login.php', ?string $msg = 'auth'): void
{
    if (!isset($_SESSION['user_id'])) {
        $redirectUrl = $msg ? "$loginPath?msg=" . urlencode($msg) : $loginPath;
        header("Location: $redirectUrl");
        exit();
    }
}

/**
 * Ensures the user is an administrator before proceeding.
 * @param string $loginPath Relative path to the login.php page (if redirecting).
 */
function requireAdmin(string $loginPath = '../login.php'): void
{
    requireLogin($loginPath);
    
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        http_response_code(403);
        echo "<div style='color:red; font-family:sans-serif; text-align:center; margin-top:50px;'>";
        echo "<h2>403 Forbidden</h2>";
        echo "<p>Access Denied: You do not have the administrative privileges required to view this directory.</p>";
        echo "<p><a href='$loginPath'>Login with an Admin Account</a></p>";
        echo "</div>";
        exit();
    }
}
