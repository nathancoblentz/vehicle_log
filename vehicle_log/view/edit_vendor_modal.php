<?php
// edit_vendor_modal.php — Edit Vendor form
// Uses renderModalStart() / renderModalEnd() for boilerplate

renderModalStart('editVendorModal', 'Edit Vendor', 'editVendorForm', 'update_vendor');
?>

<input type="hidden" name="vendor_id" id="edit_vendor_id">

<!-- Vendor Name -->
<div class="col-12">
    <label for="edit_vendor_name" class="form-label">Vendor Name</label>
    <input type="text" class="form-control" id="edit_vendor_name" name="vendor_name" required>
</div>

<!-- Address -->
<div class="col-12">
    <label for="edit_vendor_address" class="form-label">Address</label>
    <input type="text" class="form-control" id="edit_vendor_address" name="vendor_address">
</div>

<!-- City -->
<div class="col-md-4">
    <label for="edit_vendor_city" class="form-label">City</label>
    <input type="text" class="form-control" id="edit_vendor_city" name="vendor_city">
</div>

<!-- State -->
<div class="col-md-4">
    <label for="edit_vendor_state" class="form-label">State</label>
    <input type="text" class="form-control" id="edit_vendor_state" name="vendor_state" maxlength="2">
</div>

<!-- Zip -->
<div class="col-md-4">
    <label for="edit_vendor_zip" class="form-label">Zip Code</label>
    <input type="text" class="form-control" id="edit_vendor_zip" name="vendor_zip">
</div>

<!-- Phone -->
<div class="col-md-6">
    <label for="edit_vendor_phone" class="form-label">Phone</label>
    <input type="text" class="form-control" id="edit_vendor_phone" name="vendor_phone">
</div>

<!-- Email -->
<div class="col-md-6">
    <label for="edit_vendor_email" class="form-label">Email</label>
    <input type="email" class="form-control" id="edit_vendor_email" name="vendor_email">
</div>

<?php renderModalEnd('editVendorForm', 'Save Changes'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('editVendorModal');
        if (!modal) return;
        modal.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            if (!btn) return;
            const fields = ['vendor_id', 'vendor_name', 'vendor_address', 'vendor_city',
                'vendor_state', 'vendor_zip', 'vendor_phone', 'vendor_email'];
            fields.forEach(function (f) {
                const el = document.getElementById('edit_' + f);
                if (el) {
                    el.value = btn.getAttribute('data-' + f.replace(/_/g, '-')) || '';
                }
            });
        });
    });
</script>