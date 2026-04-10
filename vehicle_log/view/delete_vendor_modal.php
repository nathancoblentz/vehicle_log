<?php
// delete_vendor_modal.php — Delete/Deactivate Vendor confirmation
// Boilerplate handled by renderModalStart() / renderModalEnd()

renderModalStart('cannotDeleteVendorModal', 'Confirm Deletion', 'deleteVendorForm', 'delete_vendor', 'bg-primary text-white');
?>

<input type="hidden" name="vendor_id" id="delete_vendor_id">
<input type="hidden" name="deactivate_vendor" id="deactivate_vendor_flag" value="0">

<div class="row g-3">
    <div class="col-12 text-center" id="delete_vendor_warning_icon">
        <i class="fa-solid fa-circle-info text-primary fa-4x mb-3"></i>
    </div>
    <div class="col-12 text-center fs-5">
        <p id="delete_vendor_message">
            Are you sure you want to permanently delete the vendor <strong id="delete_vendor_name"></strong>?
        </p>

        <div id="delete_vendor_dependencies" class="d-none text-start bg-light border p-3 rounded mt-3 text-dark fs-6">
            <p class="mb-2 fw-bold text-dark"><i class="fa-solid fa-link me-2"></i>This vendor has associated records:
            </p>
            <ul class="mb-0">
                <li><strong id="delete_vendor_usage_count">0</strong> maintenance record(s)</li>
            </ul>
        </div>

        <p id="delete_vendor_alternative_message" class="d-none mt-3 fs-6 text-dark">
            Because this vendor has associated records, it cannot be permanently deleted from the database. Would you
            like to set it to <strong>inactive</strong> instead?
        </p>
    </div>
</div>

</div>
</form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

    <button type="button" id="btn_deactivate_vendor" class="btn btn-warning text-dark fw-bold px-4"
        onclick="submitAsDeactivate('deleteVendorForm', 'deactivate_vendor_flag')">
        <i class="fa-solid fa-eye-slash me-2"></i>Set Inactive
    </button>

    <button type="submit" id="btn_delete_vendor" form="deleteVendorForm" class="btn btn-danger px-4 fw-bold">
        <i class="fa-solid fa-trash-can me-2"></i>Delete Permanently
    </button>
</div>

</div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Reusing 'cannotDeleteVendorModal' ID to match existing button attributes in includes/vendors.php while adding new functionality
        var deleteVendorModal = document.getElementById('cannotDeleteVendorModal');
        if (deleteVendorModal) {
            deleteVendorModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;

                var vendorId = button.getAttribute('data-vendor-id');
                var vendorName = button.getAttribute('data-vendor-name');

                var usageCount = parseInt(button.getAttribute('data-usage-count') || '0', 10);

                if (vendorId) {
                    document.getElementById('delete_vendor_id').value = vendorId;
                }

                document.getElementById('delete_vendor_name').textContent = vendorName;
                document.getElementById('deactivate_vendor_flag').value = '0'; // reset flag

                var depDiv = document.getElementById('delete_vendor_dependencies');
                var altMsg = document.getElementById('delete_vendor_alternative_message');
                var btnDelete = document.getElementById('btn_delete_vendor');
                var btnDeactivate = document.getElementById('btn_deactivate_vendor');
                var mainMsg = document.getElementById('delete_vendor_message');
                var icon = document.getElementById('delete_vendor_warning_icon');

                if (usageCount > 0) {
                    // Has dependencies - CANNOT delete
                    document.getElementById('delete_vendor_usage_count').textContent = usageCount;

                    depDiv.classList.remove('d-none');
                    altMsg.classList.remove('d-none');
                    btnDelete.classList.add('d-none'); // Hide permanent delete button

                    mainMsg.innerHTML = 'The vendor <strong>' + vendorName + '</strong> cannot be deleted.';
                    icon.innerHTML = '<i class="fa-solid fa-ban text-secondary fa-4x mb-3"></i>';

                    btnDeactivate.classList.remove('btn-warning');
                    btnDeactivate.classList.add('btn-primary');
                } else {
                    // No dependencies - CAN delete
                    depDiv.classList.add('d-none');
                    altMsg.classList.add('d-none');
                    btnDelete.classList.remove('d-none');

                    mainMsg.innerHTML = 'Are you sure you want to permanently delete the vendor <strong class="text-danger">' + vendorName + '</strong>?';
                    icon.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-danger fa-4x mb-3"></i>';

                    btnDeactivate.classList.add('btn-warning');
                    btnDeactivate.classList.remove('btn-primary');
                }
            });
        }
    });

    if (typeof submitAsDeactivate !== 'function') {
        function submitAsDeactivate(formId, flagId) {
            document.getElementById(flagId).value = '1';
            document.getElementById(formId).submit();
        }
    }
</script>