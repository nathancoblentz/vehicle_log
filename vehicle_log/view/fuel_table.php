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
                    <a href="?" class="btn btn-outline-primary px-3">
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
$is_show_all_get = (isset($_GET['show_all']) && $_GET['show_all'] == '1');
$is_search = ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['search_string']) || isset($_POST['show_all'])));
$is_default = ($_SERVER['REQUEST_METHOD'] !== 'POST') || ($is_search) || $is_show_all_get;

if ($is_default) {
    if ($is_search) {
        $show_all = isset($_POST['show_all']);
        $search_string = trim($_POST['search_string'] ?? '');
    } elseif ($is_show_all_get) {
        $show_all = true;
        $search_string = '';
    } else {
        $show_all = true;
        $search_string = '';
    }
    
    // Always define variables first
    $search_string = trim($_POST['search_string'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $min_cost = $_POST['min_cost'] ?? '';
    $max_cost = $_POST['max_cost'] ?? '';

    require_once __DIR__ . '/../models/FuelModel.php';
    
    // Package post vars into filter array
    $filters = [
        'search_string' => $search_string,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'min_cost' => $min_cost,
        'max_cost' => $max_cost
    ];
    
    $vehicles = FuelModel::getFilteredFuel($db, $filters);

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
                            <td>$<?= number_format((float) ($v['fuel_cost_per_gallon'] ?? 0), 3) ?></td>
                            <td>$<?= number_format((float) ($v['fuel_cost'] ?? 0), 2) ?></td>
                            <td>
                                <?= htmlspecialchars($v['fuel_payment_method'] ?? '') ?>
                            </td>
                            <td><?= htmlspecialchars($v['fuel_date_formatted'] ?? '') ?></td>
                            <td class="text-center text-nowrap">
                                <!-- Edit Button -->
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editFuelModal"
                                    data-fuel-id="<?= $v['fuel_id'] ?>" data-vehicle-id="<?= $v['vehicle_id'] ?>"
                                    data-fuel-date="<?= htmlspecialchars(substr((string)($v['fuel_date'] ?? ''), 0, 10)) ?>"
                                    data-fuel-mileage="<?= round((float)($v['fuel_mileage'] ?? 0)) ?>"
                                    data-fuel-payment-method="<?= htmlspecialchars($v['fuel_payment_method'] ?? '') ?>"
                                    data-fuel-gallons="<?= htmlspecialchars($v['fuel_gallons'] ?? '') ?>"
                                    data-fuel-cost-per-gallon="<?= htmlspecialchars($v['fuel_cost_per_gallon'] ?? '') ?>"
                                    data-fuel-cost="<?= htmlspecialchars($v['fuel_cost'] ?? '') ?>"
                                    data-fuel-source="<?= htmlspecialchars($v['fuel_source'] ?? '') ?>"
                                    data-fuel-receipt-url="<?= htmlspecialchars($v['fuel_receipt_url'] ?? '') ?>"
                                    data-fuel-notes="<?= htmlspecialchars($v['fuel_notes'] ?? '') ?>" title="Edit Fuel Record"><span
                                        class="fas fa-edit"></span></button>

                                <!-- Delete Button -->
                                <?php if (isset($allow_delete) && $allow_delete && $_SESSION['is_admin']): ?>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteFuelModal"
                                        data-fuel-id="<?= $v['fuel_id'] ?>"
                                        data-fuel-name="<?= htmlspecialchars($v['fuel_date_formatted'] . ' ' . $v['vehicle_full']) ?>"
                                        title="Delete Fuel Record">
                                        <span class="fa-regular fa-trash-can"></span>
                                    </button>
                                <?php endif; ?>
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