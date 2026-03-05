<?php
// vendor_info.php — Displays vendor details and associated maintenance records

require 'config.php';
include_once 'includes/functions.php';

$title = "Vendor Info";

include_once '../includes/head.php';
include_once '../includes/nav.php';
include_once '../includes/hero.php';

// Fetch all vendors for the dropdown
$stmt = $db->query("SELECT vendor_id, vendor_name FROM vendors ORDER BY vendor_name");
$all_vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the vendor ID from the URL
$vendor_id = $_GET['vendor_id'] ?? null;

$vendor = null;
$maintenance_logs = [];
$total_cost = 0;

if ($vendor_id) {
    // Fetch vendor details
    $stmt = $db->prepare("SELECT * FROM vendors WHERE vendor_id = :vid");
    $stmt->execute([':vid' => $vendor_id]);
    $vendor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vendor) {
        // Fetch maintenance records for this vendor
        $stmt = $db->prepare("
            SELECT m.*, mt.maintenance_type AS type_name,
                   CONCAT(v.vehicle_year, ' ', v.vehicle_make, ' ', v.vehicle_model,
                          ' (', LOWER(v.vehicle_color), ' ', LOWER(v.vehicle_type), ')') AS vehicle_full,
                   DATE_FORMAT(m.maintenance_date, '%b %e, %Y') AS maintenance_date_formatted
            FROM maintenance m
            JOIN vehicles v ON v.vehicle_id = m.vehicle_id
            LEFT JOIN maintenance_type mt ON mt.maintenance_id = m.maintenance_type_id
            WHERE m.vendor_id = :vid
            ORDER BY m.maintenance_date DESC
        ");
        $stmt->execute([':vid' => $vendor_id]);
        $maintenance_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate totals
        $total_cost = array_sum(array_column($maintenance_logs, 'maintenance_cost'));
    }
}
?>

<div class="container mt-4">

    <?php
    if ($vendor) {
        $parentUrl = 'table.php#v-pills-vendors';
        $parentLabel = 'Vendors';
        $currentItem = $vendor['vendor_name'];
        include 'includes/breadcrumbs.php';
    }
    ?> <!-- Back Button & Vendor Selection Dropdown -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
            <a href="edit_vendors.php" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Vendor List
            </a>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <form action="vendor_info.php" method="GET" class="d-inline-block shadow-sm" style="min-width: 250px;">
                <div class="input-group">
                    <span class="input-group-text bg-primary text-white border-primary"><i
                            class="fa-solid fa-store"></i></span>
                    <select name="vendor_id" class="form-select border-primary" onchange="this.form.submit()">
                        <option value="">Select a Vendor...</option>
                        <?php foreach ($all_vendors as $v): ?>
                            <option value="<?= htmlspecialchars($v['vendor_id']) ?>" <?= ($v['vendor_id'] == $vendor_id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($v['vendor_name'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <?php if (!$vendor_id): ?>
        <div class="alert alert-info">Please select a vendor from the dropdown above.</div>
    <?php elseif (!$vendor): ?>
        <div class="alert alert-danger">Vendor not found.</div>
    <?php else: ?>
        <!-- Vendor Details Card -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <span class="badge bg-light text-primary me-2"><i class="fa-solid fa-store"></i></span>
                <h5 class="d-inline mb-0">
                    <?= htmlspecialchars($vendor['vendor_name'] ?? '') ?>
                </h5>
                <?php if (($vendor['is_active'] ?? 1) == 0): ?>
                    <span class="badge bg-danger ms-2">Inactive</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <tr>
                            <th class="table-dark" style="width:15%">Name</th>
                            <td colspan="3">
                                <?= htmlspecialchars($vendor['vendor_name'] ?? '') ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="table-dark">Address</th>
                            <td colspan="3">
                                <?= htmlspecialchars($vendor['vendor_address'] ?? '') ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="table-dark">City</th>
                            <td>
                                <?= htmlspecialchars($vendor['vendor_city'] ?? '') ?>
                            </td>
                            <th class="table-dark" style="width:15%">State</th>
                            <td>
                                <?= htmlspecialchars($vendor['vendor_state'] ?? '') ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="table-dark">Zip Code</th>
                            <td>
                                <?= htmlspecialchars($vendor['vendor_zip'] ?? '') ?>
                            </td>
                            <th class="table-dark">Phone</th>
                            <td>
                                <?= htmlspecialchars($vendor['vendor_phone'] ?? '') ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="table-dark">Email</th>
                            <td colspan="3">
                                <?= htmlspecialchars($vendor['vendor_email'] ?? '') ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="table-dark">Status</th>
                            <td colspan="3">
                                <?php if (($vendor['is_active'] ?? 1) == 1): ?>
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
                        <p class="text-muted mb-0">Vehicles Serviced</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Records Card -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <span class="badge bg-light text-primary me-2"><i class="fa-solid fa-wrench"></i></span>
                <h5 class="d-inline mb-0">Maintenance Records</h5>
                <span class="badge bg-light text-primary ms-2">
                    <?= count($maintenance_logs) ?>
                </span>
            </div>
            <div class="card-body">
                <?php if (empty($maintenance_logs)): ?>
                    <div class="alert alert-secondary mb-0">No maintenance records found for this vendor.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Vehicle</th>
                                    <th>Type</th>
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
                                            <?= htmlspecialchars($m['type_name'] ?? '') ?>
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