<?php
// add_vendor_modal.php — Add Vendor form fields
// Boilerplate handled by renderModalStart() / renderModalEnd()

renderModalStart('addVendorModal', 'Add Vendor', 'addVendorForm', 'add_vendor');
?>

<!-- Vendor Name -->
<div class="col-12">
    <label for="vendor_name" class="form-label">Vendor Name</label>
    <input type="text" class="form-control" id="vendor_name" name="vendor_name" required>
</div>

<!-- Address -->
<div class="col-12">
    <label for="vendor_address" class="form-label">Address</label>
    <input type="text" class="form-control" id="vendor_address" name="vendor_address">
</div>

<!-- City -->
<div class="col-md-4">
    <label for="vendor_city" class="form-label">City</label>
    <input type="text" class="form-control" id="vendor_city" name="vendor_city">
</div>

<!-- State -->
<div class="col-md-4">
    <label for="vendor_state" class="form-label">State</label>
    <input type="text" class="form-control" id="vendor_state" name="vendor_state" maxlength="2">
</div>

<!-- Zip -->
<div class="col-md-4">
    <label for="vendor_zip" class="form-label">Zip Code</label>
    <input type="text" class="form-control" id="vendor_zip" name="vendor_zip">
</div>

<!-- Phone -->
<div class="col-md-6">
    <label for="vendor_phone" class="form-label">Phone</label>
    <input type="text" class="form-control" id="vendor_phone" name="vendor_phone">
</div>

<!-- Email -->
<div class="col-md-6">
    <label for="vendor_email" class="form-label">Email</label>
    <input type="email" class="form-control" id="vendor_email" name="vendor_email">
</div>

<?php renderModalEnd('addVendorForm', 'Add Vendor'); ?>