<?php
// edit_vehicle_modal.php — Edit Vehicle form
// Uses renderModalStart() / renderModalEnd() for boilerplate

renderModalStart('editVehicleModal', 'Edit Vehicle', 'editVehicleForm', 'update_vehicle');
?>

<input type="hidden" name="vehicle_id" id="edit_vehicle_id">

<!-- Vehicle Type -->
<div class="col-md-6">
    <label for="edit_vehicle_type" class="form-label">Vehicle Type</label>
    <input type="text" class="form-control" id="edit_vehicle_type" name="vehicle_type" required>
</div>

<!-- Vehicle Make -->
<div class="col-md-6">
    <label for="edit_vehicle_make" class="form-label">Vehicle Make</label>
    <input type="text" class="form-control" id="edit_vehicle_make" name="vehicle_make" required>
</div>

<!-- Vehicle Model -->
<div class="col-md-6">
    <label for="edit_vehicle_model" class="form-label">Vehicle Model</label>
    <input type="text" class="form-control" id="edit_vehicle_model" name="vehicle_model" required>
</div>

<!-- Vehicle Year -->
<div class="col-md-3">
    <label for="edit_vehicle_year" class="form-label">Vehicle Year</label>
    <select id="edit_vehicle_year" name="vehicle_year" class="form-select" required>
        <option value="">Select Year</option>
        <?php for ($year = date('Y'); $year >= 1980; $year--): ?>
            <option value="<?= $year ?>">
                <?= $year ?>
            </option>
        <?php endfor; ?>
    </select>
</div>

<!-- Year Purchased -->
<div class="col-md-3">
    <label for="edit_vehicle_year_purchased" class="form-label">Year Purchased</label>
    <select id="edit_vehicle_year_purchased" name="vehicle_year_purchased" class="form-select" required>
        <option value="">Select Year</option>
        <?php for ($year = date('Y'); $year >= 1980; $year--): ?>
            <option value="<?= $year ?>">
                <?= $year ?>
            </option>
        <?php endfor; ?>
    </select>
</div>

<!-- Vehicle Color -->
<div class="col-md-4">
    <label for="edit_vehicle_color" class="form-label">Color</label>
    <select id="edit_vehicle_color" name="vehicle_color" class="form-select" required>
        <option value="">Select Color</option>
        <?php
        $colors = ["Black", "White", "Silver", "Red", "Blue", "Other"];
        foreach ($colors as $color): ?>
            <option value="<?= $color ?>">
                <?= $color ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- VIN -->
<div class="col-md-8">
    <label for="edit_vehicle_VIN" class="form-label">VIN</label>
    <input type="text" class="form-control" id="edit_vehicle_VIN" name="vehicle_VIN" required>
</div>

<!-- License Plate -->
<div class="col-md-6">
    <label for="edit_vehicle_license_tag" class="form-label">License Plate</label>
    <input type="text" class="form-control" id="edit_vehicle_license_tag" name="vehicle_license_tag" required>
</div>

<!-- License State -->
<div class="col-md-6">
    <label for="edit_vehicle_license_state" class="form-label">License State</label>
    <select id="edit_vehicle_license_state" name="vehicle_license_state" class="form-select" required>
        <option value="">Select State</option>
        <?php
        $states = ["AL", "AK", "AZ", "AR", "CA", "CO", "CT", "DE", "DC", "FL", "GA", "HI", "ID", "IL", "IN", "IA", "KS", "KY", "LA", "ME", "MD", "MA", "MI", "MN", "MS", "MO", "MT", "NE", "NV", "NH", "NJ", "NM", "NY", "NC", "ND", "OH", "OK", "OR", "PA", "RI", "SC", "SD", "TN", "TX", "UT", "VT", "VA", "WA", "WV", "WI", "WY"];
        foreach ($states as $state): ?>
            <option value="<?= $state ?>">
                <?= $state ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Purchase Price -->
<div class="col-md-4">
    <label for="edit_vehicle_purchase_price" class="form-label">Purchase Price</label>
    <input type="number" class="form-control" id="edit_vehicle_purchase_price" name="vehicle_purchase_price" required>
</div>

<!-- Purchase Mileage -->
<div class="col-md-4">
    <label for="edit_vehicle_purchase_mileage" class="form-label">Purchase Mileage</label>
    <input type="number" class="form-control" id="edit_vehicle_purchase_mileage" name="vehicle_purchase_mileage"
        required>
</div>

<!-- Current Mileage -->
<div class="col-md-4">
    <label for="edit_vehicle_current_mileage" class="form-label">Current Mileage</label>
    <input type="number" class="form-control" id="edit_vehicle_current_mileage" name="vehicle_current_mileage" required>
</div>

<!-- Active Status -->
<div class="col-12">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="edit_vehicle_is_active" name="is_active" value="1">
        <label class="form-check-label" for="edit_vehicle_is_active">Active</label>
    </div>
</div>

<?php renderModalEnd('editVehicleForm', 'Save Changes'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('editVehicleModal');
        if (!modal) return;
        modal.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            if (!btn) return;
            const fields = ['vehicle_id', 'vehicle_type', 'vehicle_make', 'vehicle_model', 'vehicle_year',
                'vehicle_year_purchased', 'vehicle_color', 'vehicle_VIN', 'vehicle_license_tag',
                'vehicle_license_state', 'vehicle_purchase_price', 'vehicle_purchase_mileage', 'vehicle_current_mileage'];
            fields.forEach(function (f) {
                const el = document.getElementById('edit_' + f);
                const attr = 'data-' + f.toLowerCase().replace(/_/g, '-');
                if (el) el.value = btn.getAttribute(attr) || '';
            });
            // Handle is_active checkbox
            const activeCheckbox = document.getElementById('edit_vehicle_is_active');
            if (activeCheckbox) {
                activeCheckbox.checked = btn.getAttribute('data-is-active') === '1';
            }
        });
    });
</script>