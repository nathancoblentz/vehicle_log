<?php
// edit_maintenance_modal.php — Edit Maintenance form
// Uses renderModalStart() / renderModalEnd() for boilerplate

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
renderModalStart('editMaintenanceModal', 'Edit Maintenance Record', 'editMaintenanceForm', 'update_maintenance');
?>

<input type="hidden" name="maintenance_id" id="edit_maintenance_id" value="<?= htmlspecialchars($_POST['maintenance_id'] ?? '') ?>">

<?php
$mode = 'edit';
$prefix = 'edit_maint_';
include __DIR__ . '/partials/_maintenance_form.php';
renderModalEnd('editMaintenanceForm', 'Save Changes'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('editMaintenanceModal');
        if (!modal) return;
        
        modal.addEventListener('show.bs.modal', function (e) {
            // If we are recovering from a failed POST, do NOT overwrite fields with original DB values.
            if (window.isErrorRecovery) {
                window.isErrorRecovery = false; // Reset for next time
                return;
            }

            const btn = e.relatedTarget;
            if (!btn) return;
            
            // Map data attributes to input IDs
            // The partial uses prefix 'edit_maint_'
            const mappings = {
                'maintenance-id': 'edit_maintenance_id',
                'vehicle-id': 'edit_maint_vehicle_id',
                'service-id': 'edit_maint_type_id',
                'maintenance-date': 'edit_maint_date',
                'maintenance-mileage': 'edit_maint_mileage',
                'maintenance-cost': 'edit_maint_cost',
                'vendor-id': 'edit_maint_vendor_id',
                'maintenance-status': 'edit_maint_status',
                'maintenance-description': 'edit_maint_description'
            };

            for (const [attr, id] of Object.entries(mappings)) {
                const el = document.getElementById(id);
                if (el) {
                    let val = btn.getAttribute('data-' + attr) || '';
                    if (id === 'edit_maint_mileage' && val !== '') {
                        val = Math.round(parseFloat(val));
                    }
                    el.value = val;
                }
            }

            // Set the hidden vehicle_id input for submission
            const hiddenVehicle = document.getElementById('edit_maint_vehicle_id_hidden');
            if (hiddenVehicle) {
                hiddenVehicle.value = btn.getAttribute('data-vehicle-id') || '';
            }

            // Trigger the total cost / description calculation in the partial
            const typeSelect = document.getElementById('edit_maint_type_id');
            if (typeSelect) {
                typeSelect.dispatchEvent(new Event('change'));
            }
        });
    });
</script>