<?php
// Handler: Delete Vehicle
// Checks for FK constraints before deleting — blocks if any maintenance or fuel records reference it.

global $db;

$vehicle_id = $_POST['vehicle_id'] ?? null;

if (!$vehicle_id) {
    $feedback = [
        'type' => 'error',
        'title' => 'Error',
        'message' => 'No vehicle ID provided.'
    ];
    return;
}

// Check for maintenance records
$checkMaint = $db->prepare("SELECT COUNT(*) FROM maintenance WHERE vehicle_id = ?");
$checkMaint->execute([$vehicle_id]);
$maintCount = $checkMaint->fetchColumn();
$checkMaint->closeCursor();

// Check for fuel records
$checkFuel = $db->prepare("SELECT COUNT(*) FROM fuel WHERE vehicle_id = ?");
$checkFuel->execute([$vehicle_id]);
$fuelCount = $checkFuel->fetchColumn();
$checkFuel->closeCursor();

if ($maintCount > 0 || $fuelCount > 0) {
    $feedback = [
        'type' => 'error',
        'title' => 'Cannot Delete',
        'message' => "This vehicle has $maintCount maintenance record(s) and $fuelCount fuel record(s). Remove those records first."
    ];
    return;
}

// Safe to delete
$stmt = $db->prepare("DELETE FROM vehicles WHERE vehicle_id = ?");
$stmt->execute([$vehicle_id]);
$stmt->closeCursor();

$feedback = [
    'type' => 'success',
    'title' => 'Deleted',
    'message' => 'Vehicle deleted successfully.'
];
