<?php
// edit_user_modal.php — Edit User form fields
// Boilerplate handled by renderModalStart() / renderModalEnd()
?>

<?php
renderModalStart('editUserModal', 'Edit User', 'editUserForm', 'update_user');
?>

<input type="hidden" name="user_id" id="edit_user_id" value="<?= htmlspecialchars($_POST['user_id'] ?? '') ?>">

<div class="row g-3">
    <?php
    $mode = 'edit';
    $prefix = 'edit_user_';
    include __DIR__ . '/partials/_user_form.php';
    ?>
</div>

<?php renderModalEnd('editUserForm', 'Save Changes'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('editUserModal');
        if (!modal) return;
        
        modal.addEventListener('show.bs.modal', function (e) {
            // If we are recovering from a failed POST, do NOT overwrite fields with original DB values.
            if (window.isErrorRecovery) {
                window.isErrorRecovery = false; // Reset for next time
                return;
            }

            const btn = e.relatedTarget;
            if (!btn) return;
            
            // Map data attributes to input IDs
            const mappings = {
                'user-id': 'edit_user_id',
                'first-name': 'edit_user_first_name',
                'last-name': 'edit_user_last_name',
                'email': 'edit_user_email',
                'user-role': 'edit_user_role'
            };

            for (const [attr, id] of Object.entries(mappings)) {
                const el = document.getElementById(id);
                if (el) {
                    el.value = btn.getAttribute('data-' + attr) || '';
                }
            }

            // Always clear password field on open
            const passwordField = document.getElementById('edit_user_password');
            if (passwordField) passwordField.value = '';

            // Handle checkbox for is_active
            const isActiveCheck = document.getElementById('edit_user_is_active');
            if (isActiveCheck) {
                isActiveCheck.checked = (btn.getAttribute('data-is-active') === '1');
            }
        });
    });
</script>