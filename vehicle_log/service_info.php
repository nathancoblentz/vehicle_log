<?php
// service_info.php — Displays service details and associated records

require 'config.php';
$title = "Service Info";
$hideNav = true; 
$requireAuth = true;
require_once 'includes/header_bundle.php';
include_once 'includes/functions.php';
addHandlers();

// Fetch all services for the dropdown
$stmt = $db->query("SELECT maintenance_id, maintenance_type, maintenance_code FROM maintenance_type ORDER BY maintenance_type");
$all_services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the service ID from the URL
$maintenance_id = $_GET['maintenance_id'] ?? null;

$service_info = null;
$maintenance_logs = [];
$total_cost = 0;

if ($maintenance_id) {
    // Fetch service details
    $stmt = $db->prepare("SELECT * FROM maintenance_type WHERE maintenance_id = :mid");
    $stmt->execute([':mid' => $maintenance_id]);
    $service_info = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($service_info) {
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
    if ($service_info) {
        $parentUrl = 'table.php#v-pills-services';
        $parentLabel = 'Services';
        $currentItem = $service_info['maintenance_type'];
        include 'includes/breadcrumbs.php';
    }
    ?> <!-- Back Button & Service Selection Dropdown -->
    <div class="row mb-3 align-items-center">
        <div class="col-12 text-end mt-3 mt-md-0">
            <form action="service_info.php" method="GET" class="d-inline-block shadow-sm"
                style="min-width: 250px;">
                <div class="input-group">
                    <span class="input-group-text bg-primary text-white border-primary"><i
                            class="fa-solid fa-screwdriver-wrench"></i></span>
                    <select name="maintenance_id" class="form-select border-primary" onchange="this.form.submit()">
                        <option value="">Select a Service...</option>
                        <?php foreach ($all_services as $mt): ?>
                            <option value="<?= htmlspecialchars($mt['maintenance_id']) ?>"
                                <?= ($mt['maintenance_id'] == $maintenance_id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars(($mt['maintenance_code'] ?? '') . ' - ' . ($mt['maintenance_type'] ?? '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($maintenance_id && $service_info): ?>
                        <button type="button" class="btn btn-warning border-primary" data-bs-toggle="modal"
                            data-bs-target="#editMaintenanceTypeModal"
                            data-maintenance-id="<?= $service_info['maintenance_id'] ?>"
                            data-maintenance-code="<?= htmlspecialchars($service_info['maintenance_code'] ?? '') ?>"
                            data-service="<?= htmlspecialchars($service_info['maintenance_type'] ?? '') ?>"
                            data-maintenance-description="<?= htmlspecialchars($service_info['maintenance_description'] ?? '') ?>"
                            data-recommended-interval-miles="<?= htmlspecialchars((string) ($service_info['recommended_interval_miles'] ?? '')) ?>"
                            data-recommended-interval-days="<?= htmlspecialchars((string) ($service_info['recommended_interval_days'] ?? '')) ?>"
                            data-recommended-cost="<?= htmlspecialchars((string) ($service_info['recommended_cost'] ?? '')) ?>"
                            data-is-active="<?= $service_info['is_active'] ?? 1 ?>" title="Edit This Type">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php if (!$maintenance_id): ?>
        <div class="alert alert-info">Please select a service from the dropdown above.</div>
    <?php elseif (!$service_info): ?>
        <div class="alert alert-danger">Maintenance type not found.</div>
    <?php else: ?>
        <!-- Service Details Card -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <span class="badge bg-light text-primary me-2"><i class="fa-solid fa-screwdriver-wrench"></i></span>
                <h5 class="d-inline mb-0">
                    <?= htmlspecialchars(($service_info['maintenance_code'] ?? '') . ' - ' . ($service_info['maintenance_type'] ?? '')) ?>
                </h5>
                <?php if (($service_info['is_active'] ?? 1) == 0): ?>
                    <span class="badge bg-danger ms-2">Inactive</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <tr>
                            <th class="table-dark" style="width:15%">Code</th>
                            <td>
                                <?= htmlspecialchars($service_info['maintenance_code'] ?? '') ?>
                            </td>
                            <th class="table-dark" style="width:15%">Name</th>
                            <td>
                                <?= htmlspecialchars($service_info['maintenance_type'] ?? '') ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="table-dark">Description</th>
                            <td colspan="3">
                                <?= htmlspecialchars($service_info['maintenance_description'] ?? '') ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="table-dark">Interval (Miles)</th>
                            <td>
                                <?= $service_info['recommended_interval_miles'] ? number_format($service_info['recommended_interval_miles']) : '—' ?>
                            </td>
                            <th class="table-dark">Interval (Days)</th>
                            <td>
                                <?= htmlspecialchars($service_info['recommended_interval_days'] ?? '—') ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="table-dark">Recommended Cost</th>
                            <td>
                                <?= $service_info['recommended_cost'] ? '$' . number_format($service_info['recommended_cost'], 2) : '—' ?>
                            </td>
                            <th class="table-dark">Status</th>
                            <td>
                                <?php if (($service_info['is_active'] ?? 1) == 1): ?>
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

<?php
addForms();
addFeedback();
include_once('../includes/footer.php'); ?>