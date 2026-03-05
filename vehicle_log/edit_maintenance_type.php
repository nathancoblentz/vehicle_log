<!--
Jonathan Coblentz
CPT283: PHP Programming
Final Project: Vehicle Maintenance Log
edit_maintenance_type.php

Edit Maintenance Type page — search by code, type, or description with edit/delete buttons.
Delete is blocked if any maintenance records reference the type.
-->
<?php

require 'config.php';
include_once 'includes/functions.php';

$feedback = null;
addHandlers();

$title = 'Edit Maintenance Type';
include_once '../includes/head.php';
include_once '../includes/nav.php';
include_once '../includes/hero.php';
?>

<div class="container mt-4">

    <!-- HEADER CARD -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="d-inline mb-0"><span class="fas fa-gears me-3"></span>Maintenance Types</h3>
            </div>
            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addMaintenanceTypeModal"
                title="Add Maintenance Type">
                <i class="fa-solid fa-plus text-white"></i>
            </button>
        </div>
        <div class="card-body">
            <p class="lead mb-3">
                Search for a maintenance type to edit or delete. Enter any keyword (code, type name, description).
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
                        <a href="edit_maintenance_type.php" class="btn btn-outline-primary px-3">
                            <i class="fa-solid fa-xmark me-1"></i> Clear Filters
                        </a>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <?php
    $is_search = ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['search_string']) || isset($_POST['show_all'])));
    $is_default = ($_SERVER['REQUEST_METHOD'] !== 'POST') || ($is_search);

    if ($is_default) {
        if ($is_search) {
            $show_all = isset($_POST['show_all']);
            $search_string = trim($_POST['search_string'] ?? '');
        } else {
            $show_all = true;
            $search_string = '';
        }


        if (!$show_all && empty($search_string)) {
            echo '<div class="alert alert-warning"><strong>Error!</strong> Please enter something to search for.</div>';
        } else {
            if ($show_all)
                $search_string = '%';

            $query = "SELECT mt.*,
                        (SELECT COUNT(*) FROM maintenance m WHERE m.maintenance_type_id = mt.maintenance_id) AS usage_count
                      FROM maintenance_type mt
                      WHERE mt.maintenance_code LIKE :s
                         OR mt.maintenance_type LIKE :s
                         OR mt.maintenance_description LIKE :s
                      ORDER BY mt.maintenance_type ASC";

            $stmt = $db->prepare($query);
            $stmt->bindValue(':s', '%' . $search_string . '%');
            $stmt->execute();
            $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $display_label = $show_all ? 'All Maintenance Types' : htmlspecialchars($search_string);

            if (empty($types)) {
                echo '<div class="alert alert-info">No maintenance types found matching "<strong>' . $display_label . '</strong>".</div>';
            } else {
                echo '<h5 class="mb-3">' . count($types) . ' result(s) for "' . $display_label . '"</h5>';
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
                            <?php foreach ($types as $t): ?>
                                <tr>
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
                                        <a href="maintenance_type_info.php?maintenance_id=<?= $t['maintenance_id'] ?>"
                                            class="btn btn-sm btn-info" title="View Info">
                                            <i class="fa-solid fa-circle-info text-white"></i>
                                        </a>

                                        <!-- Edit Button -->
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#editMaintenanceTypeModal" data-maintenance-id="<?= $t['maintenance_id'] ?>"
                                            data-maintenance-code="<?= htmlspecialchars($t['maintenance_code'] ?? '') ?>"
                                            data-maintenance-type="<?= htmlspecialchars($t['maintenance_type'] ?? '') ?>"
                                            data-maintenance-description="<?= htmlspecialchars($t['maintenance_description'] ?? '') ?>"
                                            data-recommended-interval-miles="<?= htmlspecialchars($t['recommended_interval_miles'] ?? '') ?>"
                                            data-recommended-interval-days="<?= htmlspecialchars($t['recommended_interval_days'] ?? '') ?>"
                                            data-recommended-cost="<?= htmlspecialchars($t['recommended_cost'] ?? '') ?>"
                                            data-is-active="<?= $t['is_active'] ?? 1 ?>" title="Edit Maintenance Type">
                                            <span class="fas fa-edit"></span>
                                        </button>

                                        <!-- Delete Button -->
                                        <?php if ($t['usage_count'] > 0): ?>
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#cannotDeleteModal"
                                                data-type-name="<?= htmlspecialchars($t['maintenance_type'] ?? '') ?>"
                                                data-usage-count="<?= $t['usage_count'] ?>" title="Delete Maintenance Type">
                                                <span class="fa-regular fa-trash-can"></span>
                                            </button>
                                        <?php else: ?>
                                            <form method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this maintenance type?');">
                                                <input type="hidden" name="delete_maintenance_type" value="1">
                                                <input type="hidden" name="maintenance_id" value="<?= $t['maintenance_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete Maintenance Type"><span
                                                        class="fa-regular fa-trash-can"></span></button>
                                            </form>
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

</div>

<!-- FORM MODALS -->
<?php addForms(); ?>

<!-- FEEDBACK MODAL -->
<?php addFeedback(); ?>

<!-- Cannot Delete Modal -->
<div class="modal fade" id="cannotDeleteModal" tabindex="-1" aria-labelledby="cannotDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cannotDeleteLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Cannot Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">
                    The maintenance type <strong id="cannotDeleteTypeName"></strong> cannot be deleted because it is
                    currently referenced by
                    <strong id="cannotDeleteCount"></strong> maintenance record(s).
                </p>
                <p class="text-muted mb-0">
                    To delete this maintenance type, you must first remove or reassign all maintenance records that use
                    it.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('cannotDeleteModal');
        if (!modal) return;
        modal.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            if (!btn) return;
            document.getElementById('cannotDeleteTypeName').textContent = btn.getAttribute('data-type-name') || '';
            document.getElementById('cannotDeleteCount').textContent = btn.getAttribute('data-usage-count') || '0';
        });
    });
</script>

<?php include_once('../includes/footer.php'); ?>