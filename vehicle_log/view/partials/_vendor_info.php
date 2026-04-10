<?php
// view/partials/_vendor_info.php
// Expected variables: $vendor, $maintenance_logs, $total_cost, $all_vendors, $vendor_id
?>

<!-- Back Button & Vendor Selection Dropdown -->
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <form action="info.php" method="GET" class="shadow-sm">
            <input type="hidden" name="tab" value="vendors">
            <div class="input-group">
                <span class="input-group-text bg-primary text-white border-primary"><i class="fa-solid fa-store"></i></span>
                <select name="vendor_id" class="form-select border-primary" onchange="this.form.submit()">
                    <option value="">Select a Vendor to report on...</option>
                    <?php foreach ($all_vendors as $v): ?>
                        <option value="<?= htmlspecialchars($v['vendor_id']) ?>" <?= ($v['vendor_id'] == $vendor_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($v['vendor_name'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <?php if ($vendor_id && $vendor): ?>
            <div class="btn-group shadow-sm" role="group">
                <button type="button" class="btn btn-warning border-primary" data-bs-toggle="modal"
                    data-bs-target="#editVendorModal" data-vendor-id="<?= $vendor['vendor_id'] ?>"
                    data-vendor-name="<?= htmlspecialchars($vendor['vendor_name'] ?? '') ?>"
                    data-vendor-address="<?= htmlspecialchars($vendor['vendor_address'] ?? '') ?>"
                    data-vendor-city="<?= htmlspecialchars($vendor['vendor_city'] ?? '') ?>"
                    data-vendor-state="<?= htmlspecialchars($vendor['vendor_state'] ?? '') ?>"
                    data-vendor-zip="<?= htmlspecialchars($vendor['vendor_zip'] ?? '') ?>"
                    data-vendor-phone="<?= htmlspecialchars($vendor['vendor_phone'] ?? '') ?>"
                    data-vendor-email="<?= htmlspecialchars($vendor['vendor_email'] ?? '') ?>"
                    data-is-active="<?= $vendor['is_active'] ?? 1 ?>" title="Edit This Vendor">
                    <i class="fas fa-edit me-1"></i> Edit
                </button>
                <?php if (isset($allow_delete) && $allow_delete): ?>
                    <button type="button" class="btn btn-danger border-primary" data-bs-toggle="modal"
                        data-bs-target="#cannotDeleteVendorModal" data-vendor-id="<?= $vendor['vendor_id'] ?>"
                        data-vendor-name="<?= htmlspecialchars($vendor['vendor_name'] ?? '') ?>"
                        data-usage-count="<?= count($maintenance_logs) ?>" title="Delete This Vendor">
                        <i class="fa-regular fa-trash-can me-1"></i> Delete
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!$vendor_id): ?>
    <div class="text-center py-5 border rounded bg-light shadow-sm">
        <i class="fa-solid fa-store fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No Vendor Selected</h4>
        <p class="text-secondary">Please use the dropdown menu above to select a service provider and view their full transaction and maintenance history.</p>
    </div>
<?php elseif (!$vendor): ?>
    <div class="alert alert-danger shadow-sm"><i class="fa-solid fa-triangle-exclamation me-2"></i>Vendor not found.</div>
<?php else: ?>
    <!-- Horizontal Tabs -->
    <ul class="nav nav-tabs mb-4 border-primary" id="vendorTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold px-4" id="vnd-details-tab" data-bs-toggle="tab" 
                    data-bs-target="#vnd-details" type="button" role="tab" aria-controls="vnd-details" aria-selected="true">
                <i class="fa-solid fa-circle-info me-2 text-primary"></i>Record Details
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4" id="vnd-history-tab" data-bs-toggle="tab" 
                    data-bs-target="#vnd-history" type="button" role="tab" aria-controls="vnd-history" aria-selected="false">
                <i class="fa-solid fa-chart-line me-2 text-primary"></i>History & Reports
            </button>
        </li>
    </ul>

    <div class="tab-content" id="vendorTabsContent">
        <!-- Tab 1: DETAILS -->
        <div class="tab-pane fade show active" id="vnd-details" role="tabpanel" aria-labelledby="vnd-details-tab">
            <!-- Vendor Details Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <span class="badge bg-light text-primary me-2"><i class="fa-solid fa-store"></i></span>
                    <h5 class="d-inline mb-0"><?= htmlspecialchars($vendor['vendor_name'] ?? '') ?></h5>
                    <?php if (($vendor['is_active'] ?? 1) == 0): ?>
                        <span class="badge bg-danger ms-2">Inactive</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <tr>
                                <th class="table-dark" style="width:15%">Name</th>
                                <td colspan="3"><?= htmlspecialchars($vendor['vendor_name'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th class="table-dark">Address</th>
                                <td colspan="3"><?= htmlspecialchars($vendor['vendor_address'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th class="table-dark">City</th>
                                <td><?= htmlspecialchars($vendor['vendor_city'] ?? '') ?></td>
                                <th class="table-dark" style="width:15%">State</th>
                                <td><?= htmlspecialchars($vendor['vendor_state'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th class="table-dark">Zip Code</th>
                                <td><?= htmlspecialchars($vendor['vendor_zip'] ?? '') ?></td>
                                <th class="table-dark">Phone</th>
                                <td><?= htmlspecialchars(formatPhone($vendor['vendor_phone'] ?? '')) ?></td>
                            </tr>
                            <tr>
                                <th class="table-dark">Email</th>
                                <td colspan="3"><?= htmlspecialchars($vendor['vendor_email'] ?? '') ?></td>
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
        </div>

        <!-- Tab 2: HISTORY/REPORTS -->
        <div class="tab-pane fade" id="vnd-history" role="tabpanel" aria-labelledby="vnd-history-tab">
            <!-- Summary Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <span class="badge bg-light text-primary me-2"><i class="fa-solid fa-chart-bar"></i></span>
                    <h5 class="d-inline mb-0">Performance Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h3 class="text-primary"><?= count($maintenance_logs) ?></h3>
                            <p class="text-muted mb-0">Total Projects</p>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-success">$<?= number_format($total_cost, 2) ?></h3>
                            <p class="text-muted mb-0">Total Revenue From Fleet</p>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-info"><?= count(array_unique(array_column($maintenance_logs, 'vehicle_id'))) ?></h3>
                            <p class="text-muted mb-0">Unique Vehicles Serviced</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Records Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <span class="badge bg-light text-primary me-2"><i class="fa-solid fa-wrench"></i></span>
                    <h5 class="d-inline mb-0">Maintenance logs</h5>
                    <span class="badge bg-light text-primary ms-2"><?= count($maintenance_logs) ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($maintenance_logs)): ?>
                        <div class="alert alert-secondary mb-0">No maintenance records found for this vendor.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered table-sm text-center">
                                <thead class="table-primary text-white text-nowrap">
                                    <tr>
                                        <th>Date</th>
                                        <th>Vehicle</th>
                                        <th class="d-none d-md-table-cell">Type</th>
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
                                            <td class="d-none d-md-table-cell"><?= htmlspecialchars($m['type_name'] ?? '') ?></td>
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
