<?php
// edit_vendor_modal.php — Edit Vendor form
// Uses renderModalStart() / renderModalEnd() for boilerplate
?>

<?php
renderModalStart('editVendorModal', 'Edit Vendor', 'editVendorForm', 'update_vendor');
?>

<input type="hidden" name="vendor_id" id="edit_vendor_id" value="<?= htmlspecialchars($_POST['vendor_id'] ?? '') ?>">

<?php
$mode = 'edit';
$prefix = 'edit_vendor_';
include __DIR__ . '/partials/_vendor_form.php';
renderModalEnd('editVendorForm', 'Save Changes'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editModal = document.getElementById('editVendorModal');
        const editForm = document.getElementById('editVendorForm');
        const confirmNotice = document.getElementById('edit_vendor_confirm_notice');
        const errorAlert = document.getElementById('edit_vendor_error_alert');
        const submitBtn = editModal.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        let isConfirmed = false;

        if (editModal) {
            editModal.addEventListener('show.bs.modal', function(event) {
                // If we are recovering from a failed POST, do NOT overwrite fields with original DB values.
                if (window.isErrorRecovery) {
                    window.isErrorRecovery = false; // Reset for next time
                    return;
                }

                const button = event.relatedTarget;
                const mapping = {
                    'vendor-id': 'edit_vendor_id',
                    'vendor-name': 'edit_vendor_name',
                    'vendor-address': 'edit_vendor_address',
                    'vendor-city': 'edit_vendor_city',
                    'vendor-state': 'edit_vendor_state',
                    'vendor-zip': 'edit_vendor_zip',
                    'vendor-phone': 'edit_vendor_phone',
                    'vendor-email': 'edit_vendor_email',
                    'is-active': 'edit_vendor_is_active'
                };

                for (const [dataAttr, inputId] of Object.entries(mapping)) {
                    const val = button.getAttribute('data-' + dataAttr);
                    const input = document.getElementById(inputId);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = (val == 1);
                        } else {
                            input.value = val || '';
                        }
                    }
                }
                
                // Reset confirmation state on show
                isConfirmed = false;
                submitBtn.innerHTML = originalBtnText;
                submitBtn.className = 'btn btn-primary';
                if (confirmNotice) confirmNotice.classList.add('d-none');
                if (errorAlert) errorAlert.classList.add('d-none');
            });
        }

        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                const email = document.getElementById('edit_vendor_email').value.trim();
                const phone = document.getElementById('edit_vendor_phone').value.trim();
                const errors = [];

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email !== "" && !emailRegex.test(email)) {
                    errors.push("<strong>Email:</strong> Please enter a valid email address.");
                }

                const phoneRegex = /^\(\d{3}\)-\d{3}-\d{4}$/;
                if (phone === "") {
                    errors.push("<strong>Phone:</strong> is required.");
                } else if (!phoneRegex.test(phone)) {
                    errors.push("<strong>Phone:</strong> must be 10 digits in (XXX)-XXX-XXXX format.");
                }

                if (errors.length > 0) {
                    e.preventDefault();
                    errorAlert.innerHTML = errors.join('<br>');
                    errorAlert.classList.remove('d-none');
                    if (confirmNotice) confirmNotice.classList.add('d-none');
                    isConfirmed = false;
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.className = 'btn btn-primary';
                    editModal.querySelector('.modal-body').scrollTop = 0;
                } else if (!isConfirmed) {
                    e.preventDefault();
                    isConfirmed = true;
                    confirmNotice.classList.remove('d-none');
                    errorAlert.classList.add('d-none');
                    submitBtn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Finalize Save';
                    submitBtn.className = 'btn btn-warning';
                    editModal.querySelector('.modal-body').scrollTop = 0;
                }
            });
        }

        // Reset alert when modal is closed
        editModal.addEventListener('hidden.bs.modal', function() {
            if (errorAlert) {
                errorAlert.classList.add('d-none');
                errorAlert.innerHTML = '';
            }
            if (confirmNotice) confirmNotice.classList.add('d-none');
        });
    });
</script>