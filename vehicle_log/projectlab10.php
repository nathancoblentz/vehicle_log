<?php
/**
 * Jonathan Coblentz
 * CPT283: PHP Programming
 * Final Project: Vehicle Maintenance Log
 * 
 * Lab 10: User Roles & Privileges
 */

$title = 'Lab 10 | User Roles & Privileges';
$showHero = true;
$requireAuth = true;
require_once 'includes/header_bundle.php';
include_once 'includes/functions.php';
?>

<div class="container mt-5">

    <!-- HEADER CARD -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
            <span class="badge bg-white text-primary me-2">Lab 10</span>
            <h5 class="d-inline mb-0">User Roles & Privileges</h5>
        </div>
        <div class="card-body">
            <p class="lead mb-2">Security Framework & Access Control</p>
            <p class="text-muted mb-0 small">
                This document outlines the security architecture of the Vehicle Maintenance Log, specifically focusing on the differentiation between administrative and standard user accounts.
            </p>
        </div>
    </div>

    <!-- ROLES OVERVIEW -->
    <div class="row">
        <!-- ADMIN ROLE -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-0 border-top border-primary border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="bg-primary text-white p-3 rounded-circle me-3">
                            <i class="fa-solid fa-user-shield fa-xl"></i>
                        </span>
                        <h3 class="card-title mb-0">Administrator</h3>
                    </div>
                    <p class="text-muted">The highest level of access, designed for system managers and fleet supervisors.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i> <strong>Full CRUD:</strong> Create, Read, Update, and Delete all records.</li>
                        <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i> <strong>Admin Dashboard:</strong> Exclusive access to <code>admin/index.php</code>.</li>
                        <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i> <strong>User Management:</strong> Ability to view and manage user accounts.</li>
                        <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i> <strong>Hard Delete:</strong> Exclusive privilege to permanently remove records.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- USER ROLE -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-0 border-top border-info border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="bg-info text-white p-3 rounded-circle me-3">
                            <i class="fa-solid fa-user fa-xl"></i>
                        </span>
                        <h3 class="card-title mb-0">Standard User</h3>
                    </div>
                    <p class="text-muted">General access level for staff entering vehicle and maintenance data.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i> <strong>Data Entry:</strong> Create, Read, and Update logs and vehicles.</li>
                        <li class="list-group-item"><i class="fa-solid fa-xmark text-danger me-2"></i> <strong>No Deletion:</strong> Cannot remove any records from the database.</li>
                        <li class="list-group-item"><i class="fa-solid fa-xmark text-danger me-2"></i> <strong>No User Management:</strong> Cannot see or edit other users.</li>
                        <li class="list-group-item"><i class="fa-solid fa-xmark text-danger me-2"></i> <strong>Restricted Navigation:</strong> Hides administrative tabs and links.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- DATA PERSISTENCE & STATUS -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <h3 class="text-primary mb-4"><i class="fa-solid fa-clock-rotate-left me-2"></i>Data Persistence: Active vs. Inactive</h3>
            <div class="row">
                <div class="col-md-6">
                    <h5>The "Soft" Deactivation Approach</h5>
                    <p class="text-muted small">
                        To maintain accurate historical reporting (such as total lifetime maintenance costs for a fleet), the system prioritizes <strong>deactivation</strong> over deletion. 
                    </p>
                    <p class="text-muted small">
                        When a vehicle is sold or a vendor is no longer used, marking them as <strong>Inactive</strong> hides them from daily operations while preserving their data for financial and historical audits.
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="bg-light p-3 rounded border">
                        <h6 class="fw-bold"><i class="fa-solid fa-toggle-on text-primary me-2"></i>Status Behavior</h6>
                        <ul class="small mb-0">
                            <li><strong>Active:</strong> Visible in all dropdowns and default search results.</li>
                            <li><strong>Inactive:</strong> Hidden from defaults but retrievable via the "Show Inactive" toggle.</li>
                            <li><strong>Reporting:</strong> Included in aggregated total cost calculations.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CONSTRAINTS & DELETION -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <h3 class="text-primary mb-4"><i class="fa-solid fa-trash-can me-2"></i>Relational Constraints & Deletion</h3>
            <div class="row">
                <div class="col-12">
                    <p class="text-muted small">
                        The application enforces <strong>Referential Integrity</strong> to prevent "orphaned" records. This means the database is protected from accidental data corruption through strict pre-deletion checks.
                    </p>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-3 border rounded h-100">
                        <h6><i class="fa-solid fa-link text-warning me-2"></i>Hierarchy</h6>
                        <p class="small text-muted mb-0">Records with dependencies (like a Vehicle with 10 logs) are locked. These logs must be removed or moved before the parent can be deleted.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-3 border rounded h-100">
                        <h6><i class="fa-solid fa-shield-halved text-success me-2"></i>Pre-Flight Checks</h6>
                        <p class="small text-muted mb-0">Logic in the <code>VehicleModel</code> and <code>VendorModel</code> counts related records. If the count is > 0, the delete action is aborted.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-3 border rounded h-100">
                        <h6><i class="fa-solid fa-user-lock text-danger me-2"></i>Hard Delete</h6>
                        <p class="small text-muted mb-0">Only the <strong>Administrator</strong> can access the "Hard Delete" function, which permanently removes the record from the MySQL database.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECURITY IMPLEMENTATION -->
    <div class="card shadow-sm mb-5 border-0">
        <div class="card-body">
            <h3 class="text-primary mb-4"><i class="fa-solid fa-lock me-2"></i>Implementation Logic</h3>
            <div class="row">
                <div class="col-md-4">
                    <h5>Gatekeepers</h5>
                    <p class="small text-muted">Functions like <code>requireAdmin()</code> and <code>requireLogin()</code> are used at the top of every script to enforce session-based security before any HTML is rendered.</p>
                </div>
                <div class="col-md-4">
                    <h5>Dynamic UI</h5>
                    <p class="small text-muted">The <code>$allow_delete</code> flag and <code>unset($tabs['users'])</code> logic dynamically alter the interface based on the logged-in user's role.</p>
                </div>
                <div class="col-md-4">
                    <h5>Server-Side Checks</h5>
                    <p class="small text-muted">Beyond hiding buttons, the actual data-processing handlers check <code>$_SESSION['is_admin']</code> before executing sensitive SQL queries like <code>DELETE</code>.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include_once('../includes/footer.php'); ?>
