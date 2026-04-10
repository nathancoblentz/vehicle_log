<?php
// delete_vehicle_modal.php — Delete/Deactivate Vehicle confirmation
// Boilerplate handled by renderModalStart() / renderModalEnd()

renderModalStart('deleteVehicleModal', 'Confirm Deletion', 'deleteVehicleForm', 'delete_vehicle', 'bg-primary text-white');
?>

<input type="hidden" name="vehicle_id" id="delete_vehicle_id">

<!-- We will dynamically change the form action based on which button is clicked (deactivate vs delete) -->
<input type="hidden" name="deactivate_vehicle" id="deactivate_vehicle_flag" value="0">

<div class="row g-3">
    <div class="col-12 text-center" id="delete_vehicle_warning_icon">
        <i class="fa-solid fa-circle-info text-primary fa-4x mb-3"></i>
    </div>
    <div class="col-12 text-center fs-5">
        <p id="delete_vehicle_message">
            Are you sure you want to permanently delete the vehicle <strong id="delete_vehicle_name"></strong>?
        </p>

        <div id="delete_vehicle_dependencies" class="d-none text-start bg-light border p-3 rounded mt-3 text-dark fs-6">
            <p class="mb-2 fw-bold text-dark"><i class="fa-solid fa-link me-2"></i>This vehicle has associated records:
            </p>
            <ul class="mb-0">
                <li><strong id="delete_vehicle_maint_count">0</strong> maintenance record(s)</li>
                <li><strong id="delete_vehicle_fuel_count">0</strong> fuel record(s)</li>
            </ul>
        </div>

        <p id="delete_vehicle_alternative_message" class="d-none mt-3 fs-6">
            Because this vehicle has associated records, it cannot be permanently deleted from the database. Would you
            like to set it to <strong>inactive</strong> instead? Inactive vehicles are hidden from searches by default.
        </p>
    </div>
</div>

<?php
// Custom footer for delete modals since we need two action buttons
// We close the body and provide our own footer instead of using renderModalEnd() verbatim if we need multiple buttons,
// but renderModalEnd() provides a single primary button. 
// For delete modals, we'll close the tags manually so we have full control over the two-button layout.
?>
</div> <!-- Close row from above -->
</form> <!-- Close form from renderModalStart -->
</div> <!-- Close modal-body from renderModalStart -->

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

    <!-- Set Inactive Button (Always available, but highlighted if dependencies exist) -->
    <button type="button" id="btn_deactivate_vehicle" class="btn btn-warning text-dark fw-bold px-4"
        onclick="submitAsDeactivate('deleteVehicleForm', 'deactivate_vehicle_flag')">
        <i class="fa-solid fa-eye-slash me-2"></i>Set Inactive
    </button>

    <!-- Delete Button (Hidden if dependencies exist) -->
    <button type="submit" id="btn_delete_vehicle" form="deleteVehicleForm" class="btn btn-danger px-4 fw-bold">
        <i class="fa-solid fa-trash-can me-2"></i>Delete Permanently
    </button>
</div>

</div> <!-- Close modal-content -->
</div> <!-- Close modal-dialog -->
</div> <!-- Close modal -->

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var deleteVehicleModal = document.getElementById('deleteVehicleModal');
        if (deleteVehicleModal) {
            deleteVehicleModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;

                var vehicleId = button.getAttribute('data-vehicle-id');
                var vehicleName = button.getAttribute('data-vehicle-name');

                // For vehicles, total usage is maint + fuel
                var maintCount = parseInt(button.getAttribute('data-maint-count') || '0', 10);
                var fuelCount = parseInt(button.getAttribute('data-fuel-count') || '0', 10);
                var totalUsage = maintCount + fuelCount;

                document.getElementById('delete_vehicle_id').value = vehicleId;
                document.getElementById('delete_vehicle_name').textContent = vehicleName;
                document.getElementById('deactivate_vehicle_flag').value = '0'; // reset flag

                var depDiv = document.getElementById('delete_vehicle_dependencies');
                var altMsg = document.getElementById('delete_vehicle_alternative_message');
                var btnDelete = document.getElementById('btn_delete_vehicle');
                var btnDeactivate = document.getElementById('btn_deactivate_vehicle');
                var mainMsg = document.getElementById('delete_vehicle_message');
                var icon = document.getElementById('delete_vehicle_warning_icon');

                if (totalUsage > 0) {
                    // Has dependencies - CANNOT delete
                    document.getElementById('delete_vehicle_maint_count').textContent = maintCount;
                    document.getElementById('delete_vehicle_fuel_count').textContent = fuelCount;

                    depDiv.classList.remove('d-none');
                    altMsg.classList.remove('d-none');
                    btnDelete.classList.add('d-none'); // Hide permanent delete button

                    mainMsg.innerHTML = 'The vehicle <strong>' + vehicleName + '</strong> cannot be deleted.';
                    icon.innerHTML = '<i class="fa-solid fa-ban text-secondary fa-4x mb-3"></i>';

                    // Highlight deactivate button
                    btnDeactivate.classList.remove('btn-warning');
                    btnDeactivate.classList.add('btn-primary');
                } else {
                    // No dependencies - CAN delete
                    depDiv.classList.add('d-none');
                    altMsg.classList.add('d-none');
                    btnDelete.classList.remove('d-none'); // Show permanent delete button

                    mainMsg.innerHTML = 'Are you sure you want to permanently delete the vehicle <strong class="text-danger">' + vehicleName + '</strong>?';
                    icon.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-danger fa-4x mb-3"></i>';

                    // Reset deactivate button style
                    btnDeactivate.classList.add('btn-warning');
                    btnDeactivate.classList.remove('btn-primary');
                }
            });
        }
    });

    // Helper function to submit a form as a "deactivate" action instead of "delete"
    // We will reuse this across modals
    if (typeof submitAsDeactivate !== 'function') {
        function submitAsDeactivate(formId, flagId) {
            document.getElementById(flagId).value = '1';
            document.getElementById(formId).submit();
        }
    }
</script>