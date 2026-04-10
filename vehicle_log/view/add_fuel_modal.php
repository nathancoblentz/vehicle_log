<?php
// add_fuel_modal.php — Add Fuel form fields only
// Boilerplate handled by renderModalStart() / renderModalEnd()

global $db;
require_once __DIR__ . '/../models/VehicleModel.php';
$vehicles = VehicleModel::getVehicleDropdowns($db);

// Create JSON array mapping vehicle_id to vehicle_current_mileage for JS lookup
$vehicleMileageMap = [];
foreach ($vehicles as $v) {
    if (isset($v['vehicle_current_mileage'])) {
        $vehicleMileageMap[$v['vehicle_id']] = (int) round($v['vehicle_current_mileage']);
    }
}
$mileageMapJSON = json_encode($vehicleMileageMap);?>

<?php
renderModalStart('addFuelModal', 'Add Fuel Record', 'addFuelForm', 'add_fuel');
$mode = 'add';
$prefix = 'add_fuel_';
include __DIR__ . '/partials/_fuel_form.php';
renderModalEnd('addFuelForm', 'Add Fuel'); ?>