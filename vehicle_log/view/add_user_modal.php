<?php
// add_user_modal.php — Add User form fields
// Boilerplate handled by renderModalStart() / renderModalEnd()?>

<?php
renderModalStart('addUserModal', 'Add New User', 'addUserForm', 'add_user');
$mode = 'add';
$prefix = 'add_user_';
?>
<div class="row g-3">
    <?php include __DIR__ . '/partials/_user_form.php'; ?>
</div>
<?php renderModalEnd('addUserForm', 'Create User'); ?>