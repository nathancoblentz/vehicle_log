<!-- HEADER CARD -->
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="d-inline mb-0"><span class="fas fa-screwdriver-wrench me-3"></span>Services</h3>
        </div>
        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addMaintenanceTypeModal"
            title="Add Service">
            <i class="fa-solid fa-plus text-white"></i>
        </button>
    </div>
    <div class="card-body">
        <p class="lead mb-3">
            Search for a service to edit or delete. Enter any keyword (code, type name, description).
        </p>

        <!-- Search Form -->
        <form method="POST" class="row g-3">

            <div class="col-12">
                <div class="input-group input-group-lg">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-search me-1"></i>
                    </button>
                    <input class="form-control" id="search_string" type="text" name="search_string"
                        placeholder="Code, type name, description..."
                        value="<?= htmlspecialchars($_POST['search_string'] ?? '') ?>">
                    <button type="submit" name="show_all" value="1" class="btn btn-outline-primary px-3">
                        <i class="fa-solid fa-list me-1"></i> Show All
                    </button>
                    <a href="?" class="btn btn-outline-primary px-3">
                        <i class="fa-solid fa-xmark me-1"></i> Clear Filters
                    </a>
                </div>
            </div>
            <div class="col-12 d-flex align-items-center pt-2">
                <div class="form-check form-switch">
                    <!-- Show inactive maintenance toggle -->
                    <input class="form-check-input" type="checkbox"
                        onclick="toggle_active(this, 'resultCount_maintenance')" id="show_inactive_maintenance"
                        name="show_inactive" value="1" <?= isset($_POST['show_inactive']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="show_inactive_maintenance">Show Inactive
                        Services</label>
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



    if (!$show_all && empty($search_string)) {
        echo '<div class="alert alert-warning"><strong>Error!</strong> Please enter something to search for.</div>';
    } else {
        require_once __DIR__ . '/../models/ServiceModel.php';
        $types = ServiceModel::getFilteredTypes($db, $search_string);

        $display_label = $show_all ? 'All Services' : htmlspecialchars($search_string);

        if (empty($types)) {
            echo '<div class="alert alert-info">No services found matching "<strong>' . $display_label . '</strong>".</div>';
        } else {
            $active_count = count(array_filter($types, fn($t) => ($t['is_active'] ?? 1) == 1));
            $total_count = count($types);
            $initial_count = isset($_POST['show_inactive']) ? $total_count : $active_count;
            echo '<h5 class="mb-3" id="resultCount_maintenance" data-active-count="' . $active_count . '" data-total-count="' . $total_count . '" data-label="' . $display_label . '">' . $initial_count . ' result(s) for "' . $display_label . '"</h5>';
            ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Interval (Miles)</th>
                            <th>Interval (Days)</th>
                            <th>Recommended Cost</th>
                            <th>Records</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($types as $t):
                            $is_inactive = ($t['is_active'] ?? 1) == 0;
                            ?>
                            <tr
                                class="<?= $is_inactive ? 'table-secondary text-decoration-line-through opacity-50' : '' ?> <?= ($is_inactive && !isset($_POST['show_inactive'])) ? 'd-none' : '' ?>">
                                <td><?= htmlspecialchars($t['maintenance_code'] ?? '') ?></td>
                                <td>
                                    <?= htmlspecialchars($t['maintenance_type'] ?? '') ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($t['maintenance_description'] ?? '') ?>
                                </td>
                                <td>
                                    <?= $t['recommended_interval_miles'] ? number_format($t['recommended_interval_miles']) : '—' ?>
                                </td>
                                <td>
                                    <?= $t['recommended_interval_days'] ?? '—' ?>
                                </td>
                                <td>
                                    <?= $t['recommended_cost'] ? '$' . number_format($t['recommended_cost'], 2) : '—' ?>
                                </td>
                                <td>
                                    <?php if ($t['usage_count'] > 0): ?>
                                        <span class="badge bg-info">
                                            <?= $t['usage_count'] ?> record(s)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">None</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center text-nowrap">
                                    <!-- Info Button -->
                                    <a href="info.php?tab=services&service_id=<?= $t['maintenance_id'] ?>"
                                        class="btn btn-sm btn-info" title="View Info">
                                        <i class="fa-solid fa-circle-info text-white"></i>
                                    </a>

                                    <!-- Edit Button -->
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editMaintenanceTypeModal" data-maintenance-id="<?= $t['maintenance_id'] ?>"
                                        data-maintenance-code="<?= htmlspecialchars($t['maintenance_code'] ?? '') ?>"
                                        data-service="<?= htmlspecialchars($t['maintenance_type'] ?? '') ?>"
                                        data-maintenance-description="<?= htmlspecialchars($t['maintenance_description'] ?? '') ?>"
                                        data-recommended-interval-miles="<?= htmlspecialchars($t['recommended_interval_miles'] ?? '') ?>"
                                        data-recommended-interval-days="<?= htmlspecialchars($t['recommended_interval_days'] ?? '') ?>"
                                        data-recommended-cost="<?= htmlspecialchars($t['recommended_cost'] ?? '') ?>"
                                        data-is-active="<?= $t['is_active'] ?? 1 ?>" title="Edit Service">
                                        <span class="fas fa-edit"></span>
                                    </button>

                                    <!-- Delete Button -->
                                    <?php if (isset($allow_delete) && $allow_delete && $_SESSION['is_admin']): ?>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#cannotDeleteModal"
                                            data-maintenance-id="<?= $t['maintenance_id'] ?>"
                                            data-type-name="<?= htmlspecialchars($t['maintenance_type'] ?? '') ?>"
                                            data-usage-count="<?= $t['usage_count'] ?>" title="Delete Service">
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
        } // end results else
    } // end not-empty else
} // end POST
?>

