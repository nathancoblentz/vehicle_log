<?php
/**
 * user_controller.php
 *
 * Handles Add, Update, and Delete user form submissions.
 * Note: passwords are hashed with bcrypt — never stored in plain text.
 */

global $db, $feedback;

require_once __DIR__ . '/../includes/validate.php';
require_once __DIR__ . '/../models/UserModel.php';

// ── Routing ────────────────────────────────────────────────────────────────────

if (isset($_POST['add_user'])) {
    saveUser($db, 'add');
} elseif (isset($_POST['update_user'])) {
    saveUser($db, 'update');
} elseif (isset($_POST['delete_user'])) {
    deleteUser($db);
} elseif (isset($_POST['deactivate_user'])) {
    deactivateUser($db);
}


// ── Helpers ────────────────────────────────────────────────────────────────────

function collectUserFields(): array
{
    return [
        'user_id'       => $_POST['user_id']       ?? '',
        'first_name'    => $_POST['first_name']    ?? '',
        'last_name'     => $_POST['last_name']     ?? '',
        'email'         => $_POST['email']         ?? '',
        'user_password' => $_POST['user_password'] ?? '',
        'user_role'     => $_POST['user_role']     ?? '',
        'is_active'     => $_POST['is_active']     ?? '1',
    ];
}

function isEmailTaken(PDO $db, string $email, int $excludeId = 0): bool
{
    return UserModel::isEmailTaken($db, $email, $excludeId);
}

function saveUser(PDO $db, string $mode): void
{
    global $feedback;

    $fields = collectUserFields();

    // Password is required on add, optional on update
    $toValidate = $fields;
    if ($mode === 'add') {
        $toValidate['_require_password'] = true;
        unset($toValidate['user_id'], $toValidate['is_active']);
    } else {
        unset($toValidate['_require_password']);
    }

    $errors = validateFields($toValidate);
    if (!empty($errors)) {
        $feedback = [
            'type'    => 'error',
            'title'   => 'Please fix the following errors',
            'message' => implode(' ', $errors),
        ];
        return;
    }

    $email  = strtolower(trim($fields['email']));
    $userId = (int) $fields['user_id'];

    if (isEmailTaken($db, $email, $mode === 'update' ? $userId : 0)) {
        $prefix = $mode === 'update' ? 'Another user' : 'A user';
        $feedback = [
            'type'    => 'error',
            'title'   => 'Duplicate Email',
            'message' => "$prefix with that email address already exists.",
        ];
        return;
    }

    $rawPassword = $fields['user_password'];
    $role        = $fields['user_role'] === 'admin' ? 'admin' : 'user';
    $name        = trim($fields['first_name']) . ' ' . trim($fields['last_name']);

    if ($mode === 'add') {
        $hashed = password_hash($rawPassword, PASSWORD_DEFAULT);
        UserModel::addUser($db, [
            trim($fields['first_name']),
            trim($fields['last_name']),
            $email,
            $hashed,
            $role,
        ]);
    } else {
        if (!empty($rawPassword)) {
            // New password provided — hash and update it
            $hashed = password_hash($rawPassword, PASSWORD_DEFAULT);
            UserModel::updateUserWithPassword($db, [
                trim($fields['first_name']),
                trim($fields['last_name']),
                $email,
                $role,
                (int) $fields['is_active'],
                $hashed,
                $userId,
            ]);
        } else {
            // No new password — leave existing hash alone
            UserModel::updateUserWithoutPassword($db, [
                trim($fields['first_name']),
                trim($fields['last_name']),
                $email,
                $role,
                (int) $fields['is_active'],
                $userId,
            ]);
        }
    }

    $action = $mode === 'add' ? 'created' : 'updated';
    $feedback = [
        'type'    => 'success',
        'title'   => 'User ' . ucfirst($action),
        'message' => "$name was $action successfully.",
    ];
}

function deleteUser(PDO $db): void
{
    global $feedback;

    $userId = (int) ($_POST['user_id'] ?? 0);
    if ($userId <= 0) {
        $feedback = ['type' => 'error', 'title' => 'Error', 'message' => 'Invalid user ID.'];
        return;
    }

    UserModel::deleteUser($db, $userId);

    $feedback = [
        'type'    => 'success',
        'title'   => 'User Deleted',
        'message' => 'User was deleted successfully.',
    ];
}

function deactivateUser(PDO $db): void
{
    global $feedback;

    $userId = (int) ($_POST['user_id'] ?? 0);
    if ($userId <= 0) {
        $feedback = ['type' => 'error', 'title' => 'Error', 'message' => 'No user ID provided.'];
        return;
    }

    // Prevent admins from deactivating their own account
    if (isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] === $userId) {
        $feedback = [
            'type'    => 'error',
            'title'   => 'Error',
            'message' => 'You cannot deactivate your own account.',
        ];
        return;
    }

    UserModel::deactivateUser($db, $userId);

    $feedback = [
        'type'    => 'success',
        'title'   => 'User Deactivated',
        'message' => 'The user account has been set to inactive. They will no longer be able to log in.',
    ];
}
