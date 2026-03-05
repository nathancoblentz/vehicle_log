<?php
// Handler: Deactivate Vehicle (set is_active = 0)

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

$stmt = $db->prepare("UPDATE vehicles SET is_active = 0 WHERE vehicle_id = ?");
$stmt->execute([$vehicle_id]);
$stmt->closeCursor();

$feedback = [
    'type' => 'success',
    'title' => 'Vehicle Deactivated',
    'message' => 'The vehicle has been set to inactive. It will no longer appear in active searches.'
];
