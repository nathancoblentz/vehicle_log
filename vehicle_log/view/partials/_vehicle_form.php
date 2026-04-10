<?php
/**
 * _vehicle_form.php - Shared fields for Add/Edit Vehicle modals
 * 
 * Expected variables:
 * @var string $mode   'add' or 'edit'
 * @var string $prefix 'add_vehicle_' or 'edit_vehicle_'
 */
?>

<!-- Vehicle Type -->
<div class="col-md-6">
    <label for="<?= $prefix ?>type" class="form-label">Vehicle Type</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-car"></i></span>
        <input type="text" class="form-control" id="<?= $prefix ?>type" name="vehicle_type" 
               value="<?= htmlspecialchars(getStickyVal(['add_vehicle', 'update_vehicle'], 'vehicle_type')) ?>" required>
    </div>
</div>

<!-- Vehicle Make -->
<div class="col-md-6">
    <label for="<?= $prefix ?>make" class="form-label">Vehicle Make</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-industry"></i></span>
        <input type="text" class="form-control" id="<?= $prefix ?>make" name="vehicle_make" 
               value="<?= htmlspecialchars(getStickyVal(['add_vehicle', 'update_vehicle'], 'vehicle_make')) ?>" required>
    </div>
</div>

<!-- Vehicle Model -->
<div class="col-md-6">
    <label for="<?= $prefix ?>model" class="form-label">Vehicle Model</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-car-side"></i></span>
        <input type="text" class="form-control" id="<?= $prefix ?>model" name="vehicle_model" 
               value="<?= htmlspecialchars(getStickyVal(['add_vehicle', 'update_vehicle'], 'vehicle_model')) ?>" required>
    </div>
</div>

<!-- Vehicle Year -->
<div class="col-md-3">
    <label for="<?= $prefix ?>year" class="form-label">Vehicle Year</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-regular fa-calendar-days"></i></span>
        <select id="<?= $prefix ?>year" name="vehicle_year" class="form-select" required>
            <?php $stickyYear = getStickyVal(['add_vehicle', 'update_vehicle'], 'vehicle_year'); ?>
            <option value="">Select Year</option>
            <?php for ($year = date('Y'); $year >= 1980; $year--): ?>
                <option value="<?= $year ?>" <?= $stickyYear == $year ? 'selected' : '' ?>><?= $year ?></option>
            <?php endfor; ?>
        </select>
    </div>
</div>

<!-- Year Purchased -->
<div class="col-md-3">
    <label for="<?= $prefix ?>year_purchased" class="form-label">Year Purchased</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-calendar-check"></i></span>
        <select id="<?= $prefix ?>year_purchased" name="vehicle_year_purchased" class="form-select" required>
            <?php $stickyPurchased = getStickyVal(['add_vehicle', 'update_vehicle'], 'vehicle_year_purchased'); ?>
            <option value="">Select Year</option>
            <?php for ($year = date('Y'); $year >= 1980; $year--): ?>
                <option value="<?= $year ?>" <?= $stickyPurchased == $year ? 'selected' : '' ?>><?= $year ?></option>
            <?php endfor; ?>
        </select>
    </div>
</div>

<!-- Vehicle Color -->
<div class="col-md-4">
    <label for="<?= $prefix ?>color" class="form-label">Color</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-palette"></i></span>
        <select id="<?= $prefix ?>color" name="vehicle_color" class="form-select" required>
            <?php $stickyColor = getStickyVal(['add_vehicle', 'update_vehicle'], 'vehicle_color'); ?>
            <option value="">Select Color</option>
            <?php
            $colors = ["Black", "White", "Silver", "Red", "Blue", "Green", "Purple", "Other"];
            foreach ($colors as $color): ?>
                <option value="<?= $color ?>" <?= $stickyColor === $color ? 'selected' : '' ?>><?= $color ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- VIN -->
<div class="col-md-8">
    <label for="<?= $prefix ?>VIN" class="form-label">VIN</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-barcode"></i></span>
        <input type="text" class="form-control" id="<?= $prefix ?>VIN" name="vehicle_VIN" required
               placeholder="e.g., 1HGCM82633A123456" 
               value="<?= htmlspecialchars(getStickyVal(['add_vehicle', 'update_vehicle'], 'vehicle_VIN')) ?>">
    </div>
</div>

<!-- License Plate -->
<div class="col-md-6">
    <label for="<?= $prefix ?>license_tag" class="form-label">License Plate</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-regular fa-id-card"></i></span>
        <input type="text" class="form-control" id="<?= $prefix ?>license_tag" name="vehicle_license_tag" 
               value="<?= htmlspecialchars(getStickyVal(['add_vehicle', 'update_vehicle'], 'vehicle_license_tag')) ?>" required>
    </div>
</div>

<!-- License State -->
<div class="col-md-6">
    <label for="<?= $prefix ?>license_state" class="form-label">License State</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-map-location-dot"></i></span>
        <select id="<?= $prefix ?>license_state" name="vehicle_license_state" class="form-select" required>
            <?php $stickyState = getStickyVal(['add_vehicle', 'update_vehicle'], 'vehicle_license_state'); ?>
            <option value="">Select State</option>
            <?php
            $states = ["AL", "AK", "AZ", "AR", "CA", "CO", "CT", "DE", "DC", "FL", "GA", "HI", "ID", "IL", "IN", "IA", "KS", "KY", "LA", "ME", "MD", "MA", "MI", "MN", "MS", "MO", "MT", "NE", "NV", "NH", "NJ", "NM", "NY", "NC", "ND", "OH", "OK", "OR", "PA", "RI", "SC", "SD", "TN", "TX", "UT", "VT", "VA", "WA", "WV", "WI", "WY"];
            foreach ($states as $state): ?>
                <option value="<?= $state ?>" <?= $stickyState === $state ? 'selected' : '' ?>><?= $state ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Purchase Price -->
<div class="col-md-4">
    <label for="<?= $prefix ?>purchase_price" class="form-label">Purchase Price</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-dollar-sign"></i></span>
        <input type="number" class="form-control" id="<?= $prefix ?>purchase_price" name="vehicle_purchase_price" 
               value="<?= htmlspecialchars(getStickyVal(['add_vehicle', 'update_vehicle'], 'vehicle_purchase_price')) ?>" required>
    </div>
</div>

<!-- Purchase Mileage -->
<div class="col-md-4">
    <label for="<?= $prefix ?>purchase_mileage" class="form-label">Purchase Mileage</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-gauge"></i></span>
        <input type="number" step="1" class="form-control" id="<?= $prefix ?>purchase_mileage" name="vehicle_purchase_mileage" 
               value="<?= htmlspecialchars(getStickyVal(['add_vehicle', 'update_vehicle'], 'vehicle_purchase_mileage')) ?>" required>
    </div>
</div>

<!-- Current Mileage -->
<div class="col-md-4">
    <label for="<?= $prefix ?>current_mileage" class="form-label">Current Mileage</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-gauge-high"></i></span>
        <input type="number" step="1" class="form-control" id="<?= $prefix ?>current_mileage" name="vehicle_current_mileage" 
               value="<?= htmlspecialchars(getStickyVal(['add_vehicle', 'update_vehicle'], 'vehicle_current_mileage')) ?>" required>
    </div>
</div>

<?php if ($mode === 'edit'): ?>
<!-- Active Status -->
<div class="col-12 mt-2">
    <div class="form-check form-switch">
        <input type="hidden" name="is_active" value="0">
        <input class="form-check-input" type="checkbox" id="<?= $prefix ?>is_active" name="is_active" value="1"
               <?= getStickyVal('update_vehicle', 'is_active') === '1' ? 'checked' : '' ?>>
        <label class="form-check-label" for="<?= $prefix ?>is_active">Active</label>
    </div>
</div>
<?php endif; ?>
