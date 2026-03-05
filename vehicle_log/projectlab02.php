<?php

require 'config.php';
$title = 'Lab 02 | Database Connection & Users';
include_once '../includes/head.php';
include_once '../includes/nav.php';
include_once '../includes/hero.php';
include_once 'includes/functions.php';


// Fetch all users
$query = 'SELECT * FROM users';
$statement = $db->prepare($query);
$statement->execute();
$users = $statement->fetchAll(PDO::FETCH_ASSOC);
$statement->closeCursor(); ?>

<div class="container mt-4">

    <!-- HEADER CARD -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
            <span class="badge bg-light text-primary me-2">Lab 02</span>
            <h5 class="d-inline mb-0">Database Connection &amp; Users</h5>
        </div>
        <div class="card-body">
            <p class="lead mb-2">PDO Connection &mdash; Querying the Database</p>
            <p class="text-muted mb-0">Demonstrates connecting to the <code>vehicle_log</code> database using PDO and
                retrieving data from the <code>users</code> table. The table below displays all registered users in the
                system.</p>
        </div>
    </div>

    <h2>Database Users (<?php echo count($users); ?> found)</h2>

    <?php if (empty($users)): ?>
        <div class="alert alert-warning">
            <h4>No users found!</h4>
            <p>Run the test data script first.</p>
        </div>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['user_role']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>


<?php include_once('../includes/footer.php'); ?>