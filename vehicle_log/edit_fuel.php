<!--

Jonathan Coblentz
CPT283: PHP Programming
Final Project: Vehicle Maintenance Log
edit_fuel.php

Edit Fuel page — search for a vehicle by any field and display results with edit/delete buttons.
-->
<?php

require 'config.php';
include_once 'includes/functions.php';

$feedback = null;
addHandlers();

$title = 'Edit Vehicle';
include_once '../includes/head.php';
include_once '../includes/nav.php';
include_once '../includes/hero.php';
?>

<div class="container mt-4">

    <!-- HEADER CARD -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="d-inline mb-0"><span class="fas fa-gas-pump me-3"></span>Fuel Logs</h3>
            </div>
            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addFuelModal"
                title="Add Fuel Record">
                <i class="fa-solid fa-plus text-white"></i>
            </button>
        </div>
        <div class="card-body">
            <p class="lead mb-3">Search for a fuel log to edit or delete. Enter any keyword gallons range, cost range,
                date range.
            </p>

            <!-- Search Form -->
            <!-- The form submits to the same page, which processes the search and displays results below. -->
            <form method="POST" class="row g-3">

                <!-- Keyword Search -->
                <div class="col-12">
                    <div class="input-group input-group-lg">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-search me-1"></i>
                        </button>
                        <input class="form-control" id="search_string" type="text" name="search_string"
                            placeholder="Make, model, source, gallons..."
                            value="<?= htmlspecialchars($_POST['search_string'] ?? '') ?>">
                        <button type="submit" name="show_all" value="1" class="btn btn-outline-primary px-3">
                            <i class="fa-solid fa-list me-1"></i> Show All
                        </button>
                        <a href="edit_fuel.php" class="btn btn-outline-primary px-3">
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
        // Always define variables first
        $search_string = trim($_POST['search_string'] ?? '');
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $min_cost = $_POST['min_cost'] ?? '';
        $max_cost = $_POST['max_cost'] ?? '';

        $conditions = [];
        $params = [];


        // Keyword search
        $conditions[] = "(vehicle_make LIKE :s
                          OR vehicle_model LIKE :s
                          OR vehicle_year LIKE :s
                          OR vehicle_color LIKE :s
                          OR vehicle_type LIKE :s
                          OR fuel_source LIKE :s
                          OR fuel_gallons LIKE :s
                          OR fuel_payment_method LIKE :s
                          OR fuel_cost_per_gallon LIKE :s)";
        $params[':s'] = "%$search_string%";

        if (!empty($start_date) && !empty($end_date)) {
            $conditions[] = "fuel.fuel_date BETWEEN :start_date AND :end_date";
            $params[':start_date'] = $start_date;
            $params[':end_date'] = $end_date;
        } elseif (!empty($start_date)) {
            $conditions[] = "fuel.fuel_date >= :start_date";
            $params[':start_date'] = $start_date;
        } elseif (!empty($end_date)) {
            $conditions[] = "fuel.fuel_date <= :end_date";
            $params[':end_date'] = $end_date;
        }

        if ($min_cost !== '' && $max_cost !== '') {
            $conditions[] = '(fuel.fuel_cost_per_gallon * fuel.fuel_gallons) BETWEEN :min_cost AND :max_cost';
            $params[':min_cost'] = $min_cost;
            $params[':max_cost'] = $max_cost;
        }

        $query = "
            SELECT vehicles.*, fuel.*,
                (fuel.fuel_cost_per_gallon * fuel.fuel_gallons) AS fuel_cost,
                CONCAT(
                vehicle_year, ' ',
                vehicle_make, ' ',
                vehicle_model, '   (',
                LOWER(vehicle_color), ' ',
                LOWER(vehicle_type), ')')
                AS vehicle_full,
                DATE_FORMAT(fuel.fuel_date, '%b %e, %Y') AS fuel_date_formatted
            FROM vehicles
            JOIN fuel ON vehicles.vehicle_id = fuel.vehicle_id
        ";

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY fuel.fuel_date DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

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
                            <th>Fuel Source</th>
                            <th>Gallons</th>
                            <th>Cost/Gal</th>
                            <th>Total Cost</th>
                            <th>Payment Method</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $v): ?>
                            <tr>
                                <td><?= htmlspecialchars($v['vehicle_full']) ?></td>
                                <td><?= htmlspecialchars($v['fuel_source'] ?? '') ?></td>
                                <td><?= htmlspecialchars($v['fuel_gallons'] ?? '') ?></td>
                                <td>$<?= number_format($v['fuel_cost_per_gallon'] ?? 0, 3) ?></td>
                                <td>$<?= number_format($v['fuel_cost'] ?? 0, 2) ?></td>
                                <td>
                                    <?= htmlspecialchars($v['fuel_payment_method'] ?? '') ?>
                                </td>
                                <td><?= htmlspecialchars($v['fuel_date_formatted'] ?? '') ?></td>
                                <td class="text-center text-nowrap">
                                    <!-- Edit Button -->
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editFuelModal"
                                        data-fuel-id="<?= $v['fuel_id'] ?>" data-vehicle-id="<?= $v['vehicle_id'] ?>"
                                        data-fuel-date="<?= htmlspecialchars($v['fuel_date'] ?? '') ?>"
                                        data-fuel-mileage="<?= htmlspecialchars($v['fuel_mileage'] ?? '') ?>"
                                        data-fuel-payment-method="<?= htmlspecialchars($v['fuel_payment_method'] ?? '') ?>"
                                        data-fuel-gallons="<?= htmlspecialchars($v['fuel_gallons'] ?? '') ?>"
                                        data-fuel-cost-per-gallon="<?= htmlspecialchars($v['fuel_cost_per_gallon'] ?? '') ?>"
                                        data-fuel-cost="<?= htmlspecialchars($v['fuel_cost'] ?? '') ?>"
                                        data-fuel-source="<?= htmlspecialchars($v['fuel_source'] ?? '') ?>"
                                        data-fuel-receipt-url="<?= htmlspecialchars($v['fuel_receipt_url'] ?? '') ?>"
                                        data-fuel-notes="<?= htmlspecialchars($v['fuel_notes'] ?? '') ?>"
                                        title="Edit Fuel Record"><span class="fas fa-edit"></span></button>

                                    <!-- Delete Button -->
                                    <form method="POST" class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this fuel log entry?');">
                                        <input type="hidden" name="delete_fuel" value="1">
                                        <input type="hidden" name="fuel_id" value="<?= $v['fuel_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Fuel Record"><span
                                                class="fa-regular fa-trash-can"></span></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php
        } // end else (results found)
    
    } // end POST if
    
    ?>

</div>

<!-- FORM MODALS -->
<?php addForms(); ?>

<!-- FEEDBACK MODAL -->
<?php addFeedback(); ?>

<?php include_once('../includes/footer.php'); ?>