<?php
// view/partials/_vehicle_info.php
// Expected variables: $vehicle, $fuel_logs, $maintenance_logs, $all_vehicles, $vehicle_id
?>

<!-- Back Button & Vehicle Selection Dropdown -->
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <form action="info.php" method="GET" class="shadow-sm">
            <input type="hidden" name="tab" value="vehicles">
            <div class="input-group">
                <span class="input-group-text bg-primary text-white border-primary"><i class="fa-solid fa-car"></i></span>
                <select name="vehicle_id" class="form-select border-primary" onchange="this.form.submit()">
                    <option value="">Select a Vehicle to report on...</option>
                    <?php foreach ($all_vehicles as $v): ?>
                        <option value="<?= htmlspecialchars($v['vehicle_id']) ?>" <?= ($v['vehicle_id'] == $vehicle_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(($v['vehicle_year'] ?? '') . ' ' . ($v['vehicle_make'] ?? '') . ' ' . ($v['vehicle_model'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <?php if ($vehicle_id && $vehicle): ?>
            <div class="btn-group shadow-sm" role="group">
                <button type="button" class="btn btn-warning border-primary" data-bs-toggle="modal"
                    data-bs-target="#editVehicleModal" data-vehicle-id="<?= $vehicle['vehicle_id'] ?>"
                    data-vehicle-type="<?= htmlspecialchars($vehicle['vehicle_type'] ?? '') ?>"
                    data-vehicle-make="<?= htmlspecialchars($vehicle['vehicle_make'] ?? '') ?>"
                    data-vehicle-model="<?= htmlspecialchars($vehicle['vehicle_model'] ?? '') ?>"
                    data-vehicle-year="<?= htmlspecialchars($vehicle['vehicle_year'] ?? '') ?>"
                    data-vehicle-year-purchased="<?= htmlspecialchars($vehicle['vehicle_year_purchased'] ?? '') ?>"
                    data-vehicle-color="<?= htmlspecialchars($vehicle['vehicle_color'] ?? '') ?>"
                    data-vehicle-vin="<?= htmlspecialchars($vehicle['vehicle_VIN'] ?? '') ?>"
                    data-vehicle-license-tag="<?= htmlspecialchars($vehicle['vehicle_license_tag'] ?? '') ?>"
                    data-vehicle-license-state="<?= htmlspecialchars($vehicle['vehicle_license_state'] ?? '') ?>"
                    data-vehicle-purchase-price="<?= htmlspecialchars((string) ($vehicle['vehicle_purchase_price'] ?? '')) ?>"
                    data-vehicle-purchase-mileage="<?= round((int) ($vehicle['vehicle_purchase_mileage'] ?? 0)) ?>"
                    data-vehicle-current-mileage="<?= round((int) ($vehicle['vehicle_current_mileage'] ?? 0)) ?>"
                    data-is-active="<?= $vehicle['is_active'] ?? 1 ?>" title="Edit This Vehicle">
                    <i class="fas fa-edit me-1"></i> Edit
                </button>
                <?php if (isset($allow_delete) && $allow_delete): ?>
                    <button type="button" class="btn btn-danger border-primary" data-bs-toggle="modal"
                        data-bs-target="#deleteVehicleModal" data-vehicle-id="<?= $vehicle['vehicle_id'] ?>"
                        data-vehicle-name="<?= htmlspecialchars(($vehicle['vehicle_year'] ?? '') . ' ' . ($vehicle['vehicle_make'] ?? '') . ' ' . ($vehicle['vehicle_model'] ?? '')) ?>"
                        data-maint-count="<?= count($maintenance_logs) ?>" data-fuel-count="<?= count($fuel_logs) ?>"
                        title="Delete This Vehicle">
                        <i class="fa-regular fa-trash-can me-1"></i> Delete
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!$vehicle_id): ?>
    <div class="text-center py-5 border rounded bg-light shadow-sm">
        <i class="fa-solid fa-car-side fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No Vehicle Selected</h4>
        <p class="text-secondary">Please use the dropdown menu above to select a vehicle and view its detailed maintenance and fuel history.</p>
    </div>
<?php elseif (!$vehicle): ?>
    <div class="alert alert-danger shadow-sm"><i class="fa-solid fa-triangle-exclamation me-2"></i>Vehicle not found.</div>
<?php else: ?>
    <!-- Horizontal Tabs -->
    <ul class="nav nav-tabs mb-4 border-primary" id="vehicleTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold px-4" id="v-details-tab" data-bs-toggle="tab" 
                    data-bs-target="#v-details" type="button" role="tab" aria-controls="v-details" aria-selected="true">
                <i class="fa-solid fa-circle-info me-2 text-primary"></i>Record Details
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4" id="v-history-tab" data-bs-toggle="tab" 
                    data-bs-target="#v-history" type="button" role="tab" aria-controls="v-history" aria-selected="false">
                <i class="fa-solid fa-chart-line me-2 text-primary"></i>History & Reports
            </button>
        </li>
    </ul>

    <div class="tab-content" id="vehicleTabsContent">
        <!-- Tab 1: DETAILS -->
        <div class="tab-pane fade show active" id="v-details" role="tabpanel" aria-labelledby="v-details-tab">
            <!-- Vehicle Details Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <span class="badge bg-light text-primary me-2"><i class="fa-solid fa-car"></i></span>
                    <h5 class="d-inline mb-0">
                        <?= htmlspecialchars(($vehicle['vehicle_year'] ?? '') . ' ' . ($vehicle['vehicle_make'] ?? '') . ' ' . ($vehicle['vehicle_model'] ?? '')) ?>
                    </h5>
                    <?php if (($vehicle['is_active'] ?? 1) == 0): ?>
                        <span class="badge bg-danger ms-2">Inactive</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <tr>
                                <th class="table-dark" style="width:15%">Make</th>
                                <td><?= htmlspecialchars($vehicle['vehicle_make'] ?? '') ?></td>
                                <th class="table-dark" style="width:15%">Model</th>
                                <td><?= htmlspecialchars($vehicle['vehicle_model'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th class="table-dark">Year</th>
                                <td><?= htmlspecialchars($vehicle['vehicle_year'] ?? '') ?></td>
                                <th class="table-dark">Color</th>
                                <td><?= htmlspecialchars($vehicle['vehicle_color'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th class="table-dark">Type</th>
                                <td><?= htmlspecialchars($vehicle['vehicle_type'] ?? '') ?></td>
                                <th class="table-dark">VIN</th>
                                <td><?= htmlspecialchars($vehicle['vehicle_VIN'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th class="table-dark">License Plate</th>
                                <td><?= htmlspecialchars($vehicle['vehicle_license_tag'] ?? '') ?></td>
                                <th class="table-dark">State</th>
                                <td><?= htmlspecialchars($vehicle['vehicle_license_state'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th class="table-dark">Purchase Price</th>
                                <td>$<?= number_format($vehicle['vehicle_purchase_price'] ?? 0, 2) ?></td>
                                <th class="table-dark">Year Purchased</th>
                                <td><?= htmlspecialchars($vehicle['vehicle_year_purchased'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th class="table-dark">Purchase Mileage</th>
                                <td><?= number_format((int)($vehicle['vehicle_purchase_mileage'] ?? 0)) ?></td>
                                <th class="table-dark">Current Mileage</th>
                                <td><?= number_format((int)($vehicle['vehicle_current_mileage'] ?? 0)) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: HISTORY/REPORTS -->
        <div class="tab-pane fade" id="v-history" role="tabpanel" aria-labelledby="v-history-tab">
            <!-- Fuel Logs Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <span class="badge bg-light text-info me-2"><i class="fa-solid fa-gas-pump"></i></span>
                    <h5 class="d-inline mb-0">Fuel Logs</h5>
                    <span class="badge bg-light text-info ms-2"><?= count($fuel_logs) ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($fuel_logs)): ?>
                        <div class="alert alert-secondary mb-0">No fuel records found for this vehicle.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered table-sm">
                                <thead class="table-primary text-white text-nowrap">
                                    <tr>
                                        <th>Date</th>
                                        <th>Source</th>
                                        <th>Gallons</th>
                                        <th>Cost/Gal</th>
                                        <th>Total Cost</th>
                                        <th>Mileage</th>
                                        <th class="d-none d-lg-table-cell">Payment</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="align-middle">
                                    <?php foreach ($fuel_logs as $f): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($f['fuel_date_formatted']) ?></td>
                                            <td><?= htmlspecialchars($f['fuel_source'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($f['fuel_gallons'] ?? '') ?></td>
                                            <td>$<?= number_format($f['fuel_cost_per_gallon'] ?? 0, 3) ?></td>
                                            <td>$<?= number_format($f['fuel_cost'] ?? 0, 2) ?></td>
                                            <td><?= number_format($f['fuel_mileage'] ?? 0) ?></td>
                                            <td class="d-none d-lg-table-cell"><?= htmlspecialchars($f['fuel_payment_method'] ?? '') ?></td>
                                            <td><small class="text-muted"><?= htmlspecialchars($f['fuel_notes'] ?? '') ?></small></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Maintenance Records Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <span class="badge bg-light text-primary me-2"><i class="fa-solid fa-wrench"></i></span>
                    <h5 class="d-inline mb-0">Maintenance Records</h5>
                    <span class="badge bg-light text-primary ms-2"><?= count($maintenance_logs) ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($maintenance_logs)): ?>
                        <div class="alert alert-secondary mb-0">No maintenance records found for this vehicle.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered table-sm text-center">
                                <thead class="table-primary text-white text-nowrap">
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th class="d-none d-md-table-cell">Vendor</th>
                                        <th>Description</th>
                                        <th>Cost</th>
                                        <th>Status</th>
                                        <th class="d-none d-lg-table-cell">Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="align-middle">
                                    <?php foreach ($maintenance_logs as $m): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($m['maintenance_date_formatted']) ?></td>
                                            <td><?= htmlspecialchars($m['type_name'] ?? '') ?></td>
                                            <td class="d-none d-md-table-cell"><?= htmlspecialchars($m['vendor_name'] ?? '') ?></td>
                                            <td><small><?= htmlspecialchars($m['maintenance_description'] ?? '') ?></small></td>
                                            <td class="text-nowrap">$<?= number_format($m['maintenance_cost'] ?? 0, 2) ?></td>
                                            <td><span class="badge bg-primary"><?= htmlspecialchars($m['maintenance_status'] ?? '') ?></span></td>
                                            <td class="d-none d-lg-table-cell"><small class="text-muted"><?= htmlspecialchars($m['maintenance_notes'] ?? '') ?></small></td>
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
