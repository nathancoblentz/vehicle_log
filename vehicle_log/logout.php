<?php
/**
 * logout.php - Universal Session Termination
 * 
 * Securely destroys the current session and redirects the user
 * to the login portal.
 */

require_once 'includes/init.php';

// Destroy all session data
session_destroy();

// Standard redirection with logout message
header("Location: login.php?msg=logout");
exit();
