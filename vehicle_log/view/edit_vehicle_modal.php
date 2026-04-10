<?php
// edit_vehicle_modal.php — Edit Vehicle form
// Uses renderModalStart() / renderModalEnd() for boilerplate
?>

<?php
renderModalStart('editVehicleModal', 'Edit Vehicle', 'editVehicleForm', 'update_vehicle');
?>

<input type="hidden" name="vehicle_id" id="edit_vehicle_id" value="<?= htmlspecialchars($_POST['vehicle_id'] ?? '') ?>">

<?php
$mode = 'edit';
$prefix = 'edit_vehicle_';
include __DIR__ . '/partials/_vehicle_form.php';
renderModalEnd('editVehicleForm', 'Save Changes'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('editVehicleModal');
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
                'vehicle-id': 'edit_vehicle_id',
                'vehicle-type': 'edit_vehicle_type',
                'vehicle-make': 'edit_vehicle_make',
                'vehicle-model': 'edit_vehicle_model',
                'vehicle-year': 'edit_vehicle_year',
                'vehicle-year-purchased': 'edit_vehicle_year_purchased',
                'vehicle-color': 'edit_vehicle_color',
                'vehicle-vin': 'edit_vehicle_VIN',
                'vehicle-license-tag': 'edit_vehicle_license_tag',
                'vehicle-license-state': 'edit_vehicle_license_state',
                'vehicle-purchase-price': 'edit_vehicle_purchase_price',
                'vehicle-purchase-mileage': 'edit_vehicle_purchase_mileage',
                'vehicle-current-mileage': 'edit_vehicle_current_mileage'
            };

            const mileageFields = ['edit_vehicle_purchase_mileage', 'edit_vehicle_current_mileage'];
            for (const [attr, id] of Object.entries(mappings)) {
                const el = document.getElementById(id);
                if (el) {
                    let val = btn.getAttribute('data-' + attr) || '';
                    if (mileageFields.includes(id) && val !== '') {
                        val = Math.round(parseFloat(val));
                    }
                    el.value = val;
                }
            }

            // Handle is_active checkbox
            const activeCheckbox = document.getElementById('edit_vehicle_is_active');
            if (activeCheckbox) {
                activeCheckbox.checked = btn.getAttribute('data-is-active') === '1';
            }
        });
    });
</script>