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
                    <a href="?" class="btn btn-outline-primary px-3">
                        <i class="fa-solid fa-xmark me-1"></i> Clear Filters
                    </a>
                </div>
            </div>

            <div class="col-12 d-flex align-items-center pt-2">
                <div class="form-check form-switch">
                    <!-- Show inactive vehicles toggle -->
                    <input class="form-check-input" type="checkbox"
                        onclick="toggle_active(this, 'resultCount_vehicles')" id="show_inactive_vehicles"
                        name="show_inactive" value="1" <?= isset($_POST['show_inactive']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="show_inactive_vehicles">Show Inactive
                        Vehicles</label>
                </div>
            </div>

        </form>
    </div>
</div>

<?php
// Check if the form was submitted
$is_show_all_get = (isset($_GET['show_all']) && $_GET['show_all'] == '1');
$is_search = ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['search_string']) || isset($_POST['show_all'])));

// Check if the form was not submitted or if it was submitted with a search string
$is_default = ($_SERVER['REQUEST_METHOD'] !== 'POST') || ($is_search) || $is_show_all_get;

// If the form was submitted with a search string, process the search
if ($is_default) {
    if ($is_search) {
        // Set show_all to true if the show_all button was clicked
        $show_all = isset($_POST['show_all']);
        // Set search_string to the value of the search_string input
        $search_string = trim($_POST['search_string'] ?? '');
    } elseif ($is_show_all_get) {
        $show_all = true;
        $search_string = '';
    } else {
        // Set show_all to true if the show_all button was not clicked
        $show_all = true;
        // Set search_string to an empty string if the search_string input is empty
        $search_string = '';
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

        // Require the new model
        require_once __DIR__ . '/../models/VehicleModel.php';

        // Retrieve filtered vehicles and their aggregated stats through the Model
        $vehicles = VehicleModel::getFilteredVehicles($db, $search_string);

        // Set the display label
        $display_label = $show_all ? 'All Vehicles' : htmlspecialchars($search_string);

        // Check if no vehicles were found
        if (empty($vehicles)) {
            echo '<div class="alert alert-info">No vehicles found matching "<strong>' . $display_label . '</strong>".</div>';
        } else {
            // Get the number of active and total vehicles
            $active_count = count(array_filter($vehicles, fn($v) => ($v['is_active'] ?? 1) == 1));
            $total_count = count($vehicles);
            // Display the number of active and total vehicles
            $initial_count = isset($_POST['show_inactive']) ? $total_count : $active_count;
            echo '<h5 class="mb-3" id="resultCount_vehicles" data-active-count="' . $active_count . '" data-total-count="' . $total_count . '" data-label="' . $display_label . '">' . $initial_count . ' result(s) for "' . $display_label . '"</h5>';
            ?>
            <div class="row">

            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Vehicle</th>
                            <th>VIN</th>
                            <th>Plate</th>
                            <th>Mileage</th>
                            <th class="text-center">Info</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop through the vehicles -->
                        <?php foreach ($vehicles as $v):
                            // Check if the vehicle is inactive
                            $is_inactive = ($v['is_active'] ?? 1) == 0;
                            // Display the vehicle information
                            ?>
                            <tr
                                class="<?= $is_inactive ? 'table-secondary text-decoration-line-through opacity-50' : '' ?> <?= ($is_inactive && !isset($_POST['show_inactive'])) ? 'd-none' : '' ?>">
                                <td><?= htmlspecialchars($v['vehicle_full'] ?? '') ?></td>
                                <td><small><?= htmlspecialchars($v['vehicle_VIN'] ?? '') ?></small></td>
                                <td><?= htmlspecialchars($v['vehicle_license_tag'] ?? '') ?></td>
                                <td><?= number_format($v['vehicle_current_mileage'] ?? 0) ?></td>
                                <td class="text-center">
                                    <div class="d-flex flex-column flex-md-row gap-1 justify-content-center">
                                        <!-- Info button -->
                                        <a href="info.php?tab=vehicles&vehicle_id=<?= $v['vehicle_id'] ?>" class="btn btn-sm btn-info"
                                            title="Vehicle Info"><i class="fa-solid fa-circle-info"></i></a>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column flex-md-row gap-1 justify-content-center">

                                        <!-- Edit Button -->

                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editVehicleModal"
                                            data-vehicle-id="<?= $v['vehicle_id'] ?>"
                                            data-vehicle-type="<?= htmlspecialchars($v['vehicle_type'] ?? '') ?>"
                                            data-vehicle-make="<?= htmlspecialchars($v['vehicle_make'] ?? '') ?>"
                                            data-vehicle-model="<?= htmlspecialchars($v['vehicle_model'] ?? '') ?>"
                                            data-vehicle-year="<?= htmlspecialchars($v['vehicle_year'] ?? '') ?>"
                                            data-vehicle-year-purchased="<?= htmlspecialchars($v['vehicle_year_purchased'] ?? '') ?>"
                                            data-vehicle-color="<?= htmlspecialchars($v['vehicle_color'] ?? '') ?>"
                                            data-vehicle-vin="<?= htmlspecialchars($v['vehicle_VIN'] ?? '') ?>"
                                            data-vehicle-license-tag="<?= htmlspecialchars($v['vehicle_license_tag'] ?? '') ?>"
                                            data-vehicle-license-state="<?= htmlspecialchars($v['vehicle_license_state'] ?? '') ?>"
                                            data-vehicle-purchase-price="<?= htmlspecialchars((string)($v['vehicle_purchase_price'] ?? '')) ?>"
                                            data-vehicle-purchase-mileage="<?= (int)($v['vehicle_purchase_mileage'] ?? 0) ?>"
                                            data-vehicle-current-mileage="<?= (int)($v['vehicle_current_mileage'] ?? 0) ?>"
                                            data-is-active="<?= $v['is_active'] ?? 1 ?>" title=" Edit Vehicle"><span
                                                class="fas fa-edit"></span></button>





                                        <!-- Delete Button -->
                                        <?php if (isset($allow_delete) && $allow_delete && $_SESSION['is_admin']): ?>
                                            <?php $total_refs = ($v['maint_count'] ?? 0) + ($v['fuel_count'] ?? 0); ?>
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteVehicleModal" data-vehicle-id="<?= $v['vehicle_id'] ?>"
                                                data-vehicle-name="<?= htmlspecialchars(($v['vehicle_year'] ?? '') . ' ' . ($v['vehicle_make'] ?? '') . ' ' . ($v['vehicle_model'] ?? '')) ?>"
                                                data-maint-count="<?= $v['maint_count'] ?>" data-fuel-count="<?= $v['fuel_count'] ?>"
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
} // end if POST
?>



