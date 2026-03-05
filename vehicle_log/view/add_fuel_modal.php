<?php
// add_fuel_modal.php — Add Fuel form fields only
// Boilerplate handled by renderModalStart() / renderModalEnd()

global $db;
$vehicleStmt = $db->query("SELECT vehicle_id, vehicle_make, vehicle_model, vehicle_year, vehicle_current_mileage FROM vehicles ORDER BY vehicle_make, vehicle_model");
$vehicles = $vehicleStmt->fetchAll(PDO::FETCH_ASSOC);
$vehicleStmt->closeCursor();

// Create JSON array mapping vehicle_id to vehicle_current_mileage for JS lookup
$vehicleMileageMap = [];
foreach ($vehicles as $v) {
    if (!empty($v['vehicle_current_mileage'])) {
        $vehicleMileageMap[$v['vehicle_id']] = $v['vehicle_current_mileage'];
    }
}
$mileageMapJSON = json_encode($vehicleMileageMap);

renderModalStart('addFuelModal', 'Add Fuel Record', 'addFuelForm', 'add_fuel');
?>

<!-- Vehicle (full width) -->
<div class="col-12">
    <label for="add_fuel_vehicle_id" class="form-label">Vehicle</label>
    <select class="form-select" id="add_fuel_vehicle_id" name="vehicle_id" required>
        <option value="">Select Vehicle</option>
        <?php foreach ($vehicles as $vehicle): ?>
            <option value="<?= $vehicle['vehicle_id'] ?>">
                <?= htmlspecialchars($vehicle['vehicle_make'] . ' ' . $vehicle['vehicle_model'] . ' (' . $vehicle['vehicle_year'] . ')') ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Fuel Date -->
<div class="col-md-4">
    <label for="fuel_date" class="form-label">Fuel Date</label>
    <input type="date" class="form-control" id="fuel_date" name="fuel_date" required>
</div>

<!-- Odometer Mileage -->
<div class="col-md-4">
    <label for="fuel_mileage" class="form-label">Odometer Mileage</label>
    <input type="number" class="form-control" id="fuel_mileage" name="fuel_mileage" min="0">
    <div class="invalid-feedback" id="mileageError">Mileage cannot be less than current odometer
        reading.</div>
    <small class="text-muted" id="currentMileageHint"></small>
</div>

<!-- Payment Method -->
<div class="col-md-4">
    <label for="fuel_payment_method" class="form-label">Payment Method</label>
    <select class="form-select" id="fuel_payment_method" name="fuel_payment_method">
        <option value="">Select</option>
        <option value="Cash">Cash</option>
        <option value="Credit">Credit</option>
        <option value="Debit">Debit</option>
        <option value="Fleet">Fleet Card</option>
    </select>
</div>

<!-- Gallons -->
<div class="col-md-3">
    <label for="fuel_gallons" class="form-label">Gallons</label>
    <input type="number" step="0.01" class="form-control" id="fuel_gallons" name="fuel_gallons" required>
</div>

<!-- Cost Per Gallon -->
<div class="col-md-3">
    <label for="fuel_cost_per_gallon" class="form-label">Cost/Gallon</label>
    <input type="number" step="0.001" class="form-control" id="fuel_cost_per_gallon" name="fuel_cost_per_gallon"
        required>
</div>

<!-- Total Cost -->
<div class="col-md-2">
    <label for="fuel_cost_total" class="form-label">Total Cost</label>
    <input type="number" step="0.01" class="form-control" id="fuel_cost_total" readonly>
</div>

<!-- Fuel Source -->
<div class="col-md-4">
    <label for="fuel_source" class="form-label">Fuel Source</label>
    <input type="text" class="form-control" id="fuel_source" name="fuel_source" placeholder="Shell, BP, etc">
</div>

<!-- Receipt URL -->
<div class="col-md-6">
    <label for="fuel_receipt_url" class="form-label">Receipt URL</label>
    <input type="url" class="form-control" id="fuel_receipt_url" name="fuel_receipt_url">
</div>

<!-- Notes -->
<div class="col-md-6">
    <label for="fuel_notes" class="form-label">Notes</label>
    <textarea class="form-control" id="fuel_notes" name="fuel_notes" rows="2"></textarea>
</div>

<?php renderModalEnd('addFuelForm', 'Add Fuel'); ?>

<script>

    // Auto-fill mileage and validate against current mileage
    document.addEventListener('DOMContentLoaded', function () {
        const vehicleSelect = document.getElementById('add_fuel_vehicle_id');
        const mileageInput = document.getElementById('fuel_mileage');
        const mileageHint = document.getElementById('currentMileageHint');
        const mileageError = document.getElementById('mileageError');
        const fuelForm = document.getElementById('addFuelForm');
        const mileageMap = <?= $mileageMapJSON ?>;

        // Auto-fill mileage when vehicle is selected
        if (vehicleSelect && mileageInput) {
            vehicleSelect.addEventListener('change', function () {
                const selectedId = this.value;
                const currentMileage = mileageMap[selectedId];

                if (currentMileage && currentMileage !== 0) {
                    mileageInput.value = currentMileage;
                    mileageInput.min = currentMileage;
                    mileageHint.textContent = 'Current mileage: ' + parseInt(currentMileage).toLocaleString() + ' mi';
                } else {
                    mileageInput.value = '';
                    mileageInput.min = 0;
                    mileageHint.textContent = '';
                }
                mileageInput.classList.remove('is-invalid');
            });
        }

        // Validate mileage on input
        mileageInput.addEventListener('input', function () {
            const minMileage = parseInt(mileageInput.min) || 0;
            const enteredMileage = parseInt(this.value) || 0;

            if (enteredMileage > 0 && enteredMileage < minMileage) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });

        // Prevent form submission if mileage is invalid
        fuelForm.addEventListener('submit', function (e) {
            const minMileage = parseInt(mileageInput.min) || 0;
            const enteredMileage = parseInt(mileageInput.value) || 0;

            if (enteredMileage > 0 && enteredMileage < minMileage) {
                e.preventDefault();
                mileageInput.classList.add('is-invalid');
                mileageInput.focus();
            }
        });

        // Automatically calculate Total Cost on input of Gallons or Cost Per Gallon
        const gallonsInput = document.getElementById('fuel_gallons');
        const costPerGallonInput = document.getElementById('fuel_cost_per_gallon');
        const totalCostInput = document.getElementById('fuel_cost_total');

        function updateTotalCost() {
            const gallons = parseFloat(gallonsInput.value) || 0;
            const costPerGallon = parseFloat(costPerGallonInput.value) || 0;
            const total = (gallons * costPerGallon).toFixed(2);
            if (total > 0) {
                totalCostInput.value = total;
            } else {
                totalCostInput.value = '';
            }
        }

        if (gallonsInput && costPerGallonInput && totalCostInput) {
            gallonsInput.addEventListener('input', updateTotalCost);
            costPerGallonInput.addEventListener('input', updateTotalCost);
        }
    });
</script>