<?php
/**
 * _user_form.php - Shared fields for Add/Edit User modals
 * 
 * Expected variables:
 * @var string $mode   'add' or 'edit'
 * @var string $prefix 'add_user_' or 'edit_user_'
 */
?>

<!-- First Name -->
<div class="col-md-6">
    <label for="<?= $prefix ?>first_name" class="form-label text-primary fw-bold">First Name <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-address-card"></i></span>
        <input type="text" class="form-control" id="<?= $prefix ?>first_name" name="first_name" 
               value="<?= htmlspecialchars(getStickyVal(['add_user', 'update_user'], 'first_name')) ?>" required>
    </div>
</div>

<!-- Last Name -->
<div class="col-md-6">
    <label for="<?= $prefix ?>last_name" class="form-label text-primary fw-bold">Last Name <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-regular fa-address-card"></i></span>
        <input type="text" class="form-control" id="<?= $prefix ?>last_name" name="last_name" 
               value="<?= htmlspecialchars(getStickyVal(['add_user', 'update_user'], 'last_name')) ?>" required>
    </div>
</div>

<!-- Email -->
<div class="col-md-6">
    <label for="<?= $prefix ?>email" class="form-label text-primary fw-bold">Email Address <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
        <input type="email" class="form-control" id="<?= $prefix ?>email" name="email" 
               value="<?= htmlspecialchars(getStickyVal(['add_user', 'update_user'], 'email')) ?>" required>
    </div>
</div>

<!-- Role -->
<div class="col-md-6">
    <label for="<?= $prefix ?>role" class="form-label text-primary fw-bold">User Role <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-user-shield"></i></span>
        <select class="form-select" id="<?= $prefix ?>role" name="user_role" required>
            <?php $stickyRole = getStickyVal(['add_user', 'update_user'], 'user_role'); ?>
            <option value="user" <?= $stickyRole === 'user' ? 'selected' : '' ?>>Standard User</option>
            <option value="admin" <?= $stickyRole === 'admin' ? 'selected' : '' ?>>Administrator</option>
        </select>
    </div>
</div>

<!-- Password -->
<div class="col-md-6">
    <label for="<?= $prefix ?>password" class="form-label text-primary fw-bold">Password <?= ($mode === 'add') ? '<span class="text-danger">*</span>' : '(leave blank to keep current)' ?></label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-key"></i></span>
        <input type="password" class="form-control" id="<?= $prefix ?>password" name="user_password" minlength="8" <?= ($mode === 'add') ? 'required' : '' ?>>
    </div>
    <div class="form-text">Must be at least 8 characters.</div>
</div>

<?php if ($mode === 'edit'): ?>
<!-- Active Status -->
<div class="col-md-6 d-flex align-items-center">
    <div class="form-check form-switch mt-4">
        <input type="hidden" name="is_active" value="0">
        <input class="form-check-input" type="checkbox" id="<?= $prefix ?>is_active" name="is_active" value="1"
               <?= getStickyVal('update_user', 'is_active') === '1' ? 'checked' : '' ?>>
        <label class="form-check-label" for="<?= $prefix ?>is_active">Active</label>
    </div>
</div>
<?php endif; ?>
