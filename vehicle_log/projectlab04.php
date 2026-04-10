<?php
$title = 'Lab 04 | Searching, Editing & Deleting';
$showHero = true;
require_once 'includes/header_bundle.php';
?>
<!--
    Jonathan Coblentz
    CPT283: PHP Programming
    Final Project: Vehicle Maintenance Log
    
    This project is a web-based application for logging and tracking vehicle maintenance and fuel records.
    It includes user authentication, CRUD operations for vehicles, services, maintenance records, and fuel records.
    
    Lab 04 focuses on Searching, Editing, and Deleting (Deactivation) records in the database.
    -->

<div class="container mt-5 text-dark">

    <!-- HEADER CARD -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
            <span class="badge bg-white text-primary me-2">Lab 04</span>
            <h5 class="d-inline mb-0">Searching, Editing & Deleting Database Records</h5>
        </div>
        <div class="card-body">
            <p class="lead mb-2">Expansion into Full CRUD &mdash; Data Integrity Management</p>
            <p class="text-muted mb-0 small">
                Building on Lab 03, this milestone introduces the ability to <strong>Search</strong> filtered results, <strong>Edit</strong> existing records via modals, and safely <strong>Delete</strong> or <strong>Deactivate</strong> data points. This implementation prioritizes referential integrity through confirmation prompts and dependency checks.
            </p>
        </div>
    </div>

    <!-- CORE INTERFACES SECTION -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h3 class="text-primary mb-3">Refactored Edit Interfaces</h3>
            <div class="row g-4">
                <?php 
                    $interfaces = [
                        ['Edit Vehicle', 'edit_vehicle.php', 'fa-car'],
                        ['Edit Maintenance', 'edit_maintenance.php', 'fa-wrench'],
                        ['Edit Fuel Log', 'edit_fuel.php', 'fa-gas-pump'],
                        ['Edit Maint. Type', 'edit_service.php', 'fa-list-check'],
                        ['Edit Vendors', 'edit_vendors.php', 'fa-shop']
                    ];

                    foreach($interfaces as $item):
                ?>
                    <div class="col-md-6 col-lg-4">
                        <a href="<?= $item[1] ?>" class="card h-100 border transition-hover text-decoration-none bg-white">
                            <div class="card-body d-flex align-items-center">
                                <span class="bg-primary bg-opacity-10 text-primary p-3 rounded me-3">
                                    <i class="fa-solid <?= $item[2] ?> fa-lg"></i>
                                </span>
                                <div class="text-dark">
                                    <h6 class="mb-0 fw-bold"><?= $item[0] ?></h6>
                                    <p class="small text-muted mb-0">Interface Detail &mdash;</p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- TECHNICAL OPERATIONS SECTION -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body">
            <h3 class="text-primary mb-3">Technical Operations Detail</h3>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover small">
                    <thead class="table-dark">
                        <tr>
                            <th>Operation</th>
                            <th>Implementation Method</th>
                            <th>Business Logic</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">Search & Filter</td>
                            <td>SQL <code>LIKE</code> operators with wildcard <code>%</code> characters.</td>
                            <td>Allows users to filter large datasets by any relevant keyword.</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Inline Editing</td>
                            <td>Bootstrap modals pre-populated with original record data.</td>
                            <td>Avoids page jumping and maintains current table context.</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Controlled Deletion</td>
                            <td>Referential integrity checks with JavaScript confirmation.</td>
                            <td>Ensures no orphaned records if data is referenced as a Foreign Key.</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">System Status</td>
                            <td>Boolean <code>is_active</code> toggling for critical entities.</td>
                            <td>Allows data persistence without cluttering active reporting views.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- OVERLAY COMPONENTS -->
<?php addForms(); ?>
<?php addFeedback(); ?>

<?php include_once('../includes/footer.php'); ?>