<?php
/**
 * _service_form.php - Shared fields for Add/Edit Service modals
 * 
 * Expected variables:
 * @var string $mode   'add' or 'edit'
 * @var string $prefix 'add_mtype_' or 'edit_mtype_'
 */
?>

<!-- Maintenance Code -->
<div class="col-md-6">
    <label for="<?= $prefix ?>code" class="form-label">Maintenance Code</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-barcode"></i></span>
        <input type="text" class="form-control" id="<?= $prefix ?>code" name="maintenance_code"
               value="<?= htmlspecialchars(getStickyVal(['add_service', 'update_service'], 'maintenance_code')) ?>">
    </div>
</div>

<!-- Service -->
<div class="col-md-6">
    <label for="<?= $prefix ?>type" class="form-label">Service</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-wrench"></i></span>
        <input type="text" class="form-control" id="<?= $prefix ?>type" name="maintenance_type" 
               value="<?= htmlspecialchars(getStickyVal(['add_service', 'update_service'], 'maintenance_type')) ?>" required>
    </div>
</div>

<!-- Maintenance Description -->
<div class="col-12">
    <label for="<?= $prefix ?>description" class="form-label">Maintenance Description</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-regular fa-note-sticky"></i></span>
        <textarea class="form-control" id="<?= $prefix ?>description" name="maintenance_description" rows="3"><?= htmlspecialchars(getStickyVal(['add_service', 'update_service'], 'maintenance_description')) ?></textarea>
    </div>
</div>

<!-- Recommended Interval Miles -->
<div class="col-md-4">
    <label for="<?= $prefix ?>interval_miles" class="form-label">Interval Miles</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-gauge-high"></i></span>
        <input type="number" step="1" class="form-control" id="<?= $prefix ?>interval_miles" name="recommended_interval_miles" min="0"
               value="<?= htmlspecialchars(getStickyVal(['add_service', 'update_service'], 'recommended_interval_miles')) ?>">
    </div>
</div>

<!-- Recommended Interval Days -->
<div class="col-md-4">
    <label for="<?= $prefix ?>interval_days" class="form-label">Interval Days</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
        <input type="number" step="1" class="form-control" id="<?= $prefix ?>interval_days" name="recommended_interval_days" min="0"
               value="<?= htmlspecialchars(getStickyVal(['add_service', 'update_service'], 'recommended_interval_days')) ?>">
    </div>
</div>

<!-- Recommended Cost -->
<div class="col-md-4">
    <label for="<?= $prefix ?>cost" class="form-label">Recommended Cost</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-dollar-sign"></i></span>
        <input type="number" step="0.01" class="form-control" id="<?= $prefix ?>cost" name="recommended_cost" min="0"
               value="<?= htmlspecialchars(getStickyVal(['add_service', 'update_service'], 'recommended_cost')) ?>">
    </div>
</div>

<?php if ($mode === 'edit'): ?>
<!-- Active Status -->
<div class="col-12 mt-2">
    <div class="form-check form-switch">
        <input type="hidden" name="is_active" value="0">
        <input class="form-check-input" type="checkbox" id="<?= $prefix ?>is_active" name="is_active" value="1"
               <?= getStickyVal('update_service', 'is_active') === '1' ? 'checked' : '' ?>>
        <label class="form-check-label" for="<?= $prefix ?>is_active">Active</label>
    </div>
</div>
<?php endif; ?>
