<?php
$title = 'Lab 03 | Adding New Records';
$showHero = true;
require_once 'includes/header_bundle.php';
?>
<!--
    Jonathan Coblentz
    CPT283: PHP Programming
    Final Project: Vehicle Maintenance Log
    
    This project is a web-based application for logging and tracking vehicle maintenance and fuel records.
    It includes user authentication, CRUD operations for vehicles, services, maintenance records, and fuel records.
    
    Lab 03 focuses on the "Create" (Add) functionality for Vehicles and Fuel records.
    -->

<div class="container mt-5">

    <!-- HEADER CARD -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
            <span class="badge bg-white text-primary me-2">Lab 03</span>
            <h5 class="d-inline mb-0">Adding New Records to the Database</h5>
        </div>
        <div class="card-body">
            <p class="lead mb-2">Expanding CRUD Capabilities &mdash; Fleet Management</p>
            <p class="text-muted mb-0 small">
                Implementation of the "Create" functionality, allowing users to seed the system with vehicle and fuel data. This version introduces Bootstrap modals for input, providing a clean, distraction-free data entry experience.
            </p>
        </div>
    </div>

    <!-- DATA FLOW SECTION -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <h3 class="text-primary mb-3">System Data Flow</h3>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0 small">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 20%;">Phase</th>
                            <th style="width: 50%;">Operation</th>
                            <th style="width: 30%;">Technical Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">1. Input</td>
                            <td>User triggers an "Add" modal via <code>data-bs-target</code>.</td>
                            <td>Bootstrap 5.3 Modals</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">2. Request</td>
                            <td>Form submits via <code>POST</code> back to the current view.</td>
                            <td>HTTP Form Handling</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">3. Orchestration</td>
                            <td><code>addHandlers()</code> identifies the action and requires the controller.</td>
                            <td>Controller Routing</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">4. Execution</td>
                            <td>The controller validates input and executes a PDO prepared statement.</td>
                            <td>Server-Side Logic</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">5. Feedback</td>
                            <td>An alert is set in session and displayed via the feedback modal.</td>
                            <td>Session Management</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- DASHBOARD PREVIEW SECTION -->
    <div class="card shadow-sm mb-5 border-0">
        <div class="card-body">
            <h3 class="text-primary mb-3">Active Dashboard Overview</h3>
            <p class="small text-muted mb-4">Click "Add Vehicle" or "Add Fuel" below to test the functional CRUD implementation for this milestone.</p>
            <!-- DASHBOARD TABS (Integrated from legacy dashboard.php) -->
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#vehicles">Vehicles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#fuel">Fuel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#maintenance">Maintenance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#maintenance_types">Services</a>
                </li>
            </ul>

            <div class="tab-content mt-3">
                <div class="tab-pane fade show active" id="vehicles">
                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                        Add Vehicle
                    </button>
                    <?php renderVehiclesTable($db); ?>
                </div>

                <div class="tab-pane fade" id="fuel">
                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addFuelModal">
                        Add Fuel
                    </button>
                    <?php renderFuelTable($db); ?>
                </div>

                <div class="tab-pane fade" id="maintenance">
                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addMaintenanceModal">
                        Add Maintenance Record
                    </button>
                    <?php renderMaintenanceTable($db); ?>
                </div>

                <div class="tab-pane fade" id="maintenance_types">
                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addMaintenanceTypeModal">
                        Add Service
                    </button>
                    <?php renderMaintenanceTypeTable($db); ?>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- MODAL OVERLAYS -->
<?php addForms(); ?>
<?php addFeedback(); ?>

<?php include_once('../includes/footer.php'); ?>
