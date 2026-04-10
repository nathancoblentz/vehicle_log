<?php

/**
 * =============================================================================
 * functions.php — Core Utility & View Helpers Library
 * =============================================================================
 *
 * PURPOSE:
 *   This file contains all the reusable rendering functions and system-wide
 *   utilities for the application. It acts as the "glue" between the database
 *   data, the controllers, and the HTML views.
 *
 * WHAT LIVES HERE:
 *
 *   1. View Helpers (Rendering UI)
 *      Instead of copying and pasting the same HTML for tables and modals,
 *      these functions generate the HTML dynamically.
 *      - `renderTable()`: Takes a 2D array from the database and spits out a
 *        Bootstrap-styled table completely automatically.
 *      - `renderModalStart() / End()`: Generates the boilerplate HTML needed
 *        for a popup form modal.
 *
 *   2. Controller Router (`addHandlers()`)
 *      This is the traffic cop for all form submissions (POST requests).
 *      Instead of letting every page process its own forms, the `addHandlers()`
 *      function sits near the top of the app and listens for ANY form submission
 *      (determined by hidden input fields like `name="add_vehicle"`).
 *
 * HOW ROUTING WORKS:
 *   Every form in the application includes a hidden input like this:
 *     <input type="hidden" name="add_vehicle" value="1">
 *
 *   When the user submits the form, `addHandlers()` detects that `add_vehicle`
 *   was sent in the `$_POST` array, and it immediately routes the request to
 *   the appropriate controller (`vehicle_controller.php`), which then parses,
 *   validates, and saves it. 
 *
 * DRY PRINCIPLE:
 *   By keeping these functions here, we ensure that if we want to change how
 *   our tables look (e.g., adding a CSS class), we only have to change it in
 *   ONE place (`renderTable`), and the entire site updates instantly.
 *
 * =============================================================================
 */

/**
 * Render a generic Bootstrap table from an array of associative arrays.
 *
 * @param array $rows
 * @param array|null $columns
 * @param string $tableClass
 */
function renderTable(array $rows, ?array $columns = null, string $tableClass = 'table table-striped table-bordered table-hover')
{


    if (empty($rows)) {
        echo '<div class="alert alert-warning"><h4>No records found!</h4></div>';
        return;
    }

    // If no columns provided, infer from first row
    if ($columns === null) {
        $columns = array_combine(array_keys($rows[0]), array_keys($rows[0]));
    }

    echo '<div class="table-responsive">';
    echo '<table class="' . htmlspecialchars($tableClass) . '">';
    echo '<thead class="table-dark"><tr>';

    foreach ($columns as $key => $label) {
        echo '<th>' . htmlspecialchars($label) . '</th>';
    }

    echo '</tr></thead><tbody>';

    foreach ($rows as $row) {
        echo '<tr>';
        foreach ($columns as $key => $label) {
            $value = $row[$key] ?? '';
            
            // Auto-format active states as glowing badges
            if ($key === 'is_active') {
                $isActive = ($value == 1 || strtolower((string)$value) === 'true' || strtolower((string)$value) === 'active');
                $glowClass = $isActive ? 'glow-success' : 'glow-danger';
                $text = $isActive ? 'Active' : 'Inactive';
                echo '<td class="text-center align-middle"><span class="badge ' . $glowClass . '">' . $text . '</span></td>';
            } else {
                echo '<td class="align-middle">' . htmlspecialchars((string)$value) . '</td>';
            }
        }
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
}

// Specific render functions for each table

/**
 * Vehicles
 * @param PDO $db Database connection
 */
function renderVehiclesTable(PDO $db)
{
    require_once __DIR__ . '/../models/VehicleModel.php';
    $vehicles = VehicleModel::getFilteredVehicles($db, "");

    $columns = [
        'vehicle_full' => 'Vehicle',
        'vehicle_VIN' => 'VIN',
        'vehicle_license_tag' => 'License Tag',
        'vehicle_license_state' => 'State',
        'vehicle_current_mileage' => 'Mileage',
        'is_active' => 'Active'
    ];

    renderTable($vehicles, $columns);
}


/**
 * Users
 * @param PDO $db Database connection
 */
function renderUsersTable(PDO $db)
{
    require_once __DIR__ . '/../models/UserModel.php';
    $users = UserModel::getAllUsers($db);

    $columns = [
        'user_id' => 'ID',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email',
        'user_role' => 'Role',
        'is_active' => 'Active',
        'date_created' => 'Created',
        'date_lastlogin' => 'Last Login',
        'date_modified' => 'Modified'
    ];

    renderTable($users, $columns);
}


/**
 * Fuel
 * @param PDO $db Database connection
 */
function renderFuelTable(PDO $db)
{
    require_once __DIR__ . '/../models/FuelModel.php';
    $fuelRowsRaw = FuelModel::getFilteredFuel($db, ['search_string' => '', 'start_date' => '', 'end_date' => '', 'min_cost' => '', 'max_cost' => '']);
    
    $fuelRows = [];
    foreach ($fuelRowsRaw as $row) {
        $row['fuel_cost_per_gallon'] = '$' . number_format($row['fuel_cost_per_gallon'], 3);
        $row['fuel_cost'] = '$' . number_format($row['fuel_cost'], 2);
        $fuelRows[] = $row;
    }

    $columns = [
        'vehicle_full' => 'Vehicle',
        'fuel_source' => 'Source',
        'fuel_gallons' => 'Gallons',
        'fuel_cost_per_gallon' => 'Cost/Gal',
        'fuel_cost' => 'Total Cost',
        'fuel_mileage' => 'Mileage',
        'fuel_date_formatted' => 'Date',
        'fuel_payment_method' => 'Payment'
    ];

    renderTable($fuelRows, $columns);
}

/**
 * Maintenance
 * @param PDO $db Database connection
 */
function renderMaintenanceTable(PDO $db)
{
    require_once __DIR__ . '/../models/MaintenanceModel.php';
    $maintRaw = MaintenanceModel::getFilteredMaintenance($db, ['search_string' => '', 'start_date' => '', 'end_date' => '', 'min_cost' => '', 'max_cost' => '']);
    
    $maintenance = [];
    foreach ($maintRaw as $row) {
        $row['maintenance_cost'] = '$' . number_format($row['maintenance_cost'], 2);
        $maintenance[] = $row;
    }

    $columns = [
        'vehicle_full' => 'Vehicle',
        'type_name' => 'Type',
        'vendor_name' => 'Vendor',
        'maintenance_description' => 'Description',
        'maintenance_cost' => 'Cost',
        'maintenance_mileage' => 'Mileage',
        'maintenance_date_formatted' => 'Date',
        'maintenance_status' => 'Status'
    ];

    renderTable($maintenance, $columns);
}

/**
 * Services
 * @param PDO $db Database connection
 */
function renderMaintenanceTypeTable(PDO $db)
{
    require_once __DIR__ . '/../models/ServiceModel.php';
    $typesRaw = ServiceModel::getFilteredTypes($db, "");

    $types = [];
    foreach ($typesRaw as $row) {
        $row['recommended_cost'] = '$' . number_format((float) $row['recommended_cost'], 2);
        $types[] = $row;
    }

    $columns = [
        'maintenance_code' => 'Code',
        'maintenance_type' => 'Type',
        'maintenance_description' => 'Description',
        'recommended_interval_miles' => 'Interval (Miles)',
        'recommended_interval_days' => 'Interval (Days)',
        'recommended_cost' => 'Cost'
    ];

    renderTable($types, $columns);
}

/**
 * Vendors
 * @param PDO $db Database connection
 */
function renderVendorsTable(PDO $db)
{
    require_once __DIR__ . '/../models/VendorModel.php';
    $vendors = VendorModel::getFilteredVendors($db, "");

    $columns = [
        'vendor_name' => 'Name',
        'vendor_city' => 'City',
        'vendor_state' => 'State',
        'vendor_phone' => 'Phone',
        'vendor_email' => 'Email'
    ];

    renderTable($vendors, $columns);
}



// ── MODAL HELPERS ──────────────────────────────────────────────

/**
 * Render the opening boilerplate for a Bootstrap modal form.
 *
 * @param string $id          Modal element ID (e.g. 'addVehicleModal')
 * @param string $title       Modal header title
 * @param string $formId      Form element ID (e.g. 'addVehicleForm')
 * @param string $hiddenName  Hidden input name for handler routing (e.g. 'add_vehicle')
 * @param string $headerClass Optional custom CSS class for the modal header (e.g. 'bg-danger text-white')
 */
function renderModalStart(string $id, string $title, string $formId, string $hiddenName, string $headerClass = 'bg-primary'): void
{
    ?>
    <div class="modal fade" id="<?= $id ?>" tabindex="-1" aria-labelledby="<?= $id ?>Label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header <?= htmlspecialchars($headerClass) ?>">
                    <h5 class="modal-title" id="<?= $id ?>Label"><?= htmlspecialchars($title) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="<?= $formId ?>" method="POST">
                        <input type="hidden" name="<?= $hiddenName ?>" value="1">
                        <div class="row g-3">
                            <?php
}

/**
 * Render the closing boilerplate for a Bootstrap modal form.
 *
 * @param string $formId      Form element ID to link the submit button
 * @param string $submitLabel Text on the submit button
 */
function renderModalEnd(string $formId, string $submitLabel): void
{
    ?>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="<?= $formId ?>"
                        class="btn btn-primary"><?= htmlspecialchars($submitLabel) ?></button>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Render a feedback modal that auto-shows when $feedback is set.
 * Replaces all individual *_feedback.php files.
 */
function renderFeedbackModal(): void
{
    global $feedback;
    if (!$feedback)
        return;

    $isSuccess = ($feedback['type'] === 'success');
    $color = $isSuccess ? 'success' : 'danger';
    $title = $feedback['title'] ?? ($isSuccess ? 'Success' : 'Error');

    // Determine the likely form ID from the previous POST to allow "Continue Editing"
    $lastFormId = null;
    if (!$isSuccess) {
        $idMap = [
            'add_vehicle'           => 'addVehicleModal',
            'update_vehicle'        => 'editVehicleModal',
            'add_fuel'              => 'addFuelModal',
            'update_fuel'           => 'editFuelModal',
            'add_vendor'            => 'addVendorModal',
            'update_vendor'         => 'editVendorModal',
            'add_maintenance'       => 'addMaintenanceModal',
            'update_maintenance'    => 'editMaintenanceModal',
            'add_service'  => 'addMaintenanceTypeModal',
            'update_service' => 'editMaintenanceTypeModal',
            'add_user'              => 'addUserModal',
            'update_user'           => 'editUserModal',
        ];
        foreach ($idMap as $postKey => $modalId) {
            if (isset($_POST[$postKey])) {
                $lastFormId = $modalId;
                break;
            }
        }
    }
    ?>
    <div class="modal fade" id="feedbackModal">
        <div class="modal-dialog">
            <div class="modal-content border-<?= $color ?>">
                <div class="modal-header bg-<?= $color ?> text-white">
                    <h5><?= htmlspecialchars($title) ?></h5>
                </div>
                <div class="modal-body">
                    <?= htmlspecialchars($feedback['message']) ?>
                </div>
                <div class="modal-footer">
                    <?php if (!$isSuccess && $lastFormId): ?>
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger" onclick="reOpenForm('<?= $lastFormId ?>')">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Continue Editing
                        </button>
                    <?php else: ?>
                        <button class="btn btn-<?= $color ?>" data-bs-dismiss="modal">OK</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const isSuccess = <?= json_encode($isSuccess) ?>;
            const feedbackModalEl = document.getElementById('feedbackModal');
            const feedbackModal = new bootstrap.Modal(feedbackModalEl);
            feedbackModal.show();

            // Global function to re-open a form modal after dismissing the feedback modal
            window.reOpenForm = function (modalId) {
                window.isErrorRecovery = true;
                feedbackModal.hide();
                // Delay slightly to allow the feedback modal to finish hiding
                setTimeout(() => {
                    const targetModalEl = document.getElementById(modalId);
                    if (targetModalEl) {
                        const targetModal = new bootstrap.Modal(targetModalEl);
                        targetModal.show();
                    }
                }, 400);
            };

            // Handle dismissal logic (Only for Success - auto-refresh list)
            feedbackModalEl.addEventListener('hidden.bs.modal', function () {
                if (isSuccess) {
                    // Manually clean up backdrop to prevent "darkened screen" during redirect
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';

                    // Redirect to show all records, preserving the current tab hash
                    const url = new URL(window.location.href);
                    url.searchParams.set('show_all', '1');
                    const hash = window.location.hash;
                    window.location.href = url.toString() + hash;
                }
            });

            // If this is an error, set a global flag to prevent Edit modals from overwriting POST data
            if (!isSuccess) {
                window.isErrorRecovery = true;
            }

            // Auto-dismiss the feedback modal after 3 seconds for success, longer for errors
            setTimeout(function () {
                feedbackModal.hide();
            }, isSuccess ? 3000 : 5000);
        });
    </script>
    <?php
}

/**
 * Returns a value from $_POST if a failed submission occurred, otherwise returns the default.
 * Helpful for "Sticky Forms" where user input must be preserved after an error.
 * 
 * @param string|array $actionKeys One or more keys to check in $_POST (e.g. 'add_fuel', 'update_fuel')
 * @param string $fieldName  The field name to retrieve
 * @param string $default    Fallback value
 */
function getStickyVal($actionKeys, string $fieldName, string $default = ''): string
{
    $keys = is_array($actionKeys) ? $actionKeys : [$actionKeys];
    foreach ($keys as $key) {
        if (isset($_POST[$key]) && isset($_POST[$fieldName])) {
            return (string) $_POST[$fieldName];
        }
    }
    return $default;
}

// ── FORM / FEEDBACK INCLUDES ───────────────────────────────────

/**
 * Includes all modal HTML files for rendering the Add, Edit, and Delete forms.
 */
function addForms()
{
    global $db;
    include_once __DIR__ . '/../view/add_vehicle_modal.php';
    include_once __DIR__ . '/../view/add_fuel_modal.php';
    include_once __DIR__ . '/../view/add_service_modal.php';
    include_once __DIR__ . '/../view/add_maintenance_modal.php';
    include_once __DIR__ . '/../view/add_vendor_modal.php';
    include_once __DIR__ . '/../view/add_user_modal.php';

    // EDIT FORMS
    include_once __DIR__ . '/../view/edit_vehicle_modal.php';
    include_once __DIR__ . '/../view/edit_service_modal.php';
    include_once __DIR__ . '/../view/edit_maintenance_modal.php';
    include_once __DIR__ . '/../view/edit_fuel_modal.php';
    include_once __DIR__ . '/../view/edit_vendor_modal.php';
    include_once __DIR__ . '/../view/edit_user_modal.php';

    // DELETE FORMS
    include_once __DIR__ . '/../view/delete_vehicle_modal.php';
    include_once __DIR__ . '/../view/delete_service_modal.php';
    include_once __DIR__ . '/../view/delete_maintenance_modal.php';
    include_once __DIR__ . '/../view/delete_vendor_modal.php';
    include_once __DIR__ . '/../view/delete_fuel_modal.php';
    include_once __DIR__ . '/../view/delete_user_modal.php';
}

/**
 * Triggers the feedback modal if $feedback is set in the global scope.
 */
function addFeedback()
{
    renderFeedbackModal();
}

// ADD HANDLERS

function addHandlers()
{
    global $feedback;

    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    // ── Vehicles ───────────────────────────────────────────────────────────────

    if (
        isset($_POST['add_vehicle']) || isset($_POST['update_vehicle'])
        || isset($_POST['deactivate_vehicle']) || isset($_POST['delete_vehicle'])
    ) {

        // Include the vehicle controller
        require_once __DIR__ . '/../controller/vehicle_controller.php';

        // ── Fuel ───────────────────────────────────────────────────────────────────

    } elseif (
        isset($_POST['add_fuel']) || isset($_POST['update_fuel'])
        || isset($_POST['deactivate_fuel']) || isset($_POST['delete_fuel'])
    ) {

        // Include the fuel controller
        require_once __DIR__ . '/../controller/fuel_controller.php';

        // ── Maintenance ────────────────────────────────────────────────────────────
    } elseif (
        isset($_POST['add_maintenance']) || isset($_POST['update_maintenance'])
        || isset($_POST['deactivate_maintenance']) || isset($_POST['delete_maintenance'])
    ) {

        // Include the maintenance controller
        require_once __DIR__ . '/../controller/maintenance_controller.php';

        // ── Services ──────────────────────────────────────────────────────
    } elseif (
        isset($_POST['add_service']) || isset($_POST['update_service'])
        || isset($_POST['deactivate_service']) || isset($_POST['delete_service'])
    ) {

        // Include the service controller
        require_once __DIR__ . '/../controller/service_controller.php';

        // ── Vendors ────────────────────────────────────────────────────────────────
    } elseif (
        isset($_POST['add_vendor']) || isset($_POST['update_vendor'])
        || isset($_POST['deactivate_vendor']) || isset($_POST['delete_vendor'])
    ) {

        // Include the vendor controller
        require_once __DIR__ . '/../controller/vendor_controller.php';


        // ── Users ──────────────────────────────────────────────────────────────────
    } elseif (
        isset($_POST['add_user']) || isset($_POST['update_user'])
        || isset($_POST['delete_user']) || isset($_POST['deactivate_user'])
    ) {

        // Include the user controller
        require_once __DIR__ . '/../controller/user_controller.php';
    }

}


/**
 * Format a 10-digit phone number as (XXX)-XXX-XXXX.
 * Returns the original string if it is not 10 digits.
 *
 * @param string|null $phone
 * @return string
 */
function formatPhone(?string $phone): string
{
    if (!$phone) return '';
    $digits = preg_replace('/\D/', '', $phone);
    if (strlen($digits) !== 10) return $phone;
    return sprintf("(%s)-%s-%s", substr($digits, 0, 3), substr($digits, 3, 3), substr($digits, 6));
}


