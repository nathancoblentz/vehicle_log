<?php
/*
    Jonathan Coblentz
    CPT283: PHP Programming
    Final Project: Vehicle Maintenance Log
    
    This project is a web-based application for logging and tracking vehicle maintenance and fuel records.
    It includes user authentication, CRUD operations for vehicles, services, maintenance records, and fuel records.
    
    Lab 06 focuses on Universal Database Access and Soft-Deletion (Deactivation) logic.
*/

$title = 'Lab 06 | Universal Database Interfaces';
$showHero = true;
$requireAuth = true;
require_once 'includes/header_bundle.php';
include_once 'includes/functions.php';

$feedback = null;

// PROCESS FORM SUBMISSIONS
addHandlers();
?>

<div class="container mt-5">

    <!-- HEADER CARD -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
            <span class="badge bg-white text-primary me-2">Lab 06</span>
            <h5 class="d-inline mb-0">Interfaces to All Database Tables</h5>
        </div>
        <div class="card-body">
            <p class="lead mb-2">Universal CRUD Access &mdash; Consistent Management Flow</p>
            <p class="text-muted mb-0 small">
                Finalizing the administrative interface for all core database tables: Vehicles, Maintenance, Fuel, Logs, Vendors, and Users. This milestone introduces "Soft-Delete" (Deactivation) logic to preserve referential integrity while allowing users to hide legacy records.
            </p>
        </div>
    </div>

    <!-- CORE FEATURES SECTION -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h3 class="text-primary mb-3">System-Wide Capabilities</h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="p-3 border rounded h-100 shadow-sm transition-hover bg-white">
                        <h6 class="fw-bold text-primary"><i class="fa-solid fa-lock-open me-2"></i>Full CRUD Authority</h6>
                        <p class="small text-muted mb-0">Every core table now supports authorized addition, deletion, and real-time updates.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 border rounded h-100 shadow-sm transition-hover bg-white">
                        <h6 class="fw-bold text-primary"><i class="fa-solid fa-eye-slash me-2"></i>Deactivation Logic</h6>
                        <p class="small text-muted mb-0">Instead of hard-deletes, referenced records (Vehicles, Vendors) can be set to 'Inactive' via a toggle.</p>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <p class="small text-muted border-start border-primary border-3 ps-3">
                        <strong>DRY Refactor:</strong> The <code>table.php</code> core has been reorganized into reusable rendering functions, significantly reducing code duplication across different database entities.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ACCESS SECTION -->
    <div class="card shadow-sm border-0 mb-5 text-center py-4">
        <div class="card-body">
            <h3 class="text-primary mb-3 text-start">Access the Centralized Interface</h3>
            <a href="table.php" class="btn btn-primary btn-lg shadow-sm">
                <i class="fa-solid fa-table-list me-2"></i>Launch Central Table View
            </a>
            <p class="small text-muted mt-3 mb-0">Navigate between entities using the primary vertical navigation pill-set.</p>
        </div>
    </div>

</div>

<!-- OVERLAY COMPONENTS -->
<?php addForms(); ?>
<?php addFeedback(); ?>

<?php include_once('../includes/footer.php'); ?>