<?php
// edit_fuel_modal.php — Edit Fuel form
// Uses renderModalStart() / renderModalEnd() for boilerplate

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

renderModalStart('editFuelModal', 'Edit Fuel Record', 'editFuelForm', 'update_fuel');
?>

<input type="hidden" name="fuel_id" id="edit_fuel_id">

<!-- Vehicle (read-only) -->
<div class="col-12">
    <label for="edit_fuel_vehicle_id" class="form-label">Vehicle</label>
    <select class="form-select" id="edit_fuel_vehicle_id" disabled>
        <option value="">Select Vehicle</option>
        <?php foreach ($vehicles as $vehicle): ?>
            <option value="<?= $vehicle['vehicle_id'] ?>">
                <?= htmlspecialchars($vehicle['vehicle_make'] . ' ' . $vehicle['vehicle_model'] . ' (' . $vehicle['vehicle_year'] . ')') ?>
            </option>
        <?php endforeach; ?>
    </select>
    <input type="hidden" id="edit_fuel_vehicle_id_hidden" name="vehicle_id">
</div>

<!-- Fuel Date -->
<div class="col-md-4">
    <label for="edit_fuel_date" class="form-label">Fuel Date</label>
    <input type="date" class="form-control" id="edit_fuel_date" name="fuel_date" required>
</div>

<!-- Odometer Mileage -->
<div class="col-md-4">
    <label for="edit_fuel_mileage" class="form-label">Odometer Mileage</label>
    <input type="number" class="form-control" id="edit_fuel_mileage" name="fuel_mileage" min="0">
</div>

<!-- Payment Method -->
<div class="col-md-4">
    <label for="edit_fuel_payment_method" class="form-label">Payment Method</label>
    <select class="form-select" id="edit_fuel_payment_method" name="fuel_payment_method">
        <option value="">Select</option>
        <option value="Cash">Cash</option>
        <option value="Credit">Credit</option>
        <option value="Debit">Debit</option>
        <option value="Fleet">Fleet Card</option>
    </select>
</div>

<!-- Gallons -->
<div class="col-md-3">
    <label for="edit_fuel_gallons" class="form-label">Gallons</label>
    <input type="number" step="0.01" class="form-control" id="edit_fuel_gallons" name="fuel_gallons" required>
</div>

<!-- Cost Per Gallon -->
<div class="col-md-3">
    <label for="edit_fuel_cost_per_gallon" class="form-label">Cost/Gallon</label>
    <input type="number" step="0.001" class="form-control" id="edit_fuel_cost_per_gallon" name="fuel_cost_per_gallon"
        required>
</div>

<!-- Total Cost -->
<div class="col-md-2">
    <label for="edit_fuel_cost_total" class="form-label">Total Cost</label>
    <input type="number" step="0.01" class="form-control" id="edit_fuel_cost_total" readonly>
</div>

<!-- Fuel Source -->
<div class="col-md-4">
    <label for="edit_fuel_source" class="form-label">Fuel Source</label>
    <input type="text" class="form-control" id="edit_fuel_source" name="fuel_source">
</div>

<!-- Receipt URL -->
<div class="col-md-6">
    <label for="edit_fuel_receipt_url" class="form-label">Receipt URL</label>
    <input type="url" class="form-control" id="edit_fuel_receipt_url" name="fuel_receipt_url">
</div>

<!-- Notes -->
<div class="col-md-6">
    <label for="edit_fuel_notes" class="form-label">Notes</label>
    <textarea class="form-control" id="edit_fuel_notes" name="fuel_notes" rows="2"></textarea>
</div>

<?php renderModalEnd('editFuelForm', 'Save Changes'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const vehicleSelect = document.getElementById('edit_fuel_vehicle_id');
        const mileageInput = document.getElementById('edit_fuel_mileage');
        const mileageMap = <?= $mileageMapJSON ?>;

        if (vehicleSelect && mileageInput) {
            vehicleSelect.addEventListener('change', function () {
                const selectedId = this.value;
                if (selectedId && mileageMap[selectedId] !== undefined) {
                    mileageInput.value = mileageMap[selectedId];
                }
            });
        }

        const modal = document.getElementById('editFuelModal');
        if (!modal) return;
        modal.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            if (!btn) return;
            const fields = ['fuel_id', 'vehicle_id', 'fuel_date', 'fuel_mileage', 'fuel_payment_method',
                'fuel_gallons', 'fuel_cost_per_gallon', 'fuel_source', 'fuel_receipt_url', 'fuel_notes'];
            fields.forEach(function (f) {
                const el = document.getElementById('edit_' + (f === 'vehicle_id' ? 'fuel_vehicle_id' : f));
                if (el) {
                    if (f === 'fuel_mileage') {
                        const vId = btn.getAttribute('data-vehicle-id');
                        if (vId && mileageMap[vId] !== undefined) {
                            el.value = mileageMap[vId];
                        } else {
                            el.value = btn.getAttribute('data-' + f.replace(/_/g, '-')) || '';
                        }
                    } else if (el.tagName === 'TEXTAREA') {
                        el.textContent = btn.getAttribute('data-' + f.replace(/_/g, '-')) || '';
                    } else {
                        el.value = btn.getAttribute('data-' + f.replace(/_/g, '-')) || '';
                    }
                }
            });
            // Also set the hidden input so vehicle_id is submitted
            const hiddenVehicle = document.getElementById('edit_fuel_vehicle_id_hidden');
            if (hiddenVehicle) hiddenVehicle.value = btn.getAttribute('data-vehicle-id') || '';

            // Calculate initial cost
            updateEditTotalCost();
        });

        // Automatically calculate Total Cost on input of Gallons or Cost Per Gallon
        const editGallonsInput = document.getElementById('edit_fuel_gallons');
        const editCostPerGallonInput = document.getElementById('edit_fuel_cost_per_gallon');
        const editTotalCostInput = document.getElementById('edit_fuel_cost_total');

        function updateEditTotalCost() {
            const gallons = parseFloat(editGallonsInput.value) || 0;
            const costPerGallon = parseFloat(editCostPerGallonInput.value) || 0;
            const total = (gallons * costPerGallon).toFixed(2);
            if (total > 0) {
                editTotalCostInput.value = total;
            } else {
                editTotalCostInput.value = '';
            }
        }

        if (editGallonsInput && editCostPerGallonInput && editTotalCostInput) {
            editGallonsInput.addEventListener('input', updateEditTotalCost);
            editCostPerGallonInput.addEventListener('input', updateEditTotalCost);
        }
    });
</script>