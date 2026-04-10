<?php
// delete_user_modal.php — Delete/Deactivate User confirmation
// Boilerplate handled by renderModalStart() / renderModalEnd()

renderModalStart('deleteUserModal', 'Confirm Deletion', 'deleteUserForm', 'delete_user', 'bg-primary text-white');
?>

<input type="hidden" name="user_id" id="delete_user_id">
<input type="hidden" name="deactivate_user" id="deactivate_user_flag" value="0">

<div class="row g-3">
    <div class="col-12 text-center" id="delete_user_warning_icon">
        <i class="fa-solid fa-circle-info text-primary fa-4x mb-3"></i>
    </div>
    <div class="col-12 text-center fs-5">
        <p id="delete_user_message">
            Are you sure you want to permanently delete the user account for <strong id="delete_user_name"></strong>?
        </p>

        <p id="delete_user_alternative_message" class="d-none mt-3 fs-6">
            Would you like to set this user to <strong>inactive</strong> instead? Inactive users cannot log in.
        </p>
    </div>
</div>

</div>
</form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

    <button type="button" id="btn_deactivate_user" class="btn btn-warning text-dark fw-bold px-4"
        onclick="submitAsDeactivate('deleteUserForm', 'deactivate_user_flag')">
        <i class="fa-solid fa-user-slash me-2"></i>Set Inactive
    </button>

    <button type="submit" id="btn_delete_user" form="deleteUserForm" class="btn btn-danger px-4 fw-bold">
        <i class="fa-solid fa-trash-can me-2"></i>Delete Permanently
    </button>
</div>

</div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var deleteUserModal = document.getElementById('deleteUserModal');
        if (deleteUserModal) {
            deleteUserModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;

                var userId = button.getAttribute('data-user-id');
                var userName = button.getAttribute('data-user-name');

                if (userId) {
                    document.getElementById('delete_user_id').value = userId;
                }

                document.getElementById('delete_user_name').textContent = userName;
                document.getElementById('deactivate_user_flag').value = '0'; // reset flag
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