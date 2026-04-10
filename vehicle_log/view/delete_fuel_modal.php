<?php
// delete_fuel_modal.php — Delete/Deactivate Fuel confirmation
// Boilerplate handled by renderModalStart() / renderModalEnd()

renderModalStart('deleteFuelModal', 'Confirm Deletion', 'deleteFuelForm', 'delete_fuel', 'bg-primary text-white');
?>

<input type="hidden" name="fuel_id" id="delete_fuel_id">
<input type="hidden" name="deactivate_fuel" id="deactivate_fuel_flag" value="0">

<div class="row g-3">
    <div class="col-12 text-center" id="delete_fuel_warning_icon">
        <i class="fa-solid fa-circle-info text-primary fa-4x mb-3"></i>
    </div>
    <div class="col-12 text-center fs-5">
        <p id="delete_fuel_message">
            Are you sure you want to permanently delete this fuel record for <strong id="delete_fuel_name"></strong>?
        </p>

        <p id="delete_fuel_alternative_message" class="d-none mt-3 fs-6">
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

    <button type="button" id="btn_deactivate_fuel" class="btn btn-warning text-dark fw-bold px-4"
        onclick="submitAsDeactivate('deleteFuelForm', 'deactivate_fuel_flag')">
        <i class="fa-solid fa-eye-slash me-2"></i>Set Inactive
    </button>

    <button type="submit" id="btn_delete_fuel" form="deleteFuelForm" class="btn btn-danger px-4 fw-bold">
        <i class="fa-solid fa-trash-can me-2"></i>Delete Permanently
    </button>
</div>

</div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var deleteFuelModal = document.getElementById('deleteFuelModal');
        if (deleteFuelModal) {
            deleteFuelModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;

                var fuelId = button.getAttribute('data-fuel-id');
                var fuelName = button.getAttribute('data-fuel-name');

                if (fuelId) {
                    document.getElementById('delete_fuel_id').value = fuelId;
                }

                document.getElementById('delete_fuel_name').textContent = fuelName;
                document.getElementById('deactivate_fuel_flag').value = '0'; // reset flag
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