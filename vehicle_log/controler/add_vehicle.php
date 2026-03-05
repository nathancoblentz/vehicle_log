<?php

global $db;

$vin = $_POST['vehicle_VIN']; // Sanitize VIN input

$stmt = $db->prepare("SELECT COUNT(*) FROM vehicles WHERE vehicle_VIN=?"); // Check for duplicate VIN
$stmt->execute([$vin]);

if ($stmt->fetchColumn()) {

    $feedback = [
        'type' => 'error',
        'message' => 'VIN already exists.'
    ];
    return;
}

$stmt = $db->prepare("
INSERT INTO vehicles (vehicle_make, vehicle_model, vehicle_year, vehicle_VIN)
VALUES (?,?,?,?)
");

$stmt->execute([
    $_POST['vehicle_make'],
    $_POST['vehicle_model'],
    $_POST['vehicle_year'],
    $vin
]);

$feedback = [
    'type' => 'success',
    'message' =>
        $_POST['vehicle_make'] . ' ' .
        $_POST['vehicle_model'] . ' (' .
        $_POST['vehicle_year'] . ') added.'
];
