<?php
// edit_fuel_modal.php — Edit Fuel form
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
?>

<?php
renderModalStart('editFuelModal', 'Edit Fuel Record', 'editFuelForm', 'update_fuel');
?>

<input type="hidden" name="fuel_id" id="edit_fuel_id" value="<?= htmlspecialchars($_POST['fuel_id'] ?? '') ?>">

<?php
$mode = 'edit';
$prefix = 'edit_fuel_';
include __DIR__ . '/partials/_fuel_form.php';
renderModalEnd('editFuelForm', 'Save Changes'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('editFuelModal');
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
            const mappings = {
                'fuel-id': 'edit_fuel_id',
                'vehicle-id': 'edit_fuel_vehicle_id',
                'fuel-date': 'edit_fuel_date',
                'fuel-mileage': 'edit_fuel_mileage',
                'fuel-payment-method': 'edit_fuel_payment_method',
                'fuel-gallons': 'edit_fuel_gallons',
                'fuel-cost-per-gallon': 'edit_fuel_cost_per_gallon',
                'fuel-source': 'edit_fuel_source',
                'fuel-receipt-url': 'edit_fuel_receipt_url',
                'fuel-notes': 'edit_fuel_notes'
            };

            for (const [attr, id] of Object.entries(mappings)) {
                const el = document.getElementById(id);
                if (el) {
                    let val = btn.getAttribute('data-' + attr) || '';
                    if (id === 'edit_fuel_mileage' && val !== '') {
                        val = Math.round(parseFloat(val));
                    }
                    el.value = val;
                }
            }

            // Set the hidden vehicle_id input for submission
            const hiddenVehicle = document.getElementById('edit_fuel_vehicle_id_hidden');
            if (hiddenVehicle) {
                hiddenVehicle.value = btn.getAttribute('data-vehicle-id') || '';
            }

            // Trigger the total cost calculation in the partial
            const gallonsInput = document.getElementById('edit_fuel_gallons');
            if (gallonsInput) {
                gallonsInput.dispatchEvent(new Event('input'));
            }
        });
    });
</script>