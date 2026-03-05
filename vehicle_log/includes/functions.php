<?php

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
            echo '<td>' . htmlspecialchars($value) . '</td>';
        }
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
}

// Specific render functions for each table

/**
 * Vehicles
 */
function renderVehiclesTable(PDO $db)
{

    $query = "
        SELECT 
            vehicle_id,
            CONCAT(vehicle_year, ' ', vehicle_make, ' ', vehicle_model, ' (', LOWER(vehicle_color), ' ', LOWER(vehicle_type), ')') AS vehicle_full,
            vehicle_VIN,
            vehicle_license_tag,
            vehicle_license_state,
            vehicle_current_mileage,
            is_active
        FROM vehicles
        ORDER BY is_active DESC, vehicle_make ASC
    ";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

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
 */
function renderUsersTable(PDO $db)
{

    $stmt = $db->query("
        SELECT user_id, first_name, last_name, email, user_role,
               is_active, date_created, date_lastlogin, date_modified
        FROM users
    ");

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

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
 */
function renderFuelTable(PDO $db)
{

    $stmt = $db->query("
        SELECT fuel.fuel_id, fuel.vehicle_id,
               CONCAT(vehicle_year, ' ', vehicle_make, ' ', vehicle_model, ' (', LOWER(vehicle_color), ' ', LOWER(vehicle_type), ')') AS vehicle_full,
               fuel_source, fuel_gallons,
               CONCAT('$', FORMAT(fuel_cost_per_gallon, 3)) AS fuel_cost_per_gallon,
               CONCAT('$', FORMAT(fuel_gallons * fuel_cost_per_gallon, 2)) AS fuel_cost, 
               fuel_mileage,
               DATE_FORMAT(fuel.fuel_date, '%b %e, %Y') AS fuel_date_formatted,
               fuel_payment_method
        FROM fuel
        JOIN vehicles ON vehicles.vehicle_id = fuel.vehicle_id
        ORDER BY fuel.fuel_date DESC
    ");

    $fuelRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

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
 */
function renderMaintenanceTable(PDO $db)
{

    $stmt = $db->query("
        SELECT m.maintenance_id, m.vehicle_id, m.maintenance_type_id,
               CONCAT(vehicle_year, ' ', vehicle_make, ' ', vehicle_model, ' (', LOWER(vehicle_color), ' ', LOWER(vehicle_type), ')') AS vehicle_full,
               mt.maintenance_type AS type_name,
               v.vendor_name,
               m.maintenance_description,
               m.maintenance_cost,
               m.maintenance_mileage,
               DATE_FORMAT(m.maintenance_date, '%b %e, %Y') AS maintenance_date_formatted,
               m.maintenance_status
        FROM maintenance m
        JOIN vehicles ON vehicles.vehicle_id = m.vehicle_id
        LEFT JOIN maintenance_type mt ON mt.maintenance_id = m.maintenance_type_id
        LEFT JOIN vendors v ON v.vendor_id = m.vendor_id
        ORDER BY m.maintenance_date DESC
    ");

    $maintenance = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

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
 * Maintenance Types
 */
function renderMaintenanceTypeTable(PDO $db)
{

    $stmt = $db->query("
        SELECT maintenance_id,
               maintenance_code,
               maintenance_type,
               maintenance_description,
               recommended_interval_miles,
               recommended_interval_days,
               CONCAT('$', FORMAT(recommended_cost, 2)) AS recommended_cost
        FROM maintenance_type
        ORDER BY maintenance_type ASC
    ");

    $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

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


// ── MODAL HELPERS ──────────────────────────────────────────────

/**
 * Render the opening boilerplate for a Bootstrap modal form.
 *
 * @param string $id          Modal element ID (e.g. 'addVehicleModal')
 * @param string $title       Modal header title
 * @param string $formId      Form element ID (e.g. 'addVehicleForm')
 * @param string $hiddenName  Hidden input name for handler routing (e.g. 'add_vehicle')
 */
function renderModalStart(string $id, string $title, string $formId, string $hiddenName): void
{
    ?>
    <div class="modal fade" id="<?= $id ?>" tabindex="-1" aria-labelledby="<?= $id ?>Label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
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
                    <button class="btn btn-<?= $color ?>" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            new bootstrap.Modal(document.getElementById('feedbackModal')).show();
        });
    </script>
    <?php
}

// ── FORM / FEEDBACK INCLUDES ───────────────────────────────────

// ADD FORMS
function addForms()
{
    include_once 'view/add_vehicle_modal.php';
    include_once 'view/add_fuel_modal.php';
    include_once 'view/add_maintenance_type_modal.php';
    include_once 'view/add_maintenance_modal.php';
    include_once 'view/add_vendor_modal.php';

    // EDIT FORMS
    include_once 'view/edit_vehicle_modal.php';
    include_once 'view/edit_maintenance_type_modal.php';
    include_once 'view/edit_maintenance_modal.php';
    include_once 'view/edit_fuel_modal.php';
    include_once 'view/edit_vendor_modal.php';
}

function addFeedback()
{
    renderFeedbackModal();
}

// ADD HANDLERS

function addHandlers()
{
    global $feedback;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vehicle'])) {
        require 'controler/add_vehicle.php';
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_maintenance_type'])) {
        require 'controler/add_maintenance_type.php';
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_maintenance'])) {
        require 'controler/add_maintenance.php';
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_fuel'])) {
        require 'controler/add_fuel.php';
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vendor'])) {
        require 'controler/add_vendor.php';

        // DELETE
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deactivate_vehicle'])) {
        require 'controler/deactivate_vehicle.php';
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_vehicle'])) {
        require 'controler/delete_vehicle.php';
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_maintenance_type'])) {
        require 'controler/delete_maintenance_type.php';
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_maintenance'])) {
        require 'controler/delete_maintenance.php';
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_fuel'])) {
        require 'controler/delete_fuel.php';
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_vendor'])) {
        require 'controler/delete_vendor.php';

        // UPDATE
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_vehicle'])) {
        require 'controler/update_vehicle.php';
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_maintenance_type'])) {
        require 'controler/update_maintenance_type.php';
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_maintenance'])) {
        require 'controler/update_maintenance.php';
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_fuel'])) {
        require 'controler/update_fuel.php';
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_vendor'])) {
        require 'controler/update_vendor.php';
    }

}

/**
 * Vendors
 */
function renderVendorsTable(PDO $db)
{
    $stmt = $db->query("
        SELECT v.vendor_id, v.vendor_name, v.vendor_city, v.vendor_state,
               v.vendor_phone, v.vendor_email, v.is_active
        FROM vendors v
        ORDER BY v.vendor_name ASC
    ");

    $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    $columns = [
        'vendor_name' => 'Name',
        'vendor_city' => 'City',
        'vendor_state' => 'State',
        'vendor_phone' => 'Phone',
        'vendor_email' => 'Email'
    ];

    renderTable($vendors, $columns);
}
