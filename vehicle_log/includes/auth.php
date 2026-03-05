<?php
session_start();

// check if user is logged in
function requireLogin() {
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

// check if user is admin
function requireAdmin() {
    requireLogin();
    if ($_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        exit('Access denied. Admins only.');
    }
}
