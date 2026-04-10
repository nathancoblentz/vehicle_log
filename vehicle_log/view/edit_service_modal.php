<?php
// edit_service_modal.php — Edit Service form
// Uses renderModalStart() / renderModalEnd() for boilerplate
?>

<?php
renderModalStart('editMaintenanceTypeModal', 'Edit Service', 'editMaintenanceTypeForm', 'update_service');
?>

<input type="hidden" name="maintenance_id" id="edit_mt_maintenance_id" value="<?= htmlspecialchars($_POST['maintenance_id'] ?? '') ?>">

<?php
$mode = 'edit';
$prefix = 'edit_mtype_';
include __DIR__ . '/partials/_service_form.php';
renderModalEnd('editMaintenanceTypeForm', 'Save Changes'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('editMaintenanceTypeModal');
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
            // The partial uses prefix 'edit_mtype_'
            const mappings = {
                'maintenance-id': 'edit_mt_maintenance_id',
                'maintenance-code': 'edit_mtype_code',
                'service': 'edit_mtype_type',
                'maintenance-description': 'edit_mtype_description',
                'recommended-interval-miles': 'edit_mtype_interval_miles',
                'recommended-interval-days': 'edit_mtype_interval_days',
                'recommended-cost': 'edit_mtype_cost'
            };

            const intFields = ['edit_mtype_interval_miles', 'edit_mtype_interval_days'];
            for (const [attr, id] of Object.entries(mappings)) {
                const el = document.getElementById(id);
                if (el) {
                    let val = btn.getAttribute('data-' + attr) || '';
                    if (intFields.includes(id) && val !== '') {
                        val = Math.round(parseFloat(val));
                    }
                    el.value = val;
                }
            }

            // Handle is_active checkbox
            const activeCheckbox = document.getElementById('edit_mtype_is_active');
            if (activeCheckbox) {
                activeCheckbox.checked = btn.getAttribute('data-is-active') === '1';
            }
        });
    });
</script>