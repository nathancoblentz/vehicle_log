

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
                        <input class="form-check-input" type="checkbox" onclick="toggle_active()" id="show_inactive"
                            name="show_inactive" value="1" <?= isset($_POST['show_inactive']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="show_inactive">Show Inactive
                            Vehicles</label>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <?php
    // Check if the form was submitted
    $is_search = ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['search_string']) || isset($_POST['show_all'])));

    // Check if the form was not submitted or if it was submitted with a search string
    $is_default = ($_SERVER['REQUEST_METHOD'] !== 'POST') || ($is_search);

    // If the form was submitted with a search string, process the search
    if ($is_default) {
        if ($is_search) {
            // Set show_all to true if the show_all button was clicked
            $show_all = isset($_POST['show_all']);
            // Set search_string to the value of the search_string input
            $search_string = trim($_POST['search_string'] ?? '');
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
            } else {
                // Get the number of active and total vehicles
                $active_count = count(array_filter($vehicles, fn($v) => ($v['is_active'] ?? 1) == 1));
                $total_count = count($vehicles);
                // Display the number of active and total vehicles
                echo '<h5 class="mb-3" id="resultCount" data-active-count="' . $active_count . '" data-total-count="' . $total_count . '" data-label="' . $display_label . '">' . $active_count . ' result(s) for "' . $display_label . '"</h5>';
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
                                <tr class="<?= $is_inactive ? 'table-secondary text-decoration-line-through opacity-50 d-none' : '' ?>">
                                    <td><?= htmlspecialchars($v['vehicle_full'] ?? '') ?></td>
                                    <td><small><?= htmlspecialchars($v['vehicle_VIN'] ?? '') ?></small></td>
                                    <td><?= htmlspecialchars($v['vehicle_license_tag'] ?? '') ?></td>
                                    <td><?= number_format($v['vehicle_current_mileage'] ?? 0) ?></td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column flex-md-row gap-1 justify-content-center">
                                            <!-- Info button -->
                                            <a href="vehicle_info.php?vehicle_id=<?= $v['vehicle_id'] ?>" class="btn btn-sm btn-info"
                                                title="Vehicle Info"><i class="fa-solid fa-circle-info"></i></a>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column flex-md-row gap-1 justify-content-center">

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
                                                data-vehicle-purchase-price="<?= htmlspecialchars($v['vehicle_purchase_price'] ?? '') ?>"
                                                data-vehicle-purchase-mileage="<?= htmlspecialchars($v['vehicle_purchase_mileage'] ?? '') ?>"
                                                data-vehicle-current-mileage="<?= htmlspecialchars($v['vehicle_current_mileage'] ?? '') ?>"
                                                data-is-active="<?= $v['is_active'] ?? 1 ?>" title=" Edit Vehicle"><span
                                                    class="fas fa-edit"></span></button>





                                            <!-- Delete Button -->
                                            <?php $total_refs = ($v['maint_count'] ?? 0) + ($v['fuel_count'] ?? 0); ?>
                                            <?php if ($total_refs > 0): ?>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#cannotDeleteVehicleModal" data-vehicle-id="<?= $v['vehicle_id'] ?>"
                                                    data-vehicle-name="<?= htmlspecialchars(($v['vehicle_year'] ?? '') . ' ' . ($v['vehicle_make'] ?? '') . ' ' . ($v['vehicle_model'] ?? '')) ?>"
                                                    data-maint-count="<?= $v['maint_count'] ?>" data-fuel-count="<?= $v['fuel_count'] ?>"
                                                    title="Delete Vehicle">
                                                    <span class="fa-regular fa-trash-can"></span>
                                                </button>
                                            <?php else: ?>
                                                <form method="POST" class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this vehicle?');">
                                                    <input type="hidden" name="delete_vehicle" value="1">
                                                    <input type="hidden" name="vehicle_id" value="<?= $v['vehicle_id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete Vehicle"><span
                                                            class="fa-regular fa-trash-can"></span></button>
                                                </form>
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





<!-- Cannot Delete Vehicle Modal -->
<div class="modal fade" id="cannotDeleteVehicleModal" tabindex="-1" aria-labelledby="cannotDeleteVehicleLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="cannotDeleteVehicleLabel">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>Cannot Delete Vehicle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">
                    The vehicle <strong id="cannotDeleteVehicleName"></strong> cannot be deleted because it has
                    associated records:
                </p>
                <ul class="mb-3">
                    <li><strong id="cannotDeleteMaintCount"></strong> maintenance record(s)</li>
                    <li><strong id="cannotDeleteFuelCount"></strong> fuel record(s)</li>
                </ul>
                <hr>
                <p class="mb-0">
                    Would you like to <strong>set this vehicle to inactive</strong> instead? Inactive vehicles are
                    hidden from searches by default but can still be viewed using the "Show Inactive" checkbox.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="deactivateVehicleForm" class="d-inline">
                    <input type="hidden" name="deactivate_vehicle" value="1">
                    <input type="hidden" name="vehicle_id" id="deactivateVehicleId" value="">
                    <button type="submit" class="btn btn-warning">
                        <i class="fa-solid fa-eye-slash me-1"></i> Set Inactive
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Cannot delete vehicle modal
    document.addEventListener('DOMContentLoaded', function () {


        // Get the modal
        const modal = document.getElementById('cannotDeleteVehicleModal');
        if (!modal) return;

        // Show the modal
        modal.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget; // Get the button that triggered the modal
            if (!btn) return;

            // Set the modal title
            document.getElementById('cannotDeleteVehicleName').textContent = btn.getAttribute('data-vehicle-name') || '';
            // Set the modal body   
            document.getElementById('cannotDeleteMaintCount').textContent = btn.getAttribute('data-maint-count') || '0';
            document.getElementById('cannotDeleteFuelCount').textContent = btn.getAttribute('data-fuel-count') || '0';
            // Set the modal footer
            document.getElementById('deactivateVehicleId').value = btn.getAttribute('data-vehicle-id') || '';
        });
    });
</script>




<script>
    function toggle_active() {
        let inactive = document.getElementsByClassName("text-decoration-line-through");
        let toggle = document.getElementById("show_inactive");
        for (let i = 0; i < inactive.length; i++) {
            if (toggle.checked) {
                inactive[i].classList.remove("d-none");
            } else {
                inactive[i].classList.add("d-none");
            }
        }
        // Update result count
        let countEl = document.getElementById("resultCount");
        if (countEl) {
            let count = toggle.checked ? countEl.dataset.totalCount : countEl.dataset.activeCount;
            let label = countEl.dataset.label;
            countEl.textContent = count + ' result(s) for "' + label + '"';
        }
    }
</script>

<!-- FEEDBACK MODAL -->
<?php renderFeedbackModal(); ?>