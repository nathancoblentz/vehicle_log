<?php
/**
 * _vendor_form.php - Shared fields for Add/Edit Vendor modals
 * 
 * Expected variables:
 * @var string $mode   'add' or 'edit'
 * @var string $prefix 'add_vendor_' or 'edit_vendor_'
 */
?>

<!-- Confirmation Alert (Hidden by default) -->
<div id="<?= $prefix ?>confirm_notice" class="alert alert-warning d-none">
    <i class="fa-solid fa-circle-question me-2"></i>
    <strong>Please confirm:</strong> Are you sure you want to save these changes?
</div>

<!-- Error Alert (Hidden by default) -->
<div id="<?= $prefix ?>error_alert" class="alert alert-danger d-none"></div>

<!-- Vendor Name -->
<div class="col-12">
    <label for="<?= $prefix ?>name" class="form-label">Vendor Name *</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-store"></i></span>
        <input type="text" class="form-control" id="<?= $prefix ?>name" name="vendor_name" 
               value="<?= htmlspecialchars(getStickyVal(['add_vendor', 'update_vendor'], 'vendor_name')) ?>" required>
    </div>
</div>

<!-- Address -->
<div class="col-12">
    <label for="<?= $prefix ?>address" class="form-label">Address</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-map-location-dot"></i></span>
        <input type="text" class="form-control" id="<?= $prefix ?>address" name="vendor_address"
               value="<?= htmlspecialchars(getStickyVal(['add_vendor', 'update_vendor'], 'vendor_address')) ?>">
    </div>
</div>

<!-- City -->
<div class="col-md-4">
    <label for="<?= $prefix ?>city" class="form-label">City</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-city"></i></span>
        <input type="text" class="form-control" id="<?= $prefix ?>city" name="vendor_city"
               value="<?= htmlspecialchars(getStickyVal(['add_vendor', 'update_vendor'], 'vendor_city')) ?>">
    </div>
</div>

<!-- State -->
<div class="col-md-4">
    <label for="<?= $prefix ?>state" class="form-label">State</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-map"></i></span>
        <input type="text" class="form-control" id="<?= $prefix ?>state" name="vendor_state" maxlength="2" placeholder="OH"
               value="<?= htmlspecialchars(getStickyVal(['add_vendor', 'update_vendor'], 'vendor_state')) ?>">
    </div>
</div>

<!-- Zip -->
<div class="col-md-4">
    <label for="<?= $prefix ?>zip" class="form-label">Zip Code</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-envelopes-bulk"></i></span>
        <input type="text" class="form-control" id="<?= $prefix ?>zip" name="vendor_zip" placeholder="44101"
               value="<?= htmlspecialchars(getStickyVal(['add_vendor', 'update_vendor'], 'vendor_zip')) ?>">
    </div>
</div>

<!-- Phone -->
<div class="col-md-6">
    <label for="<?= $prefix ?>phone" class="form-label">Phone *</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
        <input type="tel" class="form-control" id="<?= $prefix ?>phone" name="vendor_phone" placeholder="(555)-555-5555" 
               value="<?= htmlspecialchars(getStickyVal(['add_vendor', 'update_vendor'], 'vendor_phone')) ?>" required>
    </div>
</div>

<script>
    (function() {
        // Find the input by the dynamic ID prefix
        const phoneInput = document.getElementById('<?= $prefix ?>phone');
        if (!phoneInput) return;

        phoneInput.addEventListener('input', function(e) {
            // strip all non-digits
            let digits = e.target.value.replace(/\D/g, '');
            if (digits.length > 10) digits = digits.slice(0, 10);
            
            let formatted = '';
            if (digits.length > 0) {
                formatted = '(' + digits.slice(0, 3);
                if (digits.length > 3) {
                    formatted += ')-' + digits.slice(3, 6);
                    if (digits.length > 6) {
                        formatted += '-' + digits.slice(6, 10);
                    }
                }
            }
            e.target.value = formatted;
        });
    })();
</script>

<!-- Email -->
<div class="col-md-6">
    <label for="<?= $prefix ?>email" class="form-label">Email</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
        <input type="email" class="form-control" id="<?= $prefix ?>email" name="vendor_email" placeholder="vendor@example.com"
               value="<?= htmlspecialchars(getStickyVal(['add_vendor', 'update_vendor'], 'vendor_email')) ?>">
    </div>
</div>

<?php if ($mode === 'edit'): ?>
<!-- Active Status -->
<div class="col-12 mt-2">
    <div class="form-check form-switch">
        <input type="hidden" name="is_active" value="0">
        <input class="form-check-input" type="checkbox" id="<?= $prefix ?>is_active" name="is_active" value="1"
               <?= getStickyVal('update_vendor', 'is_active') === '1' ? 'checked' : '' ?>>
        <label class="form-check-label" for="<?= $prefix ?>is_active">Active</label>
    </div>
</div>
<?php endif; ?>
