<?php
// view/partials/_maintenance_info.php
// Expected variables: $maint_log_detail, $all_maintenance_logs, $log_id
?>

<!-- Back Button & Selection Dropdown -->
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <form action="info.php" method="GET" class="shadow-sm">
            <input type="hidden" name="tab" value="maintenance">
            <div class="input-group">
                <span class="input-group-text bg-primary text-white border-primary"><i class="fa-solid fa-wrench"></i></span>
                <select name="log_id" class="form-select border-primary" onchange="this.form.submit()">
                    <option value="">Select a specific Maintenance Record to report on...</option>
                    <?php foreach ($all_maintenance_logs as $m): ?>
                        <option value="<?= htmlspecialchars($m['maintenance_id']) ?>" <?= ($m['maintenance_id'] == $log_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['maintenance_date']) ?> - <?= htmlspecialchars($m['maintenance_type']) ?> (<?= htmlspecialchars($m['vehicle_year'] . ' ' . $m['vehicle_make'] . ' ' . $m['vehicle_model']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <?php if ($log_id && $maint_log_detail): ?>
            <div class="btn-group shadow-sm" role="group">
                <button type="button" class="btn btn-warning border-primary" data-bs-toggle="modal"
                    data-bs-target="#editMaintenanceModal" 
                    data-maint-id="<?= $maint_log_detail['maintenance_id'] ?>"
                    data-vehicle-id="<?= $maint_log_detail['vehicle_id'] ?>"
                    data-maint-type-id="<?= $maint_log_detail['maintenance_type_id'] ?>"
                    data-vendor-id="<?= $maint_log_detail['vendor_id'] ?? '' ?>"
                    data-maint-date="<?= $maint_log_detail['maintenance_date'] ?>"
                    data-maint-mileage="<?= $maint_log_detail['maintenance_mileage'] ?>"
                    data-maint-cost="<?= $maint_log_detail['maintenance_cost'] ?>"
                    data-maint-status="<?= htmlspecialchars($maint_log_detail['maintenance_status'] ?? '') ?>"
                    data-maint-desc="<?= htmlspecialchars($maint_log_detail['maintenance_description'] ?? '') ?>"
                    data-maint-notes="<?= htmlspecialchars($maint_log_detail['maintenance_notes'] ?? '') ?>"
                    title="Edit This Record">
                    <i class="fas fa-edit me-1"></i> Edit
                </button>
                <?php if (isset($allow_delete) && $allow_delete): ?>
                    <button type="button" class="btn btn-danger border-primary" data-bs-toggle="modal"
                        data-bs-target="#deleteMaintenanceModal" 
                        data-maint-id="<?= $maint_log_detail['maintenance_id'] ?>"
                        data-vehicle-name="<?= htmlspecialchars($maint_log_detail['vehicle_full']) ?>"
                        data-maint-type="<?= htmlspecialchars($maint_log_detail['type_name']) ?>"
                        title="Delete This Record">
                        <i class="fa-regular fa-trash-can me-1"></i> Delete
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!$log_id): ?>
    <div class="text-center py-5 border rounded bg-light shadow-sm">
        <i class="fa-solid fa-wrench fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No Maintenance Record Selected</h4>
        <p class="text-secondary">Please use the dropdown menu above to select a specific maintenance job and view its full details, cost, and vendor information.</p>
    </div>
<?php elseif (!$maint_log_detail): ?>
    <div class="alert alert-danger shadow-sm"><i class="fa-solid fa-triangle-exclamation me-2"></i>Maintenance record not found.</div>
<?php else: ?>
    <!-- Maintenance Detail Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white py-3">
            <span class="badge bg-light text-primary me-2"><i class="fa-solid fa-circle-info"></i></span>
            <h5 class="d-inline mb-0">Record #<?= $maint_log_detail['maintenance_id'] ?>: <?= htmlspecialchars($maint_log_detail['type_name']) ?></h5>
            <span class="badge bg-light text-primary ms-2"><?= htmlspecialchars($maint_log_detail['maintenance_date_formatted']) ?></span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <tr>
                        <th class="table-dark" style="width:20%">Vehicle</th>
                        <td colspan="3" class="fw-bold"><?= htmlspecialchars($maint_log_detail['vehicle_full']) ?></td>
                    </tr>
                    <tr>
                        <th class="table-dark" style="width:20%">Job Type</th>
                        <td style="width:30%"><?= htmlspecialchars($maint_log_detail['type_name']) ?> (<?= htmlspecialchars($maint_log_detail['maintenance_code']) ?>)</td>
                        <th class="table-dark" style="width:20%">Service Date</th>
                        <td><?= htmlspecialchars($maint_log_detail['maintenance_date_formatted']) ?></td>
                    </tr>
                    <tr>
                        <th class="table-dark">Service Mileage</th>
                        <td><?= number_format($maint_log_detail['maintenance_mileage']) ?> miles</td>
                        <th class="table-dark">Service Cost</th>
                        <td class="text-success fw-bold">$<?= number_format($maint_log_detail['maintenance_cost'], 2) ?></td>
                    </tr>
                    <tr>
                        <th class="table-dark">Status</th>
                        <td><span class="badge bg-primary px-3"><?= htmlspecialchars($maint_log_detail['maintenance_status']) ?></span></td>
                        <th class="table-dark">Vendor</th>
                        <td>
                            <?php if ($maint_log_detail['vendor_name']): ?>
                                <?= htmlspecialchars($maint_log_detail['vendor_name']) ?>
                                <?php if ($maint_log_detail['vendor_phone']): ?>
                                    <small class="text-muted ms-2">(<i class="fas fa-phone fa-xs"></i> <?= htmlspecialchars($maint_log_detail['vendor_phone']) ?>)</small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">None specified</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="table-dark">Description</th>
                        <td colspan="3"><?= nl2br(htmlspecialchars($maint_log_detail['maintenance_description'])) ?></td>
                    </tr>
                    <tr>
                        <th class="table-dark">Internal Notes</th>
                        <td colspan="3"><small class="text-muted"><?= nl2br(htmlspecialchars($maint_log_detail['maintenance_notes'] ?? 'No notes available.')) ?></small></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
