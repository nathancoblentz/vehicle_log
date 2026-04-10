<?php
/**
 * vehicle_controller.php
 *
 * Handles all vehicle form actions:
 *   add_vehicle, update_vehicle, deactivate_vehicle, delete_vehicle
 */

global $db, $feedback;

require_once __DIR__ . '/../includes/validate.php';
require_once __DIR__ . '/../models/VehicleModel.php';

// ── Routing ────────────────────────────────────────────────────────────────────

if (isset($_POST['add_vehicle'])) {
    saveVehicle($db, 'add');
} elseif (isset($_POST['update_vehicle'])) {
    saveVehicle($db, 'update');
} elseif (isset($_POST['deactivate_vehicle'])) {
    deactivateVehicle($db);
} elseif (isset($_POST['delete_vehicle'])) {
    deleteVehicle($db);
}


// ── Save (Add / Update) ────────────────────────────────────────────────────────

function collectVehicleFields(): array
{
    return [
        'vehicle_id'               => $_POST['vehicle_id']               ?? '',
        'vehicle_type'             => $_POST['vehicle_type']             ?? '',
        'vehicle_make'             => $_POST['vehicle_make']             ?? '',
        'vehicle_model'            => $_POST['vehicle_model']            ?? '',
        'vehicle_year'             => $_POST['vehicle_year']             ?? '',
        'vehicle_year_purchased'   => $_POST['vehicle_year_purchased']   ?? '',
        'vehicle_color'            => $_POST['vehicle_color']            ?? '',
        'vehicle_VIN'              => $_POST['vehicle_VIN']              ?? '',
        'vehicle_license_tag'      => $_POST['vehicle_license_tag']      ?? '',
        'vehicle_license_state'    => $_POST['vehicle_license_state']    ?? '',
        'vehicle_purchase_price'   => $_POST['vehicle_purchase_price']   ?? '',
        'vehicle_purchase_mileage' => $_POST['vehicle_purchase_mileage'] ?? '',
        'vehicle_current_mileage'  => $_POST['vehicle_current_mileage']  ?? '',
        'is_active'                => $_POST['is_active']                ?? '0',
    ];
}

function isVINTaken(PDO $db, string $vin, int $excludeId = 0): bool
{
    return VehicleModel::isVINTaken($db, $vin, $excludeId);
}

function saveVehicle(PDO $db, string $mode): void
{
    global $feedback;

    $fields = collectVehicleFields();

    $errors = validateFields($fields);
    if (!empty($errors)) {
        $feedback = [
            'type'    => 'error',
            'title'   => 'Please fix the following errors',
            'message' => implode(' ', $errors),
        ];
        return;
    }

    $vin       = strtoupper(trim($fields['vehicle_VIN']));
    $vehicleId = (int) $fields['vehicle_id'];

    if (isVINTaken($db, $vin, $mode === 'update' ? $vehicleId : 0)) {
        $prefix = $mode === 'update' ? 'A different vehicle' : 'A vehicle';
        $feedback = [
            'type'    => 'error',
            'title'   => 'Duplicate VIN',
            'message' => "$prefix with VIN $vin already exists.",
        ];
        return;
    }

    $bindings = [
        $fields['vehicle_type'],
        $fields['vehicle_make'],
        $fields['vehicle_model'],
        (int)   $fields['vehicle_year'],
        (int)   $fields['vehicle_year_purchased'],
        $fields['vehicle_color'],
        $vin,
        strtoupper($fields['vehicle_license_tag']),
        strtoupper($fields['vehicle_license_state']),
        (float) $fields['vehicle_purchase_price'],
        (int)   $fields['vehicle_purchase_mileage'],
        (int)   $fields['vehicle_current_mileage'],
    ];

    if ($mode === 'add') {
        VehicleModel::addVehicle($db, $bindings);
    } else {
        $bindings[] = (int) $fields['is_active'];
        $bindings[] = $vehicleId;
        VehicleModel::updateVehicle($db, $bindings);
    }

    $label  = $fields['vehicle_year'] . ' ' . $fields['vehicle_make'] . ' ' . $fields['vehicle_model'];
    $action = $mode === 'add' ? 'added' : 'updated';
    $feedback = [
        'type'    => 'success',
        'title'   => 'Vehicle ' . ucfirst($action),
        'message' => "$label was $action successfully.",
    ];
}


// ── Deactivate ─────────────────────────────────────────────────────────────────

function deactivateVehicle(PDO $db): void
{
    global $feedback;

    $id = (int) ($_POST['vehicle_id'] ?? 0);
    if ($id <= 0) {
        $feedback = ['type' => 'error', 'title' => 'Error', 'message' => 'No vehicle ID provided.'];
        return;
    }

    VehicleModel::deactivateVehicle($db, $id);

    $feedback = [
        'type'    => 'success',
        'title'   => 'Vehicle Deactivated',
        'message' => 'The vehicle has been set to inactive. It will no longer appear in active searches.',
    ];
}


// ── Delete ─────────────────────────────────────────────────────────────────────

function deleteVehicle(PDO $db): void
{
    global $feedback;

    $id = (int) ($_POST['vehicle_id'] ?? 0);
    if ($id <= 0) {
        $feedback = ['type' => 'error', 'title' => 'Error', 'message' => 'No vehicle ID provided.'];
        return;
    }

    // Block delete if related records exist (foreign key safety)
    $maint = VehicleModel::getMaintCount($db, $id);
    $fuel = VehicleModel::getFuelCount($db, $id);

    if ($maint > 0 || $fuel > 0) {
        $feedback = [
            'type'    => 'error',
            'title'   => 'Cannot Delete',
            'message' => "This vehicle has $maint maintenance record(s) and $fuel fuel record(s). Remove those records first.",
        ];
        return;
    }

    VehicleModel::deleteVehicle($db, $id);

    $feedback = [
        'type'    => 'success',
        'title'   => 'Deleted',
        'message' => 'Vehicle deleted successfully.',
    ];
}
