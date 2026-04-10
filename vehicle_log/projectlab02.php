<?php
/*
    Jonathan Coblentz
    CPT283: PHP Programming
    Final Project: Vehicle Maintenance Log
    
    This project is a web-based application for logging and tracking vehicle maintenance and fuel records.
    It includes user authentication, CRUD operations for vehicles, services, maintenance records, and fuel records.
    
    Lab 02 focuses on Database Connection (PDO) and initial user querying.
*/

$title = 'Lab 02 | Database Connection & Users';
$showHero = true;
require_once 'includes/header_bundle.php';
include_once 'includes/functions.php';

// Fetch all users for demonstration
$query = 'SELECT * FROM users';
$statement = $db->prepare($query);
$statement->execute();
$users = $statement->fetchAll(PDO::FETCH_ASSOC);
$statement->closeCursor();

?>

<div class="container mt-5">

    <!-- HEADER CARD -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
            <span class="badge bg-white text-primary me-2">Lab 02</span>
            <h5 class="d-inline mb-0">Database Connection &amp; Users</h5>
        </div>
        <div class="card-body">
            <p class="lead mb-2">PDO Connection &mdash; Querying the Database</p>
            <p class="text-muted mb-0 small">
                Demonstrates connecting to the <code>vehicle_log</code> database using PDO and retrieving data from the <code>users</code> table. This establishes the foundational data access pattern for the entire application.
            </p>
        </div>
    </div>

    <!-- USERS DATA SECTION -->
    <div class="card shadow-sm mb-5 border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-primary mb-0">Registered System Users</h3>
                <span class="badge bg-primary rounded-pill"><?php echo count($users); ?> records found</span>
            </div>

            <?php if (empty($users)): ?>
                <div class="alert alert-warning border-0 shadow-sm">
                    <h4 class="alert-heading"><i class="fa-solid fa-triangle-exclamation me-2"></i>No data detected!</h4>
                    <p class="mb-0">Please ensure you have executed the <code>vehicle_log/data/populate.sql</code> script to seed the database with initial users.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email Address</th>
                                <th>System Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($user['user_id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                                    <td><code><?php echo htmlspecialchars($user['email']); ?></code></td>
                                    <td>
                                        <span class="badge <?= $user['user_role'] === 'admin' ? 'bg-danger' : 'bg-success' ?> small">
                                            <?php echo ucfirst(htmlspecialchars($user['user_role'])); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- TECHNICAL IMPACT SECTION -->
    <div class="card shadow-sm mb-5 border-0">
        <div class="card-body">
            <h3 class="text-primary mb-3">Database Implementation Detail</h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="fw-bold">PDO (PHP Data Objects)</h6>
                    <p class="small text-muted">Using the PDO driver ensures cross-database compatibility and provides localized protection against SQL injection through the use of prepared statements and parameter binding.</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">User Management</h6>
                    <p class="small text-muted">The <code>users</code> table serves as the primary authentication authority, storing hashed passwords and facilitating role-based access control (RBAC) across administrative and standard user interfaces.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include_once('../includes/footer.php'); ?>