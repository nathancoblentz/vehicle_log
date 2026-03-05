<?php
// edit_maintenance_modal.php — Edit Maintenance form
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

$typeStmt = $db->query("SELECT maintenance_id, maintenance_type, recommended_cost, maintenance_description FROM maintenance_type ORDER BY maintenance_type");
$maintenanceTypes = $typeStmt->fetchAll(PDO::FETCH_ASSOC);
$typeStmt->closeCursor();

// Create JSON array mapping maintenance_id to recommended_cost for JS lookup
$maintenanceCostMap = [];
$maintenanceDescMap = [];
foreach ($maintenanceTypes as $t) {
    if (isset($t['recommended_cost'])) {
        $maintenanceCostMap[$t['maintenance_id']] = $t['recommended_cost'];
    }
    if (!empty($t['maintenance_description'])) {
        $maintenanceDescMap[$t['maintenance_id']] = $t['maintenance_description'];
    }
}
$costMapJSON = json_encode($maintenanceCostMap);
$descMapJSON = json_encode($maintenanceDescMap);

renderModalStart('editMaintenanceModal', 'Edit Maintenance Record', 'editMaintenanceForm', 'update_maintenance');
?>

<input type="hidden" name="maintenance_id" id="edit_maintenance_id">

<!-- Vehicle -->
<div class="col-12">
    <label for="edit_maint_vehicle_id" class="form-label">Vehicle</label>
    <select class="form-select" id="edit_maint_vehicle_id" name="vehicle_id" required>
        <option value="">Select Vehicle</option>
        <?php foreach ($vehicles as $vehicle): ?>
            <option value="<?= $vehicle['vehicle_id'] ?>">
                <?= htmlspecialchars($vehicle['vehicle_make'] . ' ' . $vehicle['vehicle_model'] . ' (' . $vehicle['vehicle_year'] . ')') ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Maintenance Type -->
<div class="col-md-6">
    <label for="edit_maintenance_type_id" class="form-label">Maintenance Type</label>
    <select class="form-select" id="edit_maintenance_type_id" name="maintenance_type_id" required>
        <option value="">Select Maintenance Type</option>
        <?php foreach ($maintenanceTypes as $type): ?>
            <option value="<?= $type['maintenance_id'] ?>">
                <?= htmlspecialchars($type['maintenance_type']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Maintenance Date -->
<div class="col-md-6">
    <label for="edit_maintenance_date" class="form-label">Maintenance Date</label>
    <input type="date" class="form-control" id="edit_maintenance_date" name="maintenance_date" required>
</div>

<!-- Odometer Mileage -->
<div class="col-md-6">
    <label for="edit_maintenance_mileage" class="form-label">Odometer Mileage</label>
    <input type="number" class="form-control" id="edit_maintenance_mileage" name="maintenance_mileage" min="0">
</div>

<!-- Maintenance Cost -->
<div class="col-md-6">
    <label for="edit_maintenance_cost" class="form-label">Maintenance Cost</label>
    <input type="number" step="0.01" class="form-control" id="edit_maintenance_cost" name="maintenance_cost" min="0"
        readonly>
</div>

<?php
$vendorStmt = $db->query("SELECT vendor_id, vendor_name FROM vendors WHERE is_active = 1 ORDER BY vendor_name");
$vendors = $vendorStmt->fetchAll(PDO::FETCH_ASSOC);
$vendorStmt->closeCursor();
?>

<!-- Vendor -->
<div class="col-md-6">
    <label for="edit_vendor_id" class="form-label">Vendor</label>
    <select class="form-select" id="edit_vendor_id" name="vendor_id">
        <option value="">Select Vendor</option>
        <?php foreach ($vendors as $vendor): ?>
            <option value="<?= $vendor['vendor_id'] ?>">
                <?= htmlspecialchars($vendor['vendor_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Status -->
<div class="col-md-6">
    <label for="edit_maintenance_status" class="form-label">Maintenance Status</label>
    <select class="form-select" id="edit_maintenance_status" name="maintenance_status">
        <option value="">Select Status</option>
        <option value="Scheduled">Scheduled</option>
        <option value="In Progress">In Progress</option>
        <option value="Completed">Completed</option>
    </select>
</div>

<!-- Description -->
<div class="col-12">
    <label for="edit_maintenance_description" class="form-label">Maintenance Description</label>
    <textarea class="form-control" id="edit_maintenance_description" name="maintenance_description" rows="3"></textarea>
</div>


<?php renderModalEnd('editMaintenanceForm', 'Save Changes'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const vehicleSelect = document.getElementById('edit_maint_vehicle_id');
        const mileageInput = document.getElementById('edit_maintenance_mileage');
        const mileageMap = <?= $mileageMapJSON ?>;

        const typeSelect = document.getElementById('edit_maintenance_type_id');
        const costInput = document.getElementById('edit_maintenance_cost');
        const descInput = document.getElementById('edit_maintenance_description');
        const costMap = <?= $costMapJSON ?>;
        const descMap = <?= $descMapJSON ?>;

        if (vehicleSelect && mileageInput) {
            vehicleSelect.addEventListener('change', function () {
                const selectedId = this.value;
                // Only override if replacing with a known value from the dictionary
                if (selectedId && mileageMap[selectedId] !== undefined) {
                    mileageInput.value = mileageMap[selectedId];
                }
            });
        }

        if (typeSelect && costInput) {
            typeSelect.addEventListener('change', function () {
                const selectedId = this.value;
                // Only override if replacing with a known value from the dictionary
                if (selectedId && costMap[selectedId] !== undefined && costMap[selectedId] !== null) {
                    costInput.value = parseFloat(costMap[selectedId]).toFixed(2);
                }

                if (descInput) {
                    if (selectedId && descMap[selectedId] !== undefined) {
                        // Populate if empty (leave writeable)
                        if (!descInput.value.trim()) {
                            descInput.value = descMap[selectedId];
                        }
                    }
                }
            });
        }

        const modal = document.getElementById('editMaintenanceModal');
        if (!modal) return;
        modal.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            if (!btn) return;
            const fields = ['maintenance_id', 'vehicle_id', 'maintenance_type_id', 'maintenance_date',
                'maintenance_mileage', 'maintenance_cost', 'vendor_id',
                'maintenance_description', 'maintenance_status'];
            fields.forEach(function (f) {
                let elId = 'edit_' + f;
                if (f === 'vehicle_id') elId = 'edit_maint_vehicle_id';
                const el = document.getElementById(elId);
                if (el) {
                    if (f === 'maintenance_mileage') {
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
        });
    });
</script>