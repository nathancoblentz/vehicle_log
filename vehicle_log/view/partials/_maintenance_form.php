<?php
/**
 * _maintenance_form.php - Shared fields for Add/Edit Maintenance modals
 * 
 * Expected variables:
 * @var string $mode               'add' or 'edit'
 * @var string $prefix             'add_maint_' or 'edit_maint_'
 * @var array  $vehicles           Array of vehicle data
 * @var array  $maintenanceTypes   Array of service data
 * @var array  $vendors            Array of vendor data
 * @var string $mileageMapJSON     JSON for vehicle mileage
 * @var string $costMapJSON        JSON for service cost
 * @var string $descMapJSON        JSON for service description
 */
?>

<!-- Vehicle -->
<div class="col-12">
    <label for="<?= $prefix ?>vehicle_id" class="form-label">Vehicle</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-car"></i></span>
        <select class="form-select" id="<?= $prefix ?>vehicle_id" name="vehicle_id" required 
                <?= ($mode === 'edit') ? 'disabled' : '' ?>>
            <option value="">Select Vehicle</option>
            <?php foreach ($vehicles as $vehicle): ?>
                <?php 
                    $stickyVehicleId = getStickyVal(['add_maintenance', 'update_maintenance'], 'vehicle_id');
                    $selected = ($stickyVehicleId == $vehicle['vehicle_id']) ? 'selected' : '';
                ?>
                <option value="<?= $vehicle['vehicle_id'] ?>" <?= $selected ?>>
                    <?= htmlspecialchars($vehicle['vehicle_make'] . ' ' . $vehicle['vehicle_model'] . ' (' . $vehicle['vehicle_year'] . ')') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php if ($mode === 'edit'): ?>
        <input type="hidden" id="<?= $prefix ?>vehicle_id_hidden" name="vehicle_id"
               value="<?= htmlspecialchars(getStickyVal('update_maintenance', 'vehicle_id')) ?>">
    <?php endif; ?>
</div>

<!-- Service -->
<div class="col-md-6">
    <label for="<?= $prefix ?>type_id" class="form-label">Service</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-wrench"></i></span>
        <select class="form-select" id="<?= $prefix ?>type_id" name="maintenance_type_id" required>
            <option value="">Select Service</option>
            <?php foreach ($maintenanceTypes as $type): ?>
                <?php 
                    $stickyTypeId = getStickyVal(['add_maintenance', 'update_maintenance'], 'maintenance_type_id');
                    $selected = ($stickyTypeId == $type['maintenance_id']) ? 'selected' : '';
                ?>
                <option value="<?= $type['maintenance_id'] ?>" <?= $selected ?>>
                    <?= htmlspecialchars($type['maintenance_type']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Maintenance Date -->
<div class="col-md-6">
    <label for="<?= $prefix ?>date" class="form-label">Maintenance Date</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-calendar-day"></i></span>
        <input type="date" class="form-control" id="<?= $prefix ?>date" name="maintenance_date" 
               value="<?= htmlspecialchars(getStickyVal(['add_maintenance', 'update_maintenance'], 'maintenance_date')) ?>" required>
    </div>
</div>

<!-- Odometer Mileage -->
<div class="col-md-6">
    <label for="<?= $prefix ?>mileage" class="form-label">Odometer Mileage</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-gauge-high"></i></span>
        <input type="number" step="1" class="form-control" id="<?= $prefix ?>mileage" name="maintenance_mileage" min="0" required
               value="<?= htmlspecialchars(getStickyVal(['add_maintenance', 'update_maintenance'], 'maintenance_mileage')) ?>">
    </div>
    <small class="text-muted" id="<?= $prefix ?>mileage_hint"></small>
</div>


<!-- Maintenance Cost -->
<div class="col-md-6">
    <label for="<?= $prefix ?>cost" class="form-label">Maintenance Cost</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-dollar-sign"></i></span>
        <input type="number" step="0.01" class="form-control" id="<?= $prefix ?>cost" name="maintenance_cost" min="0" readonly
               value="<?= htmlspecialchars(getStickyVal(['add_maintenance', 'update_maintenance'], 'maintenance_cost')) ?>">
    </div>
</div>

<!-- Vendor -->
<div class="col-md-6">
    <label for="<?= $prefix ?>vendor_id" class="form-label">Vendor</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-store"></i></span>
        <select class="form-select" id="<?= $prefix ?>vendor_id" name="vendor_id">
            <?php $stickyVendor = getStickyVal(['add_maintenance', 'update_maintenance'], 'vendor_id'); ?>
            <option value="">Select Vendor</option>
            <?php foreach ($vendors as $vendor): ?>
                <option value="<?= $vendor['vendor_id'] ?>" <?= $stickyVendor == $vendor['vendor_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($vendor['vendor_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Maintenance Status -->
<div class="col-md-6">
    <label for="<?= $prefix ?>status" class="form-label">Maintenance Status</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-clipboard-check"></i></span>
        <select class="form-select" id="<?= $prefix ?>status" name="maintenance_status">
            <?php $stickyStatus = getStickyVal(['add_maintenance', 'update_maintenance'], 'maintenance_status'); ?>
            <option value="">Select Status</option>
            <option value="Scheduled" <?= $stickyStatus === 'Scheduled' ? 'selected' : '' ?>>Scheduled</option>
            <option value="In Progress" <?= $stickyStatus === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
            <option value="Completed" <?= $stickyStatus === 'Completed' ? 'selected' : '' ?>>Completed</option>
        </select>
    </div>
</div>

<!-- Maintenance Description -->
<div class="col-12">
    <label for="<?= $prefix ?>description" class="form-label">Maintenance Description</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-regular fa-note-sticky"></i></span>
        <textarea class="form-control" id="<?= $prefix ?>description" name="maintenance_description" rows="3"><?= htmlspecialchars(getStickyVal(['add_maintenance', 'update_maintenance'], 'maintenance_description')) ?></textarea>
    </div>
</div>

<script>
(function() {
    const prefix = '<?= $prefix ?>';
    const mode = '<?= $mode ?>';
    const mileageMap = <?= $mileageMapJSON ?>;
    const costMap = <?= $costMapJSON ?>;
    const descMap = <?= $descMapJSON ?>;

    // Initialize event listeners and logic
    function initMaintLogic() {
        const vehicleSelect = document.getElementById(prefix + 'vehicle_id');
        const typeSelect = document.getElementById(prefix + 'type_id');
        const mileageInput = document.getElementById(prefix + 'mileage');
        const costInput = document.getElementById(prefix + 'cost');
        const descInput = document.getElementById(prefix + 'description');

        const mileageHint = document.getElementById(prefix + 'mileage_hint');

        // Helper: set mileage floor for a given vehicle
        function applyMileageFloor(vehicleId, autoFill) {
            if (vehicleId && mileageMap[vehicleId] !== undefined) {
                const currentMileage = parseInt(mileageMap[vehicleId]);
                if (autoFill) {
                    mileageInput.value = currentMileage;
                }
                mileageInput.min = currentMileage;
                if (mileageHint) {
                    mileageHint.innerHTML = '<i class="fa-solid fa-info-circle me-1"></i>Current odometer: <strong>' + currentMileage.toLocaleString() + '</strong> mi — cannot go below this value.';
                }
            } else {
                mileageInput.min = 0;
                if (mileageHint) mileageHint.textContent = '';
            }
        }

        if (vehicleSelect && mileageInput) {
            vehicleSelect.addEventListener('change', function() {
                applyMileageFloor(this.value, mode === 'add');
            });
            // On initial load, apply floor if a vehicle is already selected (e.g. edit mode or sticky)
            if (vehicleSelect.value) {
                applyMileageFloor(vehicleSelect.value, false);
            }
        }
        // When service changes, update cost and description if they are empty
        if (typeSelect && costInput) {
            typeSelect.addEventListener('change', function() {
                const selectedId = this.value;
                if (selectedId && costMap[selectedId] !== undefined && costMap[selectedId] !== null) {
                    costInput.value = parseFloat(costMap[selectedId]).toFixed(2);
                } else {
                    costInput.value = '';
                }

                if (descInput && !descInput.value.trim()) {
                    if (selectedId && descMap[selectedId] !== undefined) {
                        descInput.value = descMap[selectedId];
                    }
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMaintLogic);
    } else {
        initMaintLogic();
    }
})();
</script>
