<?php // ADD MAINTENANCE TYPE FORM 

global $db;
$maintenanceTypeStmt = $db->query("

SELECT maintenance_id, maintenance_type, maintenance_description, recommended_interval_miles, recommended_interval_days FROM maintenance_type ORDER BY maintenance_type");
$maintenanceTypes = $maintenanceTypeStmt->fetchAll(PDO::FETCH_ASSOC);
$maintenanceTypeStmt->closeCursor();

renderModalStart('addMaintenanceTypeModal', 'Add Maintenance Type', 'addMaintenanceTypeForm', 'add_maintenance_type');

?>

<!-- Maintenance Code-->
<div class="col-md-6">
    <label for="amt_maintenance_code" class="form-label">Maintenance Code</label>
    <input type="text" class="form-control" id="amt_maintenance_code" name="maintenance_code">
</div>

<!-- Maintenance Type -->
<div class="col-md-6">
    <label for="amt_maintenance_type" class="form-label">Maintenance Type</label>
    <input type="text" class="form-control" id="amt_maintenance_type" name="maintenance_type" required>
</div>

<!-- Maintenance Description -->
<div class="col-12">
    <label for="amt_maintenance_description" class="form-label">Maintenance Description</label>
    <textarea class="form-control" id="amt_maintenance_description" name="maintenance_description" rows="3"></textarea>
</div>

<!-- Recommended Interval Miles -->
<div class="col-md-4">
    <label for="amt_recommended_interval_miles" class="form-label">Recommended Interval Miles</label>
    <input type="number" class="form-control" id="amt_recommended_interval_miles" name="recommended_interval_miles"
        min="0">
</div>

<!-- Recommended Interval Days -->
<div class="col-md-4">
    <label for="amt_recommended_interval_days" class="form-label">Recommended Interval Days</label>
    <input type="number" class="form-control" id="amt_recommended_interval_days" name="recommended_interval_days"
        min="0">
</div>

<!-- Recommended Cost -->
<div class="col-md-4">
    <label for="amt_recommended_cost" class="form-label">Recommended Cost</label>
    <div class="input-group">
        <span class="input-group-text">$</span>
        <input type="number" step="0.01" class="form-control" id="amt_recommended_cost" name="recommended_cost" min="0">
    </div>
</div>

<?php renderModalEnd('addMaintenanceTypeForm', 'Add Maintenance Type'); ?>