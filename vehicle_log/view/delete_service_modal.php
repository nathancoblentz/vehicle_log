<?php
// delete_service_modal.php — Delete/Deactivate Service confirmation
// Boilerplate handled by renderModalStart() / renderModalEnd()

renderModalStart('cannotDeleteModal', 'Confirm Deletion', 'deleteMaintenanceTypeForm', 'delete_service', 'bg-primary text-white');
?>

<input type="hidden" name="maintenance_id" id="delete_type_id">
<input type="hidden" name="deactivate_service" id="deactivate_type_flag" value="0">

<div class="row g-3">
    <div class="col-12 text-center" id="delete_type_warning_icon">
        <i class="fa-solid fa-circle-info text-primary fa-4x mb-3"></i>
    </div>
    <div class="col-12 text-center fs-5">
        <p id="delete_type_message">
            Are you sure you want to permanently delete the service <strong id="delete_type_name"></strong>?
        </p>

        <div id="delete_type_dependencies" class="d-none text-start bg-light border p-3 rounded mt-3 text-dark fs-6">
            <p class="mb-2 fw-bold text-dark"><i class="fa-solid fa-link me-2"></i>This type has associated records:</p>
            <ul class="mb-0">
                <li><strong id="delete_type_usage_count">0</strong> maintenance record(s)</li>
            </ul>
        </div>

        <p id="delete_type_alternative_message" class="d-none mt-3 fs-6">
            Because this service has associated records, it cannot be permanently deleted from the database.
            Would you like to set it to <strong>inactive</strong> instead?
        </p>
    </div>
</div>

</div>
</form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

    <button type="button" id="btn_deactivate_type" class="btn btn-warning text-dark fw-bold px-4"
        onclick="submitAsDeactivate('deleteMaintenanceTypeForm', 'deactivate_type_flag')">
        <i class="fa-solid fa-eye-slash me-2"></i>Set Inactive
    </button>

    <button type="submit" id="btn_delete_type" form="deleteMaintenanceTypeForm" class="btn btn-danger px-4 fw-bold">
        <i class="fa-solid fa-trash-can me-2"></i>Delete Permanently
    </button>
</div>

</div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Note: Using 'cannotDeleteModal' for backward compatibility with the existing button ID in includes/maintenance_type.php
        // but adding full delete capabilities.
        var deleteTypeModal = document.getElementById('cannotDeleteModal');
        if (deleteTypeModal) {
            deleteTypeModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;

                var typeId = button.getAttribute('data-maintenance-id');
                var typeName = button.getAttribute('data-type-name');

                var usageCount = parseInt(button.getAttribute('data-usage-count') || '0', 10);

                if (typeId) {
                    document.getElementById('delete_type_id').value = typeId;
                }

                document.getElementById('delete_type_name').textContent = typeName;
                document.getElementById('deactivate_type_flag').value = '0'; // reset flag

                var depDiv = document.getElementById('delete_type_dependencies');
                var altMsg = document.getElementById('delete_type_alternative_message');
                var btnDelete = document.getElementById('btn_delete_type');
                var btnDeactivate = document.getElementById('btn_deactivate_type');
                var mainMsg = document.getElementById('delete_type_message');
                var icon = document.getElementById('delete_type_warning_icon');

                if (usageCount > 0) {
                    // Has dependencies - CANNOT delete
                    document.getElementById('delete_type_usage_count').textContent = usageCount;

                    depDiv.classList.remove('d-none');
                    altMsg.classList.remove('d-none');
                    btnDelete.classList.add('d-none'); // Hide permanent delete button

                    mainMsg.innerHTML = 'The service <strong>' + typeName + '</strong> cannot be deleted.';
                    icon.innerHTML = '<i class="fa-solid fa-ban text-secondary fa-4x mb-3"></i>';

                    btnDeactivate.classList.remove('btn-warning');
                    btnDeactivate.classList.add('btn-primary');
                } else {
                    // No dependencies - CAN delete
                    depDiv.classList.add('d-none');
                    altMsg.classList.add('d-none');
                    btnDelete.classList.remove('d-none');

                    mainMsg.innerHTML = 'Are you sure you want to permanently delete the service <strong class="text-danger">' + typeName + '</strong>?';
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