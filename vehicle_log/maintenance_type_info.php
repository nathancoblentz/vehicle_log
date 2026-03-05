<?php
// maintenance_type_info.php — Displays maintenance type details and associated records

require 'config.php';
include_once 'includes/functions.php';

$title = "Maintenance Type Info";

include_once '../includes/head.php';
include_once '../includes/nav.php';
include_once '../includes/hero.php';

// Fetch all maintenance types for the dropdown
$stmt = $db->query("SELECT maintenance_id, maintenance_type, maintenance_code FROM maintenance_type ORDER BY maintenance_type");
$all_maintenance_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the maintenance type ID from the URL
$maintenance_id = $_GET['maintenance_id'] ?? null;

$maintenance_type_info = null;
$maintenance_logs = [];
$total_cost = 0;

if ($maintenance_id) {
    // Fetch maintenance type details
    $stmt = $db->prepare("SELECT * FROM maintenance_type WHERE maintenance_id = :mid");
    $stmt->execute([':mid' => $maintenance_id]);
    $maintenance_type_info = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($maintenance_type_info) {
        // Fetch maintenance records for this type
        $stmt = $db->prepare("
            SELECT m.*, v.vendor_name,
                   CONCAT(veh.vehicle_year, ' ', veh.vehicle_make, ' ', veh.vehicle_model,
                          ' (', LOWER(veh.vehicle_color), ' ', LOWER(veh.vehicle_type), ')') AS vehicle_full,
                   DATE_FORMAT(m.maintenance_date, '%b %e, %Y') AS maintenance_date_formatted
            FROM maintenance m
            JOIN vehicles veh ON veh.vehicle_id = m.vehicle_id
            LEFT JOIN vendors v ON v.vendor_id = m.vendor_id
            WHERE m.maintenance_type_id = :mid
            ORDER BY m.maintenance_date DESC
        ");
        $stmt->execute([':mid' => $maintenance_id]);
        $maintenance_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate totals
        $total_cost = array_sum(array_column($maintenance_logs, 'maintenance_cost'));
    }
}
?>

<div class="container mt-4">

    <?php
    if ($maintenance_type_info) {
        $parentUrl = 'table.php#v-pills-maintenance-types';
        $parentLabel = 'Maintenance Types';
        $currentItem = $maintenance_type_info['maintenance_type'];
        include 'includes/breadcrumbs.php';
    }
    ?> <!-- Back Button & Maintenance Type Selection Dropdown -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
            <a href="edit_maintenance_type.php" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Maintenance Types
            </a>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <form action="maintenance_type_info.php" method="GET" class="d-inline-block shadow-sm"
                style="min-width: 250px;">
                <div class="input-group">
                    <span class="input-group-text bg-primary text-white border-primary"><i
                            class="fa-solid fa-gears"></i></span>
                    <select name="maintenance_id" class="form-select border-primary" onchange="this.form.submit()">
                        <option value="">Select a Maintenance Type...</option>
                        <?php foreach ($all_maintenance_types as $mt): ?>
                            <option value="<?= htmlspecialchars($mt['maintenance_id']) ?>"
                                <?= ($mt['maintenance_id'] == $maintenance_id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars(($mt['maintenance_code'] ?? '') . ' - ' . ($mt['maintenance_type'] ?? '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <?php if (!$maintenance_id): ?>
        <div class="alert alert-info">Please select a maintenance type from the dropdown above.</div>
    <?php elseif (!$maintenance_type_info): ?>
        <div class="alert alert-danger">Maintenance type not found.</div>
    <?php else: ?>
        <!-- Maintenance Type Details Card -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <span class="badge bg-light text-primary me-2"><i class="fa-solid fa-gears"></i></span>
                <h5 class="d-inline mb-0">
                    <?= htmlspecialchars(($maintenance_type_info['maintenance_code'] ?? '') . ' - ' . ($maintenance_type_info['maintenance_type'] ?? '')) ?>
                </h5>
                <?php if (($maintenance_type_info['is_active'] ?? 1) == 0): ?>
                    <span class="badge bg-danger ms-2">Inactive</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <tr>
                            <th class="table-dark" style="width:15%">Code</th>
                            <td>
                                <?= htmlspecialchars($maintenance_type_info['maintenance_code'] ?? '') ?>
                            </td>
                            <th class="table-dark" style="width:15%">Name</th>
                            <td>
                                <?= htmlspecialchars($maintenance_type_info['maintenance_type'] ?? '') ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="table-dark">Description</th>
                            <td colspan="3">
                                <?= htmlspecialchars($maintenance_type_info['maintenance_description'] ?? '') ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="table-dark">Interval (Miles)</th>
                            <td>
                                <?= $maintenance_type_info['recommended_interval_miles'] ? number_format($maintenance_type_info['recommended_interval_miles']) : '—' ?>
                            </td>
                            <th class="table-dark">Interval (Days)</th>
                            <td>
                                <?= htmlspecialchars($maintenance_type_info['recommended_interval_days'] ?? '—') ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="table-dark">Recommended Cost</th>
                            <td>
                                <?= $maintenance_type_info['recommended_cost'] ? '$' . number_format($maintenance_type_info['recommended_cost'], 2) : '—' ?>
                            </td>
                            <th class="table-dark">Status</th>
                            <td>
                                <?php if (($maintenance_type_info['is_active'] ?? 1) == 1): ?>
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

        <!-- Summary Card -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <span class="badge bg-light text-primary me-2"><i class="fa-solid fa-chart-bar"></i></span>
                <h5 class="d-inline mb-0">Summary</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h3 class="text-primary">
                            <?= count($maintenance_logs) ?>
                        </h3>
                        <p class="text-muted mb-0">Total Records</p>
                    </div>
                    <div class="col-md-4">
                        <h3 class="text-success">$
                            <?= number_format($total_cost, 2) ?>
                        </h3>
                        <p class="text-muted mb-0">Total Cost</p>
                    </div>
                    <div class="col-md-4">
                        <h3 class="text-info">
                            <?= count(array_unique(array_column($maintenance_logs, 'vehicle_id'))) ?>
                        </h3>
                        <p class="text-muted mb-0">Unique Vehicles Serviced</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Records Card -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <span class="badge bg-light text-primary me-2"><i class="fa-solid fa-wrench"></i></span>
                <h5 class="d-inline mb-0">Service History</h5>
                <span class="badge bg-light text-primary ms-2">
                    <?= count($maintenance_logs) ?>
                </span>
            </div>
            <div class="card-body">
                <?php if (empty($maintenance_logs)): ?>
                    <div class="alert alert-secondary mb-0">No maintenance records found for this type.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Vehicle</th>
                                    <th>Vendor</th>
                                    <th>Description</th>
                                    <th>Cost</th>
                                    <th>Mileage</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($maintenance_logs as $m): ?>
                                    <tr>
                                        <td>
                                            <?= htmlspecialchars($m['maintenance_date_formatted']) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($m['vehicle_full'] ?? '') ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($m['vendor_name'] ?? '') ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($m['maintenance_description'] ?? '') ?>
                                        </td>
                                        <td>$
                                            <?= number_format($m['maintenance_cost'] ?? 0, 2) ?>
                                        </td>
                                        <td>
                                            <?= number_format($m['maintenance_mileage'] ?? 0) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($m['maintenance_status'] ?? '') ?>
                                        </td>
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

<?php include_once('../includes/footer.php'); ?>