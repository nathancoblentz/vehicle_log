<?php
/**
 * config.local.php - Local Database Configuration
 * 
 * This file contains the database connection settings and system-wide
 * constants for the development environment.
 * 
 * NOTE: The closing PHP tag '?>' is intentionally omitted to prevent
 * accidental trailing whitespace from causing "headers already sent" errors.
 */

$host    = 'localhost';
$dbname  = 'cpt283coblentz_vehicle_log';
$user    = 'root';            // ← Development default
$username = 'cpt283coblentz';  // ← System integration username
$password = 'Pinkyp!321';      // ← System integration password
$charset  = 'utf8mb4';

// Root of the project (vehicle_log folder)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}
