<?php
// add_maintenance_modal.php — Add Maintenance form fields only
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
$mileageMapJSON = json_encode($vehicleMileageMap);

require_once __DIR__ . '/../models/ServiceModel.php';
$maintenanceTypes = ServiceModel::getServiceDropdowns($db);

// Create JSON array mapping maintenance_id to recommended_cost for JS lookup
$maintenanceCostMap = [];
$maintenanceDescMap = [];
foreach ($maintenanceTypes as $t) {
    if (isset($t['recommended_cost'])) {
        $maintenanceCostMap[$t['maintenance_id']] = $t['recommended_cost'];
    }
    if (!empty($t['maintenance_description'])) {
        $maintenanceDescMap[$t['maintenance_id']] = $t['maintenance_description'];
    }
}
$costMapJSON = json_encode($maintenanceCostMap);
$descMapJSON = json_encode($maintenanceDescMap);

require_once __DIR__ . '/../models/VendorModel.php';
$vendors = VendorModel::getVendorDropdowns($db);
?>

<?php
renderModalStart('addMaintenanceModal', 'Add Maintenance Record', 'addMaintenanceForm', 'add_maintenance');
$mode = 'add';
$prefix = 'add_maint_';
include __DIR__ . '/partials/_maintenance_form.php';
renderModalEnd('addMaintenanceForm', 'Add Maintenance'); ?>