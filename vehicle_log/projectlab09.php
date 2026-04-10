<?php
/*
    Jonathan Coblentz
    CPT283: PHP Programming
    Final Project: Vehicle Maintenance Log
    
    This project is a web-based application for logging and tracking vehicle maintenance and fuel records.
    It includes user authentication, CRUD operations for vehicles, services, maintenance records, and fuel records.
    
    Lab 09 focuses on Form Validation, "Sticky Forms" (data preservation), 
    and the Modal Error Recovery system.
*/

$title = 'Lab 09 | Form Validation & Modal Recovery';
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
            <span class="badge bg-white text-primary me-2">Lab 09</span>
            <h5 class="d-inline mb-0">Form Validation & Modal Recovery System</h5>
        </div>
        <div class="card-body">
            <p class="lead mb-2">Advanced Server-Side Validation &mdash; Data Integrity Safeguards</p>
            <p class="text-muted lead mb-0 fw-normal">
                Strategic UX Impact: By combining strict server-side enforcement with instant restoration, we've created a system that maintains high data quality while respecting the user's time and effort.
            </p>
            <p class="text-muted mb-0 mt-3 lead">
                Implementation of a sophisticated server-side validation architecture and a seamless error recovery workflow. By utilizing a centralized validation library (<code>validate.php</code>), the application ensures data integrity across all 50+ form fields.
            </p>
        </div>
    </div>

    <!-- CORE COMPONENTS SECTION -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h3 class="text-primary mt-0 mb-3">Core Monitoring Components</h3>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover mt-2 small">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" style="width: 25%;">Component</th>
                            <th scope="col">Description</th>
                            <th scope="col" style="width: 25%;">Key User Benefit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Centralized Validation</strong></td>
                            <td>The <code>validate.php</code> library houses all field-level rules and cross-field logic (like verifying odometer trends), following the DRY principle.</td>
                            <td class="text-success"><i class="fa-solid fa-shield-check me-2"></i>Global Consistency</td>
                        </tr>
                        <tr>
                            <td><strong>Sticky Forms</strong></td>
                            <td>Using the <code>getStickyVal()</code> helper, all form fields now preserve user input after a failed submission, preventing frustrating data loss.</td>
                            <td class="text-primary"><i class="fa-solid fa-floppy-disk me-2"></i>Eliminate Re-typing</td>
                        </tr>
                        <tr>
                            <td><strong>Modal Error Recovery</strong></td>
                            <td>If an error occurs, the feedback modal generates a "Continue Editing" button that intelligently re-opens the specific form that failed.</td>
                            <td class="text-warning"><i class="fa-solid fa-rotate-left me-2"></i>Seamless Correction</td>
                        </tr>
                        <tr>
                            <td><strong>State Management</strong></td>
                            <td>A JavaScript-driven recovery flag (<code>window.isErrorRecovery</code>) ensures that Edit modals respect the user's corrected input.</td>
                            <td class="text-info"><i class="fa-solid fa-screwdriver-wrench me-2"></i>Failsafe Persistence</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- IMPLEMENTATION MATRIX SECTION -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body">
            <h3 class="text-primary mb-3">Technical Implementation Matrix</h3>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover mt-2 small">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 30%;">File Architecture</th>
                            <th>Responsibility / Action Point</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>includes/validate.php</code></td>
                            <td>The "Source of Truth" for all field-level rules and cross-field comparisons across 50+ inputs.</td>
                        </tr>
                        <tr>
                            <td><code>includes/functions.php</code></td>
                            <td>Hosts <code>getStickyVal()</code> and <code>renderFeedbackModal()</code> core recovery logic.</td>
                        </tr>
                        <tr>
                            <td><code>controller/*.php</code></td>
                            <td>Orchestrates the data flow: Maps <code>POST</code> data, triggers validation, and manages feedback triggers.</td>
                        </tr>
                        <tr>
                            <td><code>view/partials/</code></td>
                            <td>All 6 core form partials updated with sticky value attributes for instant input restoration.</td>
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
