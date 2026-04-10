<?php
// delete_maintenance_modal.php — Delete/Deactivate Maintenance confirmation
// Boilerplate handled by renderModalStart() / renderModalEnd()

renderModalStart('deleteMaintenanceModal', 'Confirm Deletion', 'deleteMaintenanceForm', 'delete_maintenance', 'bg-primary text-white');
?>

<input type="hidden" name="maintenance_id" id="delete_maintenance_id">
<input type="hidden" name="deactivate_maintenance" id="deactivate_maintenance_flag" value="0">

<div class="row g-3">
    <div class="col-12 text-center" id="delete_maintenance_warning_icon">
        <i class="fa-solid fa-circle-info text-primary fa-4x mb-3"></i>
    </div>
    <div class="col-12 text-center fs-5">
        <p id="delete_maintenance_message">
            Are you sure you want to permanently delete this maintenance record for <strong
                id="delete_maintenance_name"></strong>?
        </p>

        <p id="delete_maintenance_alternative_message" class="d-none mt-3 fs-6">
            Would you like to set it to <strong>inactive</strong> instead? Inactive records are hidden from searches by
            default.
        </p>
    </div>
</div>

</div>
</form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

    <button type="button" id="btn_deactivate_maintenance" class="btn btn-warning text-dark fw-bold px-4"
        onclick="submitAsDeactivate('deleteMaintenanceForm', 'deactivate_maintenance_flag')">
        <i class="fa-solid fa-eye-slash me-2"></i>Set Inactive
    </button>

    <button type="submit" id="btn_delete_maintenance" form="deleteMaintenanceForm" class="btn btn-danger px-4 fw-bold">
        <i class="fa-solid fa-trash-can me-2"></i>Delete Permanently
    </button>
</div>

</div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var deleteMaintenanceModal = document.getElementById('deleteMaintenanceModal');
        if (deleteMaintenanceModal) {
            deleteMaintenanceModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;

                var maintenanceId = button.getAttribute('data-maintenance-id');
                var maintenanceName = button.getAttribute('data-maintenance-name');

                if (maintenanceId) {
                    document.getElementById('delete_maintenance_id').value = maintenanceId;
                }

                document.getElementById('delete_maintenance_name').textContent = maintenanceName;
                document.getElementById('deactivate_maintenance_flag').value = '0'; // reset flag
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