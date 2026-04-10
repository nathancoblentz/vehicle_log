<?php
/*
    Jonathan Coblentz
    CPT283: PHP Programming
    Final Project: Vehicle Maintenance Log
    
    This project is a web-based application for logging and tracking vehicle maintenance and fuel records.
    It includes user authentication, CRUD operations for vehicles, services, maintenance records, and fuel records.
    
    Lab 08 focuses on the Administrative Security Layer and Password Protection.
*/

$title = 'Lab 08 | Admin Security & Password Protection';
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
            <span class="badge bg-white text-primary me-2">Lab 08</span>
            <h5 class="d-inline mb-0">Admin Security & Password Protected Directory</h5>
        </div>
        <div class="card-body">
            <p class="lead mb-2">Restricting Administrative Access &mdash; Security Best Practices</p>
            <p class="text-muted mb-0 small">
                Implementation of a password-protected directory for administrative features. This milestone focuses on securing critical fleet management tools from unauthorized access using session-based authentication and role enforcement logic.
            </p>
        </div>
    </div>

    <!-- SECURITY COMPONENTS SECTION -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h3 class="text-primary mb-3">Core Security Features</h3>
            <div class="row g-4 mb-4">
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 border rounded h-100 shadow-sm transition-hover bg-white text-center">
                        <i class="fa-solid fa-folder-lock text-primary fa-2x mb-3"></i>
                        <h6 class="fw-bold">Directory Lockdown</h6>
                        <p class="small text-muted mb-0">The <code>/admin/</code> folder is restricted via server-side session checks.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 border rounded h-100 shadow-sm transition-hover bg-white text-center">
                        <i class="fa-solid fa-user-shield text-success fa-2x mb-3"></i>
                        <h6 class="fw-bold">Role Enforcement</h6>
                        <p class="small text-muted mb-0">Verification logic ensures only users with the 'Admin' role can access these views.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mx-md-auto">
                    <div class="p-3 border rounded h-100 shadow-sm transition-hover bg-white text-center">
                        <i class="fa-solid fa-key text-warning fa-2x mb-3"></i>
                        <h6 class="fw-bold">Authentication Guards</h6>
                        <p class="small text-muted mb-0">Redirect logic prevents deep-linking to admin views without a valid session.</p>
                    </div>
                </div>
            </div>

            <p class="small text-muted border-start border-primary border-3 ps-3">
                <strong>Strategic Impact:</strong> Protecting PII (Personally Identifiable Information) and financial records is a core requirement for any enterprise fleet management system.
            </p>
        </div>
    </div>

    <!-- ACCESS SECTION -->
    <div class="card shadow-sm border-0 mb-5 text-center py-4 bg-white">
        <div class="card-body">
            <h3 class="text-primary mb-3 text-start">Administrative Access Point</h3>
            <a href="admin/index.php" class="btn btn-outline-primary btn-lg shadow-sm border-2">
                <i class="fa-solid fa-shield-halved me-2"></i>Enter Secure Admin Dashboard
            </a>
            <p class="small text-muted mt-3 mb-0">Note: You must be logged in as an administrator to enter this directory.</p>
        </div>
    </div>

</div>

<!-- OVERLAY COMPONENTS -->
<?php addForms(); ?>
<?php addFeedback(); ?>

<?php include_once('../includes/footer.php'); ?>