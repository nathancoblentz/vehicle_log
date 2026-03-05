<?php
// Handler: Delete Maintenance Type
// Checks for FK constraint before deleting — blocks if any maintenance records reference it.

global $db;

$maintenance_id = $_POST['maintenance_id'] ?? null;

if (!$maintenance_id) {
    $feedback = [
        'type' => 'error',
        'title' => 'Error',
        'message' => 'No maintenance type ID provided.'
    ];
    return;
}

// Check if any maintenance records reference this type
$checkStmt = $db->prepare("SELECT COUNT(*) FROM maintenance WHERE maintenance_type_id = ?");
$checkStmt->execute([$maintenance_id]);
$count = $checkStmt->fetchColumn();
$checkStmt->closeCursor();

if ($count > 0) {
    $feedback = [
        'type' => 'error',
        'title' => 'Cannot Delete',
        'message' => "This maintenance type is referenced by $count maintenance record(s). Remove or reassign those records first."
    ];
    return;
}

// Safe to delete
$stmt = $db->prepare("DELETE FROM maintenance_type WHERE maintenance_id = ?");
$stmt->execute([$maintenance_id]);
$stmt->closeCursor();

$feedback = [
    'type' => 'success',
    'title' => 'Deleted',
    'message' => 'Maintenance type deleted successfully.'
];
