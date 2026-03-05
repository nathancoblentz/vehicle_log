<?php
/*
Jonathan Coblentz
CPT283: PHP Programming
Final Project: Vehicle Maintenance Log
edit_fuel.php

Edit Maintenance page — search for a vehicle by any field and display results with edit/delete buttons.
*/

require 'config.php';
include_once 'includes/functions.php';

$feedback = null;
addHandlers();

$title = 'Edit Maintenance Record';
include_once '../includes/head.php';
include_once '../includes/nav.php';
include_once '../includes/hero.php';
?>

<div class="container mt-4">

    <!-- HEADER CARD -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="d-inline mb-0"><span class="fas fa-wrench me-3"></span>Maintenance Records</h3>
            </div>
            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addMaintenanceModal"
                title="Add Maintenance Record">
                <i class="fa-solid fa-plus text-white"></i>
            </button>
        </div>
        <div class="card-body">
            <p class="lead mb-3">
                Search for a Maintenance Record to edit or delete. Enter any vehicle keyword, vendor, description,
                status, cost range, or date range.
            </p>

            <!-- Search Form -->
            <form method="POST" class="row g-3">

                <!-- Keyword Search -->
                <div class="col-12">
                    <div class="input-group input-group-lg">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-search me-1"></i>
                        </button>
                        <input class="form-control" id="search_string" type="text" name="search_string"
                            placeholder="Make, model, vendor, description, status..."
                            value="<?= htmlspecialchars($_POST['search_string'] ?? '') ?>">
                        <button type="submit" name="show_all" value="1" class="btn btn-outline-primary px-3">
                            <i class="fa-solid fa-list me-1"></i> Show All
                        </button>
                        <a href="edit_maintenance.php" class="btn btn-outline-primary px-3">
                            <i class="fa-solid fa-xmark me-1"></i> Clear Filters
                        </a>
                    </div>
                </div>

                <!-- Divider -->
                <div class="col-12">
                    <hr class="my-2">
                </div>

                <!-- Date Range -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold">Start Date</label>
                    <input type="date" name="start_date" class="form-control"
                        value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>">
                </div>

                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold">End Date</label>
                    <input type="date" name="end_date" class="form-control"
                        value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>">
                </div>

                <!-- Cost Range -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold">Min Cost</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" name="min_cost" class="form-control"
                            value="<?= htmlspecialchars($_POST['min_cost'] ?? '') ?>">
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold">Max Cost</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" name="max_cost" class="form-control"
                            value="<?= htmlspecialchars($_POST['max_cost'] ?? '') ?>">
                    </div>
                </div>

                <!-- Filter / Clear Buttons -->
                <div class="col-12 d-flex justify-content-end pt-2">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-filter me-1"></i> Filter Results
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <?php
    $is_search = ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['search_string']) || isset($_POST['show_all'])));
    $is_default = ($_SERVER['REQUEST_METHOD'] !== 'POST') || ($is_search);

    if ($is_default) {
        // Gather inputs
        $search_string = trim($_POST['search_string'] ?? '');
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $min_cost = $_POST['min_cost'] ?? '';
        $max_cost = $_POST['max_cost'] ?? '';

        $conditions = [];
        $params = [];


        // Keyword search
        if (!empty($search_string)) {
            $conditions[] = "(vehicle_make LIKE :s
                              OR vehicle_model LIKE :s
                              OR vehicle_year LIKE :s
                              OR vehicle_color LIKE :s
                              OR vehicle_type LIKE :s
                              OR v.vendor_name LIKE :s
                              OR maintenance_description LIKE :s
                              OR maintenance_status LIKE :s)";
            $params[':s'] = "%$search_string%";
        }

        // Date range
        if (!empty($start_date) && !empty($end_date)) {
            $conditions[] = "maintenance.maintenance_date BETWEEN :start_date AND :end_date";
            $params[':start_date'] = $start_date;
            $params[':end_date'] = $end_date;
        } elseif (!empty($start_date)) {
            $conditions[] = "maintenance.maintenance_date >= :start_date";
            $params[':start_date'] = $start_date;
        } elseif (!empty($end_date)) {
            $conditions[] = "maintenance.maintenance_date <= :end_date";
            $params[':end_date'] = $end_date;
        }

        // Cost range
        if ($min_cost !== '' && $max_cost !== '') {
            $conditions[] = "maintenance.maintenance_cost BETWEEN :min_cost AND :max_cost";
            $params[':min_cost'] = $min_cost;
            $params[':max_cost'] = $max_cost;
        } elseif ($min_cost !== '') {
            $conditions[] = "maintenance.maintenance_cost >= :min_cost";
            $params[':min_cost'] = $min_cost;
        } elseif ($max_cost !== '') {
            $conditions[] = "maintenance.maintenance_cost <= :max_cost";
            $params[':max_cost'] = $max_cost;
        }

        // Build query
        $query = "
            SELECT maintenance.*,
                CONCAT(
                    vehicle_year, ' ',
                    vehicle_make, ' ',
                    vehicle_model, ' (',
                    LOWER(vehicle_color), ' ',
                    LOWER(vehicle_type), ')'
                ) AS vehicle_full,
                v.vendor_name,
                DATE_FORMAT(maintenance.maintenance_date, '%b %e, %Y') AS maintenance_date_formatted
            FROM vehicles
            JOIN maintenance ON vehicles.vehicle_id = maintenance.vehicle_id
            LEFT JOIN vendors v ON v.vendor_id = maintenance.vendor_id
        ";

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY maintenance.maintenance_date DESC";

        // Execute
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        // Display results
        if (empty($vehicles)) {
            echo '<div class="alert alert-info">No vehicles found matching "<strong>' . htmlspecialchars($search_string) . '</strong>".</div>';
        } else {
            echo '<h5 class="mb-3">' . count($vehicles) . ' result(s) for "' . htmlspecialchars($search_string) . '"</h5>';
            ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Vehicle</th>
                            <th>Vendor</th>
                            <th>Description</th>
                            <th>Cost</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $v): ?>
                            <tr>
                                <td><?= htmlspecialchars($v['vehicle_full']) ?></td>
                                <td><?= htmlspecialchars($v['vendor_name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($v['maintenance_description'] ?? '') ?></td>
                                <td>$<?= number_format($v['maintenance_cost'] ?? 0, 2) ?></td>
                                <td><?= htmlspecialchars($v['maintenance_status'] ?? '') ?></td>
                                <td><?= htmlspecialchars($v['maintenance_date_formatted'] ?? '') ?></td>
                                <td class="text-center text-nowrap">
                                    <!-- Edit Button -->
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editMaintenanceModal" data-maintenance-id="<?= $v['maintenance_id'] ?>"
                                        data-vehicle-id="<?= $v['vehicle_id'] ?>"
                                        data-maintenance-type-id="<?= htmlspecialchars($v['maintenance_type_id'] ?? '') ?>"
                                        data-maintenance-date="<?= htmlspecialchars($v['maintenance_date'] ?? '') ?>"
                                        data-maintenance-mileage="<?= htmlspecialchars($v['maintenance_mileage'] ?? '') ?>"
                                        data-maintenance-cost="<?= htmlspecialchars($v['maintenance_cost'] ?? '') ?>"
                                        data-vendor-id="<?= htmlspecialchars($v['vendor_id'] ?? '') ?>"
                                        data-maintenance-description="<?= htmlspecialchars($v['maintenance_description'] ?? '') ?>"
                                        data-maintenance-status="<?= htmlspecialchars($v['maintenance_status'] ?? '') ?>"
                                        title="Edit Record">
                                        <span class="fas fa-edit"></span>
                                    </button>

                                    <!-- Delete Button -->
                                    <form method="POST" class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this maintenance record entry?');">
                                        <input type="hidden" name="delete_maintenance" value="1">
                                        <input type="hidden" name="maintenance_id" value="<?= $v['maintenance_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Record"><span
                                                class="fa-regular fa-trash-can"></span></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php
        } // end results else
    } // end POST
    ?>

</div>

<!-- FORM MODALS -->
<?php addForms(); ?>

<!-- FEEDBACK MODAL -->
<?php addFeedback(); ?>

<?php include_once('../includes/footer.php'); ?>