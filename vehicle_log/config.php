<?php
/**
 * config.php — Primary Database Configuration & Connection
 * 
 * Provides global access to the $db (PDO) object and core system constants.
 * Optimized to prevent "headers already sent" errors by omitting the closing ?> tag.
 */

$host     = 'localhost';
$dbname   = 'cpt283coblentz_vehicle_log';
$username = 'cpt283coblentz'; 
$password = 'Pinkyp!321';    
$charset  = 'utf8mb4';

if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // In production, log this instead of using die()
    error_log("Database Connection Failure: " . $e->getMessage());
    die("Database access is currently unavailable.");
}
