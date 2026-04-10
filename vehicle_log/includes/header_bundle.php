<?php
/**
 * includes/header_bundle.php — Universal UI Header
 * 
 * Bundles logic (init.php), design (head.php), and navigation (nav.php)
 * specifically for HTML views. This avoids repeating 4-5 include lines
 * on every single page.
 */

// 1. First, load the logic and database session
require_once __DIR__ . '/init.php';

// 1.5 Security Guard: Check authentication before sending any HTML to the browser
if (isset($requireAuth) && $requireAuth) {
    if (file_exists(__DIR__ . '/../../login.php')) {
        requireLogin('../../login.php');
    } else {
        requireLogin('login.php');
    }
}

// 2. Output Standard HTML Head (CSS, Meta, Title)
// Note: $title can still be set before including this header_bundle.
if (file_exists(__DIR__ . '/../../includes/head.php')) {
    include_once __DIR__ . '/../../includes/head.php';
} else {
    // Fallback if structure is different
    include_once '../includes/head.php';
}

// 3. Output Main Navigation (Optional)
// Set $hideNav = true; before this include to skip the top navigation bar.
if (!isset($hideNav) || !$hideNav) {
    if (file_exists(__DIR__ . '/../../includes/nav.php')) {
        include_once __DIR__ . '/../../includes/nav.php';
    } else {
        include_once '../includes/nav.php';
    }
}

// 4. Output Hero Section (Optional)
// Use $showHero = true; before this include to display the hero banner.
if (isset($showHero) && $showHero) {
    if (file_exists(__DIR__ . '/../../includes/hero.php')) {
        include_once __DIR__ . '/../../includes/hero.php';
    } else {
        // Fallback if structure is different
        include_once '../includes/hero.php';
    }
}
