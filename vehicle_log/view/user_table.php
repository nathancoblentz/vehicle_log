<!-- HEADER CARD -->
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="d-inline mb-0"><span class="fas fa-users me-3"></span>Users</h3>
        </div>
        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal" title="Add User">
            <i class="fa-solid fa-plus text-white"></i>
        </button>
    </div>
    <div class="card-body">
        <p class="lead mb-0">
            Manage system users and their access roles.
        </p>
    </div>
</div>

<?php
require_once __DIR__ . '/../models/UserModel.php';
$users = UserModel::getAllUsers($db);

if (empty($users)) {
    echo '<div class="alert alert-info">No users found.</div>';
} else {
    echo '<h5 class="mb-3">' . count($users) . ' user(s) total</h5>';
    ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Date Created</th>
                    <th>Last Login</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?>
                        </td>
                        <td>
                            <a href="mailto:<?= htmlspecialchars($u['email'] ?? '') ?>">
                                <?= htmlspecialchars($u['email'] ?? '') ?>
                            </a>
                        </td>
                        <td>
                            <?php if (($u['user_role'] ?? '') === 'admin'): ?>
                                <span class="badge bg-danger"><i class="fa-solid fa-shield-halved me-1"></i> Admin</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><i class="fa-solid fa-user me-1"></i> User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (($u['is_active'] ?? 1) == 1): ?>
                                <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><i class="fa-solid fa-ban me-1"></i>Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $u['date_created'] ? date('M j, Y', strtotime($u['date_created'])) : '—' ?>
                        </td>
                        <td>
                            <?= $u['date_lastlogin'] ? date('M j, Y g:i A', strtotime($u['date_lastlogin'])) : 'Never' ?>
                        </td>
                        <td class="text-center text-nowrap">
                            <!-- Edit Button -->
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal"
                                data-user-id="<?= $u['user_id'] ?>"
                                data-first-name="<?= htmlspecialchars($u['first_name'] ?? '') ?>"
                                data-last-name="<?= htmlspecialchars($u['last_name'] ?? '') ?>"
                                data-email="<?= htmlspecialchars($u['email'] ?? '') ?>"
                                data-user-role="<?= htmlspecialchars($u['user_role'] ?? 'user') ?>"
                                data-is-active="<?= $u['is_active'] ?? 1 ?>" title="Edit User">
                                <span class="fas fa-edit"></span>
                            </button>

                            <!-- Delete Button -->
                            <!-- Prevent the user from deleting themselves (handled in controller, but modal still shown) -->
                            <?php if (isset($allow_delete) && $allow_delete && $_SESSION['is_admin']): ?>
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                                    data-user-id="<?= $u['user_id'] ?>"
                                    data-user-name="<?= htmlspecialchars(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?>"
                                    title="Delete User">
                                    <span class="fa-regular fa-trash-can"></span>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php
} // end results else
?>