<?php
// add_vehicle_modal.php — Add Vehicle form fields only
// Boilerplate handled by renderModalStart() / renderModalEnd()

renderModalStart('addVehicleModal', 'Add Vehicle', 'addVehicleForm', 'add_vehicle');
?>

<!-- Vehicle Type -->
<div class="col-md-6">
  <label for="vehicleType" class="form-label">Vehicle Type</label>
  <input type="text" class="form-control" id="vehicleType" name="vehicle_type" required>
</div>

<!-- Vehicle Make -->
<div class="col-md-6">
  <label for="vehicleMake" class="form-label">Vehicle Make</label>
  <input type="text" class="form-control" id="vehicleMake" name="vehicle_make" required>
</div>

<!-- Vehicle Model -->
<div class="col-md-6">
  <label for="vehicleModel" class="form-label">Vehicle Model</label>
  <input type="text" class="form-control" id="vehicleModel" name="vehicle_model" required>
</div>

<!-- Vehicle Year -->
<div class="col-md-3">
  <label for="vehicleYear" class="form-label">Vehicle Year</label>
  <select id="vehicleYear" name="vehicle_year" class="form-select" required>
    <option value="">Select Year</option>
    <?php for ($year = date('Y'); $year >= 1980; $year--): ?>
      <option value="<?= $year ?>"><?= $year ?></option>
    <?php endfor; ?>
  </select>
</div>

<!-- Year Purchased -->
<div class="col-md-3">
  <label for="vehicleYearPurchased" class="form-label">Year Purchased</label>
  <select id="vehicleYearPurchased" name="vehicle_year_purchased" class="form-select" required>
    <option value="">Select Year</option>
    <?php for ($year = date('Y'); $year >= 1980; $year--): ?>
      <option value="<?= $year ?>"><?= $year ?></option>
    <?php endfor; ?>
  </select>
</div>

<!-- Vehicle Color -->
<div class="col-md-4">
  <label for="vehicleColor" class="form-label">Color</label>
  <select id="vehicleColor" name="vehicle_color" class="form-select" required>
    <option value="">Select Color</option>
    <?php
    $colors = ["Black", "White", "Silver", "Red", "Blue", "Other"];
    foreach ($colors as $color): ?>
      <option value="<?= $color ?>"><?= $color ?></option>
    <?php endforeach; ?>
  </select>
</div>

<!-- VIN -->
<div class="col-md-8">
  <label for="vehicleVIN" class="form-label">VIN</label>
  <input type="text" class="form-control" id="vehicleVIN" name="vehicle_VIN" required
    placeholder="e.g., 1HGCM82633A123456">
</div>

<!-- License Plate -->
<div class="col-md-6">
  <label for="vehicleLicensePlate" class="form-label">License Plate</label>
  <input type="text" class="form-control" id="vehicleLicensePlate" name="vehicle_license_tag" required>
</div>

<!-- License State -->
<div class="col-md-6">
  <label for="vehicleLicenseState" class="form-label">License State</label>
  <select id="vehicleLicenseState" name="vehicle_license_state" class="form-select" required>
    <option value="">Select State</option>
    <?php
    $states = ["AL", "AK", "AZ", "AR", "CA", "CO", "CT", "DE", "FL", "GA", "HI", "ID", "IL", "IN", "IA", "KS", "KY", "LA", "ME", "MD", "MA", "MI", "MN", "MS", "MO", "MT", "NE", "NV", "NH", "NJ", "NM", "NY", "NC", "ND", "OH", "OK"];
    foreach ($states as $state): ?>
      <option value="<?= $state ?>"><?= $state ?></option>
    <?php endforeach; ?>
  </select>
</div>

<!-- Purchase Price -->
<div class="col-md-4">
  <label for="vehiclePurchasePrice" class="form-label">Purchase Price</label>
  <input type="number" class="form-control" id="vehiclePurchasePrice" name="vehicle_purchase_price" required>
</div>

<!-- Purchase Mileage -->
<div class="col-md-4">
  <label for="vehiclePurchaseMileage" class="form-label">Purchase Mileage</label>
  <input type="number" class="form-control" id="vehiclePurchaseMileage" name="vehicle_purchase_mileage" required>
</div>

<!-- Current Mileage -->
<div class="col-md-4">
  <label for="vehicleCurrentMileage" class="form-label">Current Mileage</label>
  <input type="number" class="form-control" id="vehicleCurrentMileage" name="vehicle_current_mileage" required>
</div>

<?php renderModalEnd('addVehicleForm', 'Save Vehicle'); ?>