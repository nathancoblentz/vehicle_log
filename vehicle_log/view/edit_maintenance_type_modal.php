<?php
// edit_maintenance_type_modal.php — Edit Maintenance Type form
// Uses renderModalStart() / renderModalEnd() for boilerplate

renderModalStart('editMaintenanceTypeModal', 'Edit Maintenance Type', 'editMaintenanceTypeForm', 'update_maintenance_type');
?>

<input type="hidden" name="maintenance_id" id="edit_mt_maintenance_id">

<!-- Maintenance Code -->
<div class="col-md-6">
    <label for="edit_mt_maintenance_code" class="form-label">Maintenance Code</label>
    <input type="text" class="form-control" id="edit_mt_maintenance_code" name="maintenance_code">
</div>

<!-- Maintenance Type -->
<div class="col-md-6">
    <label for="edit_mt_maintenance_type" class="form-label">Maintenance Type</label>
    <input type="text" class="form-control" id="edit_mt_maintenance_type" name="maintenance_type" required>
</div>

<!-- Description -->
<div class="col-12">
    <label for="edit_mt_maintenance_description" class="form-label">Maintenance Description</label>
    <textarea class="form-control" id="edit_mt_maintenance_description" name="maintenance_description"
        rows="3"></textarea>
</div>

<!-- Recommended Interval Miles -->
<div class="col-md-4">
    <label for="edit_mt_recommended_interval_miles" class="form-label">Recommended Interval Miles</label>
    <input type="number" class="form-control" id="edit_mt_recommended_interval_miles" name="recommended_interval_miles"
        min="0">
</div>

<!-- Recommended Interval Days -->
<div class="col-md-4">
    <label for="edit_mt_recommended_interval_days" class="form-label">Recommended Interval Days</label>
    <input type="number" class="form-control" id="edit_mt_recommended_interval_days" name="recommended_interval_days"
        min="0">
</div>

<!-- Recommended Cost -->
<div class="col-md-4">
    <label for="edit_mt_recommended_cost" class="form-label">Recommended Cost</label>
    <div class="input-group">
        <span class="input-group-text">$</span>
        <input type="number" step="0.01" class="form-control" id="edit_mt_recommended_cost" name="recommended_cost"
            min="0">
    </div>
</div>

<!-- Active Status -->
<div class="col-12">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="edit_mt_is_active" name="is_active" value="1">
        <label class="form-check-label" for="edit_mt_is_active">Active</label>
    </div>
</div>

<?php renderModalEnd('editMaintenanceTypeForm', 'Save Changes'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('editMaintenanceTypeModal');
        if (!modal) return;
        modal.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            if (!btn) return;
            const map = {
                'maintenance_id': 'edit_mt_maintenance_id',
                'maintenance_code': 'edit_mt_maintenance_code',
                'maintenance_type': 'edit_mt_maintenance_type',
                'maintenance_description': 'edit_mt_maintenance_description',
                'recommended_interval_miles': 'edit_mt_recommended_interval_miles',
                'recommended_interval_days': 'edit_mt_recommended_interval_days',
                'recommended_cost': 'edit_mt_recommended_cost'
            };
            for (const [dataKey, elId] of Object.entries(map)) {
                const el = document.getElementById(elId);
                if (el) {
                    const val = btn.getAttribute('data-' + dataKey.replace(/_/g, '-')) || '';
                    if (el.tagName === 'TEXTAREA') {
                        el.textContent = val;
                    } else {
                        el.value = val;
                    }
                }
            }
            // Handle is_active checkbox
            const activeCheckbox = document.getElementById('edit_mt_is_active');
            if (activeCheckbox) {
                activeCheckbox.checked = btn.getAttribute('data-is-active') === '1';
            }
        });
    });
</script>