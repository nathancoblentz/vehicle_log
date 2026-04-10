<!--
Jonathan Coblentz
CPT283: PHP Programming
Final Project: Vehicle Maintenance Log

Edit Vehicle page — search for a vehicle by any field and display results with edit/delete buttons.
-->

<?php

// Include configuration and functions
require 'config.php';
include_once 'includes/functions.php';


// Initialize feedback array
$feedback = null;

// Add handlers for add, update, and delete operations
addHandlers();

// Set page title and include header
$title = 'Edit Vehicle';
$showHero = true;
$requireAuth = true;
require_once 'includes/header_bundle.php';
?>

<div class="container mt-4">

    <!-- HEADER CARD -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="d-inline mb-0"><span class="fas fa-car-side me-3"></span>Vehicles</h3>
            </div>
            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addVehicleModal"
                title="Add Vehicle">
                <i class="fa-solid fa-plus text-white"></i>
            </button>
        </div>
        <div class="card-body">
            <p class="lead mb-3">Search for a vehicle to edit or delete. Enter any keyword (make, model, year, VIN,
                color, etc.).</p>

            <!-- Search Form -->
            <!-- The form submits to the same page, which processes the search and displays results below. -->
            <form method="POST" class="row g-3">

                <div class="col-12">
                    <div class="input-group input-group-lg">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-search me-1"></i>
                        </button>
                        <!-- Search input -->
                        <input class="form-control" id="search_string" type="text" name="search_string"
                            placeholder="Make, model, year, VIN, color..."
                            value="<?= htmlspecialchars($_POST['search_string'] ?? '') ?>">
                        <!-- Show all button -->
                        <button type="submit" name="show_all" value="1" class="btn btn-outline-primary px-3">
                            <i class="fa-solid fa-list me-1"></i> Show All
                        </button>
                        <!-- Clear filters button -->
                        <a href="edit_vehicle.php" class="btn btn-outline-primary px-3">
                            <i class="fa-solid fa-xmark me-1"></i> Clear Filters
                        </a>
                    </div>
                </div>

                <div class="col-12 d-flex align-items-center pt-2">
                    <div class="form-check form-switch">
                        <!-- Show inactive vehicles toggle -->
                        <input class="form-check-input" type="checkbox" onclick="toggle_active(this, 'resultCount')"
                            id="show_inactive" name="show_inactive" value="1" <?= isset($_POST['show_inactive']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="show_inactive">Show Inactive
                            Vehicles</label>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <?php
    // Check if the form was submitted or if a search parameter was passed via GET
    $is_search = ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['search_string']) || isset($_POST['show_all']))) || isset($_GET['search']);

    // Process search or show defaults
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['search'])) {
        // Set show_all to true if the show_all button was clicked
        $show_all = isset($_POST['show_all']);
        // Use GET search param if POST is not set
        $search_string = trim($_POST['search_string'] ?? $_GET['search'] ?? '');
    } else {
        // Default state: perform a full search (show all)
        $show_all = true;
        $search_string = "";
    }

    // Check if the form was submitted with a search string
    if (!$show_all && empty($search_string)) {
            echo '<div class="alert alert-warning"><strong>Error!</strong> Please enter something to search for.</div>';
        } else {
            // Set search_string to an empty string if the show_all button was clicked
            if ($show_all) {
                $search_string = '%';
            }
            // Remove any single quotes from the search string
            $search_string = str_replace("'", '', $search_string);
            // Set show_inactive to true if the show_inactive checkbox was clicked
            $show_inactive = isset($_POST['show_inactive']);

            // Build the query
            // Get the number of maintenance records for each vehicle
            // Concatenate the vehicle year, make, model, color, and type
            $query = "SELECT *,
                (SELECT COUNT(*) FROM maintenance m WHERE m.vehicle_id = vehicles.vehicle_id) AS maint_count,
                CONCAT(
                    vehicle_year, ' ',
                    vehicle_make, ' ',
                    vehicle_model, ' (',
                    LOWER(vehicle_color), ' ',
                    LOWER(vehicle_type), ')'
                ) AS vehicle_full,
                (SELECT COUNT(*) FROM fuel f WHERE f.vehicle_id = vehicles.vehicle_id) AS fuel_count
                FROM vehicles
                WHERE (vehicle_make LIKE :s
                OR vehicle_model LIKE :s
                OR vehicle_year LIKE :s
                OR vehicle_type LIKE :s
                OR vehicle_color LIKE :s
                OR vehicle_VIN LIKE :s
                OR vehicle_license_tag LIKE :s
                OR vehicle_license_state LIKE :s)
                ORDER BY is_active DESC, vehicle_make ASC";

            $stmt = $db->prepare($query);
            $stmt->bindValue(':s', '%' . $search_string . '%');
            $stmt->execute();
            $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            // Set the display label
            $display_label = $show_all ? 'All Vehicles' : htmlspecialchars($search_string);

            // Check if no vehicles were found
            if (empty($vehicles)) {
                echo '<div class="alert alert-info">No vehicles found matching "<strong>' . $display_label . '</strong>".</div>';
            // Display results
            ?>
            <div class="row mb-3">
                <div class="col-12">
                    <h5 id="resultCount" data-label="Vehicles"
                        data-active-count="<?= count(array_filter($vehicles, function ($item) {
                            return ($item['is_active'] ?? 1) == 1;
                        })) ?>" data-total-count="<?= count($vehicles) ?>">
                        <?= count($vehicles) ?> result(s) for "<?= htmlspecialchars($search_string === '%' ? 'All' : $search_string) ?>"
                    </h5>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Year</th>
                            <th>Make/Model</th>
                            <th>Mileage</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $v): ?>
                            <tr>
                                <td><?= htmlspecialchars($v['vehicle_year'] ?? '') ?></td>
                                <td><?= htmlspecialchars(($v['vehicle_make'] ?? '') . ' ' . ($v['vehicle_model'] ?? '')) ?></td>
                                <td><?= number_format((float) ($v['vehicle_current_mileage'] ?? 0)) ?></td>
                                <td>
                                    <?php if (($v['is_active'] ?? 1) == 1): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center text-nowrap">
                                    <div class="btn-group">
                                        <!-- Edit Button -->
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#editVehicleModal" data-vehicle-id="<?= $v['vehicle_id'] ?>"
                                            data-vehicle-type="<?= htmlspecialchars($v['vehicle_type'] ?? '') ?>"
                                            data-vehicle-make="<?= htmlspecialchars($v['vehicle_make'] ?? '') ?>"
                                            data-vehicle-model="<?= htmlspecialchars($v['vehicle_model'] ?? '') ?>"
                                            data-vehicle-year="<?= htmlspecialchars($v['vehicle_year'] ?? '') ?>"
                                            data-vehicle-year-purchased="<?= htmlspecialchars($v['vehicle_year_purchased'] ?? '') ?>"
                                            data-vehicle-color="<?= htmlspecialchars($v['vehicle_color'] ?? '') ?>"
                                            data-vehicle-vin="<?= htmlspecialchars($v['vehicle_VIN'] ?? '') ?>"
                                            data-vehicle-license-tag="<?= htmlspecialchars($v['vehicle_license_tag'] ?? '') ?>"
                                            data-vehicle-license-state="<?= htmlspecialchars($v['vehicle_license_state'] ?? '') ?>"
                                            data-vehicle-purchase-price="<?= htmlspecialchars((string) ($v['vehicle_purchase_price'] ?? '')) ?>"
                                            data-vehicle-purchase-mileage="<?= round((int) ($v['vehicle_purchase_mileage'] ?? 0)) ?>"
                                            data-vehicle-current-mileage="<?= round((int) ($v['vehicle_current_mileage'] ?? 0)) ?>"
                                            data-is-active="<?= $v['is_active'] ?? 1 ?>" title="Edit Vehicle"><span
                                                class="fas fa-edit"></span></button>

                                        <!-- Delete Button -->
                                        <?php if (isset($allow_delete) && $allow_delete && $_SESSION['is_admin']): ?>
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteVehicleModal" data-vehicle-id="<?= $v['vehicle_id'] ?>"
                                                data-vehicle-name="<?= htmlspecialchars(($v['vehicle_year'] ?? '') . ' ' . ($v['vehicle_make'] ?? '') . ' ' . ($v['vehicle_model'] ?? '')) ?>"
                                                title="Delete Vehicle">
                                                <span class="fa-regular fa-trash-can"></span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php
        } // end else (results found)
    } // end else (not empty)
    ?>


</div>

<!-- FORM MODALS -->
<?php addForms(); ?>

<!-- FEEDBACK MODAL -->
<?php addFeedback(); ?>




<script>
    if (typeof window.toggle_active !== "function") {
        window.toggle_active = function (checkbox, countId) {
            let container = checkbox.closest('.tab-pane');
            if (!container) container = checkbox.closest('.container') || document;

            let inactive = document.querySelectorAll("tr.text-decoration-line-through"); for (let i = 0; i < inactive.length; i++) {
                for (let i = 0; i < inactive.length; i++) {
                    if (checkbox.checked) {
                        inactive[i].classList.remove("d-none");
                    } else {
                        inactive[i].classList.add("d-none");
                    }
                }
                // Update result count
                let countEl = document.getElementById(countId);
                if (countEl) {
                    let count = checkbox.checked ? countEl.dataset.totalCount : countEl.dataset.activeCount;
                    let label = countEl.dataset.label;
                    countEl.textContent = count + ' result(s) for "' + label + '"';
                }
            };
        }
</script>

<?php include_once('../includes/footer.php'); ?>