<?php
// add_vehicle_modal.php — Add Vehicle form fields only
// Boilerplate handled by renderModalStart() / renderModalEnd()?>

<?php
renderModalStart('addVehicleModal', 'Add Vehicle', 'addVehicleForm', 'add_vehicle');
$mode = 'add';
$prefix = 'add_vehicle_';
include __DIR__ . '/partials/_vehicle_form.php';
renderModalEnd('addVehicleForm', 'Save Vehicle'); ?>