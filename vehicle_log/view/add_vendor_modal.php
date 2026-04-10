
<?php
renderModalStart('addVendorModal', 'Add Vendor', 'addVendorForm', 'add_vendor');
$mode = 'add';
$prefix = 'add_vendor_';
include __DIR__ . '/partials/_vendor_form.php';
renderModalEnd('addVendorForm', 'Add Vendor'); ?>


<script>
    (function() {
        let isConfirmed = false;
        const form = document.getElementById('addVendorForm');
        const modal = document.getElementById('addVendorModal');
        const confirmNotice = document.getElementById('add_vendor_confirm_notice');
        const errorAlert = document.getElementById('add_vendor_error_alert');
        const submitBtn = modal.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;

        form.addEventListener('submit', function(e) {
            const email = document.getElementById('add_vendor_email').value.trim();
            const phone = document.getElementById('add_vendor_phone').value.trim();
            let errors = [];

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email !== "" && !emailRegex.test(email)) {
                errors.push("<strong>Email:</strong> Please enter a valid email address.");
            }

            // Phone validation
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
                confirmNotice.classList.add('d-none'); // Hide confirm if errors exist
                isConfirmed = false;
                submitBtn.innerHTML = originalBtnText;
                submitBtn.className = 'btn btn-primary';
                
                document.querySelector('#addVendorModal .modal-body').scrollTop = 0;
            } else if (!isConfirmed) {
                // First valid click -> ask for confirmation
                e.preventDefault();
                isConfirmed = true;
                confirmNotice.classList.remove('d-none');
                errorAlert.classList.add('d-none');
                
                submitBtn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Finalize Save';
                submitBtn.className = 'btn btn-warning';
                
                document.querySelector('#addVendorModal .modal-body').scrollTop = 0;
            }
        });

        // Reset state when modal is closed
        modal.addEventListener('hidden.bs.modal', function() {
            errorAlert.classList.add('d-none');
            confirmNotice.classList.add('d-none');
            isConfirmed = false;
            submitBtn.innerHTML = originalBtnText;
            submitBtn.className = 'btn btn-primary';
        });
    })();
</script>