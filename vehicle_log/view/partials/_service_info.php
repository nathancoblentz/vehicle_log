<?php
// view/partials/_service_info.php
// Expected variables: $service_info, $maintenance_logs, $total_cost, $all_services, $maintenance_id
?>

<!-- Back Button & Service Selection Dropdown -->
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <form action="info.php" method="GET" class="shadow-sm">
            <input type="hidden" name="tab" value="services">
            <div class="input-group">
                <span class="input-group-text bg-primary text-white border-primary"><i class="fa-solid fa-screwdriver-wrench"></i></span>
                <select name="maintenance_id" class="form-select border-primary" onchange="this.form.submit()">
                    <option value="">Select a Service to report on...</option>
                    <?php foreach ($all_services as $mt): ?>
                        <option value="<?= htmlspecialchars($mt['maintenance_id']) ?>"
                            <?= ($mt['maintenance_id'] == $maintenance_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(($mt['maintenance_code'] ?? '') . ' - ' . ($mt['maintenance_type'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <?php if ($maintenance_id && $service_info): ?>
            <div class="btn-group shadow-sm" role="group">
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
                    <i class="fas fa-edit me-1"></i> Edit
                </button>
                <?php if (isset($allow_delete) && $allow_delete): ?>
                    <button type="button" class="btn btn-danger border-primary" data-bs-toggle="modal"
                        data-bs-target="#cannotDeleteModal"
                        data-maintenance-id="<?= $service_info['maintenance_id'] ?>"
                        data-type-name="<?= htmlspecialchars($service_info['maintenance_type'] ?? '') ?>"
                        data-usage-count="<?= count($maintenance_logs) ?>" title="Delete This Type">
                        <i class="fa-regular fa-trash-can me-1"></i> Delete
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!$maintenance_id): ?>
    <div class="text-center py-5 border rounded bg-light shadow-sm">
        <i class="fa-solid fa-screwdriver-wrench fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No Service Selected</h4>
        <p class="text-secondary">Please use the dropdown menu above to select a maintenance category and view its consolidated service history and cost analysis.</p>
    </div>
<?php elseif (!$service_info): ?>
    <div class="alert alert-danger shadow-sm"><i class="fa-solid fa-triangle-exclamation me-2"></i>Service not found.</div>
<?php else: ?>
    <!-- Horizontal Tabs -->
    <ul class="nav nav-tabs mb-4 border-primary" id="maintTypeTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold px-4" id="mt-details-tab" data-bs-toggle="tab" 
                    data-bs-target="#mt-details" type="button" role="tab" aria-controls="mt-details" aria-selected="true">
                <i class="fa-solid fa-circle-info me-2 text-primary"></i>Record Details
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4" id="mt-history-tab" data-bs-toggle="tab" 
                    data-bs-target="#mt-history" type="button" role="tab" aria-controls="mt-history" aria-selected="false">
                <i class="fa-solid fa-chart-line me-2 text-primary"></i>History & Reports
            </button>
        </li>
    </ul>

    <div class="tab-content" id="maintTypeTabsContent">
        <!-- Tab 1: DETAILS -->
        <div class="tab-pane fade show active" id="mt-details" role="tabpanel" aria-labelledby="mt-details-tab">
            <!-- Service Details Card -->
            <div class="card border-0 shadow-sm mb-4">
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
                                <td><?= htmlspecialchars($service_info['maintenance_code'] ?? '') ?></td>
                                <th class="table-dark" style="width:15%">Name</th>
                                <td><?= htmlspecialchars($service_info['maintenance_type'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th class="table-dark">Description</th>
                                <td colspan="3"><?= htmlspecialchars($service_info['maintenance_description'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th class="table-dark">Interval (Miles)</th>
                                <td><?= $service_info['recommended_interval_miles'] ? number_format($service_info['recommended_interval_miles']) : '—' ?></td>
                                <th class="table-dark">Interval (Days)</th>
                                <td><?= htmlspecialchars($service_info['recommended_interval_days'] ?? '—') ?></td>
                            </tr>
                            <tr>
                                <th class="table-dark">Recommended Cost</th>
                                <td><?= $service_info['recommended_cost'] ? '$' . number_format($service_info['recommended_cost'], 2) : '—' ?></td>
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
        </div>

        <!-- Tab 2: HISTORY/REPORTS -->
        <div class="tab-pane fade" id="mt-history" role="tabpanel" aria-labelledby="mt-history-tab">
            <!-- Summary Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <span class="badge bg-light text-primary me-2"><i class="fa-solid fa-chart-bar"></i></span>
                    <h5 class="d-inline mb-0">Cost Analysis</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h3 class="text-primary"><?= count($maintenance_logs) ?></h3>
                            <p class="text-muted mb-0">Total Records</p>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-success">$<?= number_format($total_cost, 2) ?></h3>
                            <p class="text-muted mb-0">Total Lifetime Cost</p>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-info"><?= count(array_unique(array_column($maintenance_logs, 'vehicle_id'))) ?></h3>
                            <p class="text-muted mb-0">Vehicles Serviced</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Records Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <span class="badge bg-light text-primary me-2"><i class="fa-solid fa-wrench"></i></span>
                    <h5 class="d-inline mb-0">Service History</h5>
                    <span class="badge bg-light text-primary ms-2"><?= count($maintenance_logs) ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($maintenance_logs)): ?>
                        <div class="alert alert-secondary mb-0">No maintenance records found for this type.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered table-sm text-center">
                                <thead class="table-primary text-white text-nowrap">
                                    <tr>
                                        <th>Date</th>
                                        <th>Vehicle</th>
                                        <th class="d-none d-md-table-cell">Vendor</th>
                                        <th>Description</th>
                                        <th>Cost</th>
                                        <th>Mileage</th>
                                        <th class="d-none d-lg-table-cell">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="align-middle">
                                    <?php foreach ($maintenance_logs as $m): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($m['maintenance_date_formatted']) ?></td>
                                            <td class="text-nowrap small text-start"><?= htmlspecialchars($m['vehicle_full'] ?? '') ?></td>
                                            <td class="d-none d-md-table-cell"><?= htmlspecialchars($m['vendor_name'] ?? '') ?></td>
                                            <td><small><?= htmlspecialchars($m['maintenance_description'] ?? '') ?></small></td>
                                            <td class="text-nowrap">$<?= number_format($m['maintenance_cost'] ?? 0, 2) ?></td>
                                            <td><?= number_format($m['maintenance_mileage'] ?? 0) ?></td>
                                            <td class="d-none d-lg-table-cell"><span class="badge border text-dark"><?= htmlspecialchars($m['maintenance_status'] ?? '') ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
