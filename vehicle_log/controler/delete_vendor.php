<?php
// Handler: Delete Vendor
// Checks for FK constraints before deleting — blocks if any maintenance records reference it.

global $db;

$vendor_id = $_POST['vendor_id'] ?? null;

if (!$vendor_id) {
    $feedback = [
        'type' => 'error',
        'title' => 'Error',
        'message' => 'No vendor ID provided.'
    ];
    return;
}

// Check for maintenance records
$check = $db->prepare("SELECT COUNT(*) FROM maintenance WHERE vendor_id = ?");
$check->execute([$vendor_id]);
$usageCount = $check->fetchColumn();
$check->closeCursor();

if ($usageCount > 0) {
    $feedback = [
        'type' => 'error',
        'title' => 'Cannot Delete',
        'message' => "This vendor has $usageCount maintenance record(s). Remove or reassign those records first."
    ];
    return;
}

// Safe to delete
$stmt = $db->prepare("DELETE FROM vendors WHERE vendor_id = ?");
$stmt->execute([$vendor_id]);
$stmt->closeCursor();

$feedback = [
    'type' => 'success',
    'title' => 'Deleted',
    'message' => 'Vendor deleted successfully.'
];
