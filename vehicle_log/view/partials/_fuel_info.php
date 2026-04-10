<?php
// view/partials/_fuel_info.php
// Expected variables: $fuel_entry_detail, $all_fuel_entries, $f_id
?>

<!-- Back Button & Selection Dropdown -->
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <form action="info.php" method="GET" class="shadow-sm">
            <input type="hidden" name="tab" value="fuel">
            <div class="input-group">
                <span class="input-group-text bg-primary text-white border-primary"><i class="fa-solid fa-gas-pump"></i></span>
                <select name="f_id" class="form-select border-primary" onchange="this.form.submit()">
                    <option value="">Select a specific Fuel Entry to report on...</option>
                    <?php foreach ($all_fuel_entries as $f): ?>
                        <option value="<?= htmlspecialchars($f['fuel_id']) ?>" <?= ($f['fuel_id'] == $f_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($f['fuel_date']) ?> - <?= htmlspecialchars($f['fuel_gallons']) ?> Gal (<?= htmlspecialchars($f['vehicle_year'] . ' ' . $f['vehicle_make'] . ' ' . $f['vehicle_model']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <?php if ($f_id && $fuel_entry_detail): ?>
            <div class="btn-group shadow-sm" role="group">
                <button type="button" class="btn btn-warning border-primary" data-bs-toggle="modal"
                    data-bs-target="#editFuelModal" 
                    data-fuel-id="<?= $fuel_entry_detail['fuel_id'] ?>"
                    data-vehicle-id="<?= $fuel_entry_detail['vehicle_id'] ?>"
                    data-fuel-date="<?= $fuel_entry_detail['fuel_date'] ?>"
                    data-fuel-gallons="<?= $fuel_entry_detail['fuel_gallons'] ?>"
                    data-fuel-cost="<?= $fuel_entry_detail['fuel_cost_per_gallon'] ?>"
                    data-fuel-mileage="<?= $fuel_entry_detail['fuel_mileage'] ?>"
                    data-fuel-source="<?= htmlspecialchars($fuel_entry_detail['fuel_source'] ?? '') ?>"
                    data-payment-method="<?= htmlspecialchars($fuel_entry_detail['fuel_payment_method'] ?? '') ?>"
                    data-fuel-notes="<?= htmlspecialchars($fuel_entry_detail['fuel_notes'] ?? '') ?>"
                    title="Edit This Record">
                    <i class="fas fa-edit me-1"></i> Edit
                </button>
                <?php if (isset($allow_delete) && $allow_delete): ?>
                    <button type="button" class="btn btn-danger border-primary" data-bs-toggle="modal"
                        data-bs-target="#deleteFuelModal" 
                        data-fuel-id="<?= $fuel_entry_detail['fuel_id'] ?>"
                        data-vehicle-name="<?= htmlspecialchars($fuel_entry_detail['vehicle_full']) ?>"
                        data-fuel-date="<?= htmlspecialchars($fuel_entry_detail['fuel_date']) ?>"
                        title="Delete This Record">
                        <i class="fa-regular fa-trash-can me-1"></i> Delete
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!$f_id): ?>
    <div class="text-center py-5 border rounded bg-light shadow-sm">
        <i class="fa-solid fa-gas-pump fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No Fuel Entry Selected</h4>
        <p class="text-secondary">Please use the dropdown menu above to select a specific fuel up and view its full cost analysis, mileage, and payment details.</p>
    </div>
<?php elseif (!$fuel_entry_detail): ?>
    <div class="alert alert-danger shadow-sm"><i class="fa-solid fa-triangle-exclamation me-2"></i>Fuel entry not found.</div>
<?php else: ?>
    <!-- Fuel Detail Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-info text-white py-3">
            <span class="badge bg-light text-info me-2"><i class="fa-solid fa-circle-info"></i></span>
            <h5 class="d-inline mb-0">Fuel Entry #<?= $fuel_entry_detail['fuel_id'] ?>: <?= htmlspecialchars($fuel_entry_detail['vehicle_full']) ?></h5>
            <span class="badge bg-light text-info ms-2"><?= htmlspecialchars($fuel_entry_detail['fuel_date_formatted']) ?></span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <tr>
                        <th class="table-dark" style="width:20%">Vehicle</th>
                        <td colspan="3" class="fw-bold"><?= htmlspecialchars($fuel_entry_detail['vehicle_full']) ?></td>
                    </tr>
                    <tr>
                        <th class="table-dark" style="width:20%">Fueling Date</th>
                        <td style="width:30%"><?= htmlspecialchars($fuel_entry_detail['fuel_date_formatted']) ?></td>
                        <th class="table-dark" style="width:20%">Mileage at Pump</th>
                        <td><?= number_format($fuel_entry_detail['fuel_mileage']) ?> miles</td>
                    </tr>
                    <tr>
                        <th class="table-dark">Gallons Added</th>
                        <td><?= htmlspecialchars($fuel_entry_detail['fuel_gallons']) ?> Gal</td>
                        <th class="table-dark">Cost per Gallon</th>
                        <td>$<?= number_format($fuel_entry_detail['fuel_cost_per_gallon'], 3) ?></td>
                    </tr>
                    <tr>
                        <th class="table-dark">Total Cost</th>
                        <td class="text-success fw-bold h5 mb-0">$<?= number_format($fuel_entry_detail['fuel_total'], 2) ?></td>
                        <th class="table-dark">Source/Station</th>
                        <td><?= htmlspecialchars($fuel_entry_detail['fuel_source'] ?? 'Not specified') ?></td>
                    </tr>
                    <tr>
                        <th class="table-dark">Payment Method</th>
                        <td><?= htmlspecialchars($fuel_entry_detail['fuel_payment_method'] ?? 'Not specified') ?></td>
                        <th class="table-dark">Reference #</th>
                        <td><small class="text-muted">Record ID: <?= $fuel_entry_detail['fuel_id'] ?></small></td>
                    </tr>
                    <tr>
                        <th class="table-dark">Internal Notes</th>
                        <td colspan="3"><small class="text-muted"><?= nl2br(htmlspecialchars($fuel_entry_detail['fuel_notes'] ?? 'No notes available.')) ?></small></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Stats Callout -->
    <div class="row">
        <div class="col-md-6">
            <div class="card bg-light border-0 shadow-sm text-center p-3">
                <h6 class="text-muted text-uppercase small mb-2">Cost Analysis</h6>
                <div class="d-flex justify-content-around">
                    <div>
                        <div class="h4 mb-0"><?= htmlspecialchars($fuel_entry_detail['fuel_gallons']) ?></div>
                        <div class="small text-muted">Gallons</div>
                    </div>
                    <div class="border-start"></div>
                    <div>
                        <div class="h4 mb-0">$<?= number_format($fuel_entry_detail['fuel_total'], 2) ?></div>
                        <div class="small text-muted">Total Spent</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-light border-0 shadow-sm text-center p-3 mt-3 mt-md-0">
                <h6 class="text-muted text-uppercase small mb-2">Vehicle Status</h6>
                <div class="h4 mb-0"><?= number_format($fuel_entry_detail['fuel_mileage']) ?></div>
                <div class="small text-muted">Odometer Reading</div>
            </div>
        </div>
    </div>
<?php endif; ?>
