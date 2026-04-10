<?php
// vehicle_info.php

require 'config.php'; //     Database connection
$title = "Vehicle Info";
$hideNav = true; 
$requireAuth = true;
require_once 'includes/header_bundle.php';
include_once 'includes/functions.php'; // For renderVehiclesTable, renderFuelTable, renderMaintenanceTable
addHandlers();

// Fetch all vehicles for the dropdown
$stmt = $db->query("SELECT vehicle_id, vehicle_year, vehicle_make, vehicle_model FROM vehicles ORDER BY vehicle_year DESC, vehicle_make, vehicle_model");
$all_vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the vehicle ID from the URL
$vehicle_id = $_GET['vehicle_id'] ?? null;

$vehicle = null;
$fuel_logs = [];
$maintenance_logs = [];

if ($vehicle_id) {
    // Fetch vehicle details
    $stmt = $db->prepare("SELECT * FROM vehicles WHERE vehicle_id = :vid"); // Prepare the SQL statement
    $stmt->execute([':vid' => $vehicle_id]); // Execute the SQL statement
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the vehicle details

    if ($vehicle) {
        // Fetch fuel logs
        $stmt = $db->prepare("SELECT *, (fuel_gallons * fuel_cost_per_gallon) AS fuel_cost, DATE_FORMAT(fuel_date, '%b %e, %Y') AS fuel_date_formatted 
                              FROM fuel 
                              WHERE vehicle_id = :vid 
                              ORDER BY fuel_date DESC"); // Prepare the SQL statement
        $stmt->execute([':vid' => $vehicle_id]); // Execute the SQL statement
        $fuel_logs = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch the fuel logs

        // Fetch maintenance records
        $stmt = $db->prepare("SELECT m.*, mt.maintenance_type AS type_name, v.vendor_name,
                              DATE_FORMAT(maintenance_date, '%b %e, %Y') AS maintenance_date_formatted 
                              FROM maintenance m
                              LEFT JOIN maintenance_type mt ON m.maintenance_type_id = mt.maintenance_id
                              LEFT JOIN vendors v ON v.vendor_id = m.vendor_id
                              WHERE m.vehicle_id = :vid 
                              ORDER BY maintenance_date DESC"); // Prepare the SQL statement
        $stmt->execute([':vid' => $vehicle_id]); // Execute the SQL statement
        $maintenance_logs = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch the maintenance logs
    }
}
?>

<div class="container mt-4">
    <!-- logged in user right align -->
    <div class="d-flex justify-content-end align-items-center py-3">
        <span class="text-muted me-2">
            <i class="fa-solid fa-user-circle me-1"></i>
            You are logged in as <strong><?= htmlspecialchars($_SESSION['user']['name'] ?? 'System User') ?></strong>.
        </span>
        <a class="btn btn-outline-secondary btn-sm" href="<?= $baseURL ?>vehicle_log/logout.php">
            <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
        </a>
    </div>

    <?php
    if ($vehicle) {
        $parentUrl = 'table.php#v-pills-vehicles';
        $parentLabel = 'Vehicles';
        $currentItem = $vehicle['vehicle_year'] . ' ' . $vehicle['vehicle_make'] . ' ' . $vehicle['vehicle_model'];
        include 'includes/breadcrumbs.php';
    }
    ?> <!-- Back Button & Vehicle Selection Dropdown -->
    <div class="row mb-3 align-items-center">
        <div class="col-12 text-end mt-3 mt-md-0">
            <form action="vehicle_info.php" method="GET" class="d-inline-block shadow-sm" style="min-width: 250px;">
                <div class="input-group">
                    <span class="input-group-text bg-primary text-white border-primary"><i
                            class="fa-solid fa-car"></i></span>
                    <select name="vehicle_id" class="form-select border-primary" onchange="this.form.submit()">
                        <option value="">Select a Vehicle...</option>
                        <?php foreach ($all_vehicles as $v): ?>
                            <option value="<?= htmlspecialchars($v['vehicle_id']) ?>" <?= ($v['vehicle_id'] == $vehicle_id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars(($v['vehicle_year'] ?? '') . ' ' . ($v['vehicle_make'] ?? '') . ' ' . ($v['vehicle_model'] ?? '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($vehicle_id && $vehicle): ?>
                        <button type="button" class="btn btn-warning border-primary" data-bs-toggle="modal"
                            data-bs-target="#editVehicleModal" data-vehicle-id="<?= $vehicle['vehicle_id'] ?>"
                            data-vehicle-type="<?= htmlspecialchars($vehicle['vehicle_type'] ?? '') ?>"
                            data-vehicle-make="<?= htmlspecialchars($vehicle['vehicle_make'] ?? '') ?>"
                            data-vehicle-model="<?= htmlspecialchars($vehicle['vehicle_model'] ?? '') ?>"
                            data-vehicle-year="<?= htmlspecialchars($vehicle['vehicle_year'] ?? '') ?>"
                            data-vehicle-year-purchased="<?= htmlspecialchars($vehicle['vehicle_year_purchased'] ?? '') ?>"
                            data-vehicle-color="<?= htmlspecialchars($vehicle['vehicle_color'] ?? '') ?>"
                            data-vehicle-vin="<?= htmlspecialchars($vehicle['vehicle_VIN'] ?? '') ?>"
                            data-vehicle-license-tag="<?= htmlspecialchars($vehicle['vehicle_license_tag'] ?? '') ?>"
                            data-vehicle-license-state="<?= htmlspecialchars($vehicle['vehicle_license_state'] ?? '') ?>"
                            data-vehicle-purchase-price="<?= htmlspecialchars((string) ($vehicle['vehicle_purchase_price'] ?? '')) ?>"
                            data-vehicle-purchase-mileage="<?= round((int) ($vehicle['vehicle_purchase_mileage'] ?? 0)) ?>"
                            data-vehicle-current-mileage="<?= round((int) ($vehicle['vehicle_current_mileage'] ?? 0)) ?>"
                            data-is-active="<?= $vehicle['is_active'] ?? 1 ?>" title="Edit This Vehicle">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php if (!$vehicle_id): ?>
        <div class="alert alert-info">Please select a vehicle from the dropdown above.</div>
    <?php elseif (!$vehicle): ?>
        <div class="alert alert-danger">Vehicle not found.</div>
    <?php else: ?>
        <!-- Vehicle Details Card -->
        <div class="card mb-4 border-0 shadow-sm"> <!-- Vehicle details card -->
            <div class="card-header bg-primary text-white py-3">
                <span class="badge bg-light text-primary me-2"><i class="fa-solid fa-car"></i></span>
                <h5 class="d-inline mb-0">
                    <!-- Display the vehicle name -->
                    <?= htmlspecialchars(($vehicle['vehicle_year'] ?? '') . ' ' . ($vehicle['vehicle_make'] ?? '') . ' ' . ($vehicle['vehicle_model'] ?? '')) ?>
                </h5>
                <?php if (($vehicle['is_active'] ?? 1) == 0): ?>
                    <span class="badge bg-danger ms-2">Inactive</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <tr>
                            <th class="table-dark" style="width:15%">Make</th>
                            <td><?= htmlspecialchars($vehicle['vehicle_make'] ?? '') ?></td>
                            <th class="table-dark" style="width:15%">Model</th>
                            <td><?= htmlspecialchars($vehicle['vehicle_model'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th class="table-dark">Year</th>
                            <td><?= htmlspecialchars($vehicle['vehicle_year'] ?? '') ?></td>
                            <th class="table-dark">Color</th>
                            <td><?= htmlspecialchars($vehicle['vehicle_color'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th class="table-dark">Type</th>
                            <td><?= htmlspecialchars($vehicle['vehicle_type'] ?? '') ?></td>
                            <th class="table-dark">VIN</th>
                            <td><?= htmlspecialchars($vehicle['vehicle_VIN'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th class="table-dark">License Plate</th>
                            <td><?= htmlspecialchars($vehicle['vehicle_license_tag'] ?? '') ?></td>
                            <th class="table-dark">State</th>
                            <td><?= htmlspecialchars($vehicle['vehicle_license_state'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th class="table-dark">Purchase Price</th>
                            <td>$<?= number_format($vehicle['vehicle_purchase_price'] ?? 0, 2) ?></td>
                            <th class="table-dark">Year Purchased</th>
                            <td><?= htmlspecialchars($vehicle['vehicle_year_purchased'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th class="table-dark">Purchase Mileage</th>
                            <td><?= number_format((int)($vehicle['vehicle_purchase_mileage'] ?? 0)) ?></td>
                            <th class="table-dark">Current Mileage</th>
                            <td><?= number_format((int)($vehicle['vehicle_current_mileage'] ?? 0)) ?></td>
                        </tr>
                        <tr>
                            <th class="table-dark">Status</th>
                            <td colspan="3">
                                <?php if (($vehicle['is_active'] ?? 1) == 1): ?>
                                    <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="fa-solid fa-ban me-1"></i>Inactive</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Fuel Logs Card -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <span class="badge bg-light text-info me-2"><i class="fa-solid fa-gas-pump"></i></span>
                <h5 class="d-inline mb-0">Fuel Logs</h5>
                <span class="badge bg-light text-info ms-2"><?= count($fuel_logs) ?></span>
            </div>
            <div class="card-body">
                <?php if (empty($fuel_logs)): ?>
                    <div class="alert alert-secondary mb-0">No fuel records found for this vehicle.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Source</th>
                                    <th>Gallons</th>
                                    <th>Cost/Gal</th>
                                    <th>Total Cost</th>
                                    <th>Mileage</th>
                                    <th>Payment</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fuel_logs as $f): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($f['fuel_date_formatted']) ?></td>
                                        <td><?= htmlspecialchars($f['fuel_source'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($f['fuel_gallons'] ?? '') ?></td>
                                        <td>$<?= number_format($f['fuel_cost_per_gallon'] ?? 0, 3) ?></td>
                                        <td>$<?= number_format($f['fuel_cost'] ?? 0, 2) ?></td>
                                        <td><?= number_format($f['fuel_mileage'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars($f['fuel_payment_method'] ?? '') ?></td>
                                        <td><small><?= htmlspecialchars($f['fuel_notes'] ?? '') ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Maintenance Records Card -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <span class="badge bg-light text-primary me-2"><i class="fa-solid fa-wrench"></i></span>
                <h5 class="d-inline mb-0">Maintenance Records</h5>
                <span class="badge bg-light text-primary ms-2"><?= count($maintenance_logs) ?></span>
            </div>
            <div class="card-body">
                <?php if (empty($maintenance_logs)): ?>
                    <div class="alert alert-secondary mb-0">No maintenance records found for this vehicle.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Vendor</th>
                                    <th>Description</th>
                                    <th>Cost</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($maintenance_logs as $m): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($m['maintenance_date_formatted']) ?></td>
                                        <td><?= htmlspecialchars($m['type_name'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($m['vendor_name'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($m['maintenance_description'] ?? '') ?></td>
                                        <td>$<?= number_format($m['maintenance_cost'] ?? 0, 2) ?></td>
                                        <td><?= htmlspecialchars($m['maintenance_status'] ?? '') ?></td>
                                        <td><small><?= htmlspecialchars($m['maintenance_notes'] ?? '') ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php
addForms();
addFeedback();
include_once('../includes/footer.php'); ?>