<?php
/*
    Jonathan Coblentz
    CPT283: PHP Programming
    Final Project: Vehicle Maintenance Log
    
    This project is a web-based application for logging and tracking vehicle maintenance and fuel records.
    It includes user authentication, CRUD operations for vehicles, services, maintenance records, and fuel records.
    
    Lab 01-02 focuses on Database Documentation, schema design, and sample queries.
*/

$title = 'Lab 01-02 | Database Documentation';
$showHero = true;
require_once 'includes/header_bundle.php';
include_once 'includes/functions.php';
?>

<div class="container mt-5">

    <!-- HEADER CARD -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
            <span class="badge bg-white text-primary me-2">Lab 01-02</span>
            <h5 class="d-inline mb-0">Database Documentation</h5>
        </div>
        <div class="card-body">
            <p class="lead mb-2">Vehicle Log Database &mdash; Schema &amp; Design</p>
            <p class="text-muted mb-0 small">
                Complete documentation of the <code>vehicle_log</code> database schema, including table structures, relationships, key design considerations, and sample queries for reporting.
            </p>
        </div>
    </div>

    <!-- CORE SCHEMA SECTION -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <h3 class="text-primary mb-3">Core Schema Elements</h3>
            <ul class="list-group list-group-flush mb-0">
                <li class="list-group-item"><strong>Vehicle Tracking:</strong> Manage purchase details, model info, and automatic mileage synchronization.</li>
                <li class="list-group-item"><strong>Fuel Logging:</strong> Record cost, volume, payment methods, and automated MPG calculations.</li>
                <li class="list-group-item"><strong>Service History:</strong> Track services with recommended intervals and scheduled statuses.</li>
                <li class="list-group-item"><strong>Access Control:</strong> Securely manage user roles and account assignments.</li>
            </ul>
        </div>
    </div>

    <!-- DESIGN CONSIDERATIONS SECTION -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <h3 class="text-primary mb-3">Key Design Considerations</h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <p class="small"><strong>Normalization:</strong> Strictly separated entities for users, vehicles, and logs to eliminate data redundancy.</p>
                    <p class="small"><strong>Relational Integrity:</strong> InnoDB foreign keys enforce <code>ON DELETE CASCADE</code> and <code>RESTRICT</code> rules appropriately.</p>
                </div>
                <div class="col-md-6">
                    <p class="small"><strong>Precision:</strong> Used <code>DECIMAL(10,2)</code> for all monetary and geometric values to prevent floating-point errors.</p>
                    <p class="small"><strong>Auditing:</strong> Automatic <code>_modified</code> timestamps for tracking database mutations.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SCHEMA DIAGRAM SECTION -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body text-center">
            <h3 class="text-primary mb-3 text-start">Relational Architecture</h3>
            <img src="img/schema.png" alt="Vehicle Log Schema Diagram" class="img-fluid border rounded shadow-sm my-3" style="max-height: 500px;">
            <p class="small text-muted mt-2">Enhanced ERD visualization highlighting user assignments and log-to-vehicle relationships.</p>
        </div>
    </div>

    <!-- TABLE DEFINITIONS TABBED/STACKED SECTION -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <h3 class="text-primary mb-3">Table Structures</h3>
            
            <?php 
                $tables = [
                    'users'            => ['user_id (PK)', 'first_name', 'last_name', 'email (U)', 'user_password', 'user_role', 'is_active', 'date_created'],
                    'vehicles'         => ['vehicle_id (PK)', 'vehicle_make', 'vehicle_model', 'vehicle_year', 'vehicle_VIN', 'assigned_user_id (FK)', 'current_mileage'],
                    'maintenance_type' => ['maintenance_id (PK)', 'maintenance_code', 'maintenance_type', 'recommended_interval_miles', 'recommended_interval_days'],
                    'maintenance'      => ['maintenance_id (PK)', 'vehicle_id (FK)', 'maintenance_type_id (FK)', 'maintenance_cost', 'maintenance_date', 'maintenance_status'],
                    'fuel'             => ['fuel_id (PK)', 'vehicle_id (FK)', 'fuel_date', 'fuel_gallons', 'fuel_cost_per_gallon', 'fuel_mileage', 'fuel_notes'],
                    'vendors'          => ['vendor_id (PK)', 'vendor_name', 'vendor_phone', 'vendor_email', 'vendor_address', 'is_active']
                ];

                foreach($tables as $name => $cols):
            ?>
                <div class="mb-3">
                    <h6 class="fw-bold mb-1"><i class="fa-solid fa-table text-primary me-2"></i><?= ucfirst(str_replace('_', ' ', $name)) ?> Table</h6>
                    <p class="small text-muted mb-2">Key columns: <?= implode(', ', $cols) ?>...</p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- SAMPLE QUERIES SECTION -->
    <div class="card shadow-sm mb-5 border-0">
        <div class="card-body">
            <h3 class="text-primary mb-3">Advanced Reporting Queries</h3>
            
            <div class="mb-4">
                <h6 class="fw-bold">1. Predictive Maintenance Calculation</h6>
                <pre class="bg-dark text-success p-3 rounded small"><code>SELECT v.vehicle_make, mt.maintenance_type, 
       m.maintenance_mileage + mt.recommended_interval_miles AS next_due_mileage
FROM maintenance m
JOIN maintenance_type mt ON m.maintenance_type_id = mt.maintenance_id
JOIN vehicles v ON m.vehicle_id = v.vehicle_id;</code></pre>
            </div>

            <div class="mb-0">
                <h6 class="fw-bold">2. MPG Performance Metric</h6>
                <pre class="bg-dark text-info p-3 rounded small"><code>SELECT v.vehicle_model, f.fuel_date,
       (f.fuel_mileage - LAG(f.fuel_mileage) OVER (PARTITION BY v.vehicle_id ORDER BY f.fuel_date)) 
       / f.fuel_gallons AS mpg
FROM fuel f JOIN vehicles v ON f.vehicle_id = v.vehicle_id;</code></pre>
            </div>
        </div>
    </div>

</div>

<?php include_once('../includes/footer.php'); ?>