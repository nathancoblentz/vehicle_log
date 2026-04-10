<?php
/**
 * _fuel_form.php - Shared fields for Add/Edit Fuel modals
 * 
 * Expected variables:
 * @var string $mode   'add' or 'edit'
 * @var string $prefix 'add_fuel_' or 'edit_fuel_'
 * @var array  $vehicles Array of vehicle data for the dropdown
 * @var string $mileageMapJSON JSON string for vehicle mileage lookups
 */
?>

<!-- Vehicle Selection -->
<div class="col-12">
    <label for="<?= $prefix ?>vehicle_id" class="form-label">Vehicle</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-car"></i></span>
        <select class="form-select" id="<?= $prefix ?>vehicle_id" name="vehicle_id" required 
                <?= ($mode === 'edit') ? 'disabled' : '' ?>>
            <option value="">Select Vehicle</option>
            <?php foreach ($vehicles as $vehicle): ?>
                <?php 
                    $stickyVehicleId = getStickyVal(['add_fuel', 'update_fuel'], 'vehicle_id');
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
               value="<?= htmlspecialchars(getStickyVal('update_fuel', 'vehicle_id')) ?>">
    <?php endif; ?>
</div>

<!-- Fuel Date -->
<div class="col-md-4">
    <label for="<?= $prefix ?>date" class="form-label">Fuel Date</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-calendar-day"></i></span>
        <input type="date" class="form-control" id="<?= $prefix ?>date" name="fuel_date" 
               value="<?= htmlspecialchars(getStickyVal(['add_fuel', 'update_fuel'], 'fuel_date')) ?>" required>
    </div>
</div>

<!-- Odometer Mileage -->
<div class="col-md-4">
    <label for="<?= $prefix ?>mileage" class="form-label">Odometer Mileage</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-gauge-high"></i></span>
        <input type="number" step="1" class="form-control" id="<?= $prefix ?>mileage" name="fuel_mileage" min="0"
               value="<?= htmlspecialchars(getStickyVal(['add_fuel', 'update_fuel'], 'fuel_mileage')) ?>">
        <div class="invalid-feedback" id="<?= $prefix ?>mileage_error">
            Mileage cannot be less than current odometer reading.
        </div>
    </div>
    <small class="text-muted" id="<?= $prefix ?>mileage_hint"></small>
</div>

<!-- Payment Method -->
<div class="col-md-4">
    <label for="<?= $prefix ?>payment_method" class="form-label">Payment Method</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-credit-card"></i></span>
        <select class="form-select" id="<?= $prefix ?>payment_method" name="fuel_payment_method">
            <?php $stickyPay = getStickyVal(['add_fuel', 'update_fuel'], 'fuel_payment_method'); ?>
            <option value="">Select</option>
            <option value="Cash" <?= $stickyPay === 'Cash' ? 'selected' : '' ?>>Cash</option>
            <option value="Credit" <?= $stickyPay === 'Credit' ? 'selected' : '' ?>>Credit</option>
            <option value="Debit" <?= $stickyPay === 'Debit' ? 'selected' : '' ?>>Debit</option>
            <option value="Fleet" <?= $stickyPay === 'Fleet' ? 'selected' : '' ?>>Fleet Card</option>
        </select>
    </div>
</div>

<!-- Gallons -->
<div class="col-md-2">
    <label for="<?= $prefix ?>gallons" class="form-label">Gallons</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-gas-pump"></i></span>
        <input type="number" step="0.01" class="form-control" id="<?= $prefix ?>gallons" name="fuel_gallons" 
               value="<?= htmlspecialchars(getStickyVal(['add_fuel', 'update_fuel'], 'fuel_gallons')) ?>" required>
    </div>
</div>

<!-- Cost Per Gallon -->
<div class="col-md-2">
    <label for="<?= $prefix ?>cost_per_gallon" class="form-label">Cost/Gal</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-dollar-sign"></i></span>
        <input type="number" step="0.001" class="form-control" id="<?= $prefix ?>cost_per_gallon" name="fuel_cost_per_gallon" 
               value="<?= htmlspecialchars(getStickyVal(['add_fuel', 'update_fuel'], 'fuel_cost_per_gallon')) ?>" required>
    </div>
</div>

<!-- Total Cost (Calculated) -->
<div class="col-md-4">
    <label for="<?= $prefix ?>cost_total" class="form-label">Total Cost</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-money-bill-wave"></i></span>
        <input type="number" step="0.01" class="form-control" id="<?= $prefix ?>cost_total" readonly>
    </div>
</div>

<!-- Fuel Source -->
<div class="col-md-4">
    <label for="<?= $prefix ?>source" class="form-label">Fuel Source</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-building"></i></span>
        <input type="text" class="form-control" id="<?= $prefix ?>source" name="fuel_source" placeholder="Shell, BP, etc"
               value="<?= htmlspecialchars(getStickyVal(['add_fuel', 'update_fuel'], 'fuel_source')) ?>">
    </div>
</div>

<!-- Receipt URL -->
<div class="col-md-6">
    <label for="<?= $prefix ?>receipt_url" class="form-label">Receipt URL</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-link"></i></span>
        <input type="url" class="form-control" id="<?= $prefix ?>receipt_url" name="fuel_receipt_url"
               value="<?= htmlspecialchars(getStickyVal(['add_fuel', 'update_fuel'], 'fuel_receipt_url')) ?>">
    </div>
</div>

<!-- Notes -->
<div class="col-md-6">
    <label for="<?= $prefix ?>notes" class="form-label">Notes</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-regular fa-note-sticky"></i></span>
        <textarea class="form-control" id="<?= $prefix ?>notes" name="fuel_notes" rows="2"><?= htmlspecialchars(getStickyVal(['add_fuel', 'update_fuel'], 'fuel_notes')) ?></textarea>
    </div>
</div>

<script>
// JavaScript logic for mileage validation and total cost calculation
(function() {
    const prefix = '<?= $prefix ?>';
    const mode = '<?= $mode ?>';
    const mileageMap = <?= $mileageMapJSON ?>;

    // Initialize event listeners and logic
    function initFuelLogic() {
        const vehicleSelect = document.getElementById(prefix + 'vehicle_id');
        const mileageInput = document.getElementById(prefix + 'mileage');
        const mileageHint = document.getElementById(prefix + 'mileage_hint');
        const gallonsInput = document.getElementById(prefix + 'gallons');
        const costPerGallonInput = document.getElementById(prefix + 'cost_per_gallon');
        const totalCostInput = document.getElementById(prefix + 'cost_total');

        // Initial Total cost calculation if we already have values (on Edit Modal open)
        if (gallonsInput && costPerGallonInput) {
            const updateCost = () => {
                const gallons = parseFloat(gallonsInput.value) || 0;
                const cost = parseFloat(costPerGallonInput.value) || 0;
                const total = (gallons * cost).toFixed(2);
                totalCostInput.value = (total > 0) ? total : '';
            };
            gallonsInput.addEventListener('input', updateCost);
            costPerGallonInput.addEventListener('input', updateCost);
            
            // For Edit Modal, we might need a way to trigger this when data is populated.
            // Since populate logic is usually in the parent script, we can expose updateCost
            totalCostInput.dataset.update = "updateCost";
        }

        // Helper: set mileage floor for a given vehicle
        function applyMileageFloor(vehicleId, autoFill) {
            if (vehicleId && mileageMap[vehicleId] !== undefined) {
                const currentMileage = parseInt(mileageMap[vehicleId]);
                if (autoFill) {
                    mileageInput.value = currentMileage;
                }
                mileageInput.min = currentMileage;
                if (mileageHint) {
                    mileageHint.innerHTML = '<i class="fa-solid fa-info-circle me-1"></i>Current odometer: <strong>' + currentMileage.toLocaleString() + '</strong> mi';
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
            // On initial load, apply floor if a vehicle is already selected
            if (vehicleSelect.value) {
                applyMileageFloor(vehicleSelect.value, false);
            }
        }
    }

    // Run init or wait for DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFuelLogic);
    } else {
        initFuelLogic();
    }
})();
</script>
