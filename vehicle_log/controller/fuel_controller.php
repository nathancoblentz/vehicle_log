<?php
/**
 * fuel_controller.php
 *
 * Handles all fuel form actions:
 *   add_fuel, update_fuel, deactivate_fuel, delete_fuel
 */

global $db, $feedback;

require_once __DIR__ . '/../includes/validate.php';
require_once __DIR__ . '/../models/FuelModel.php';

// ── Routing ────────────────────────────────────────────────────────────────────

if (isset($_POST['add_fuel'])) {
    saveFuel($db, 'add');
} elseif (isset($_POST['update_fuel'])) {
    saveFuel($db, 'update');
} elseif (isset($_POST['deactivate_fuel'])) {
    deactivateFuel($db);
} elseif (isset($_POST['delete_fuel'])) {
    deleteFuel($db);
}


// ── Save (Add / Update) ────────────────────────────────────────────────────────

function collectFuelFields(): array
{
    return [
        'fuel_id'              => $_POST['fuel_id']              ?? '',
        'vehicle_id'           => $_POST['vehicle_id']           ?? '',
        'fuel_date'            => $_POST['fuel_date']            ?? '',
        'fuel_mileage'         => $_POST['fuel_mileage']         ?? '',
        'fuel_payment_method'  => $_POST['fuel_payment_method']  ?? '',
        'fuel_gallons'         => $_POST['fuel_gallons']         ?? '',
        'fuel_cost_per_gallon' => $_POST['fuel_cost_per_gallon'] ?? '',
        'fuel_source'          => $_POST['fuel_source']          ?? '',
        'fuel_receipt_url'     => $_POST['fuel_receipt_url']     ?? '',
        'fuel_notes'           => $_POST['fuel_notes']           ?? '',
    ];
}

function syncVehicleMileage(PDO $db, int $vehicleId): void
{
    FuelModel::syncVehicleMileage($db, $vehicleId);
}

function saveFuel(PDO $db, string $mode): void
{
    global $feedback;

    $fields = collectFuelFields();

    $toValidate = $fields;
    if ($mode === 'add') unset($toValidate['fuel_id']);

    $errors = validateFields($toValidate);
    if (!empty($errors)) {
        $feedback = [
            'type'    => 'error',
            'title'   => 'Please fix the following errors',
            'message' => implode(' ', $errors),
        ];
        return;
    }

    $vehicleId = (int) $fields['vehicle_id'];
    $mileage   = $fields['fuel_mileage'] !== '' ? (int) $fields['fuel_mileage'] : null;

    // Server-side: ensure mileage is not below vehicle's current odometer
    if ($mileage !== null) {
        require_once __DIR__ . '/../models/VehicleModel.php';
        $stmt = $db->prepare("SELECT vehicle_current_mileage FROM vehicles WHERE vehicle_id = ?");
        $stmt->execute([$vehicleId]);
        $currentMileage = (int) $stmt->fetchColumn();
        if ($mileage < $currentMileage) {
            $feedback = [
                'type'    => 'error',
                'title'   => 'Mileage Too Low',
                'message' => "Odometer reading ($mileage) cannot be less than the vehicle's current mileage ($currentMileage).",
            ];
            return;
        }
    }

    $bindings = [
        $vehicleId,
        $fields['fuel_date'],
        $fields['fuel_source']         !== '' ? $fields['fuel_source']         : null,
        (float) $fields['fuel_gallons'],
        (float) $fields['fuel_cost_per_gallon'],
        $mileage,
        $fields['fuel_payment_method'] !== '' ? $fields['fuel_payment_method'] : null,
        $fields['fuel_notes']          !== '' ? $fields['fuel_notes']          : null,
        $fields['fuel_receipt_url']    !== '' ? $fields['fuel_receipt_url']    : null,
    ];

    if ($mode === 'add') {
        FuelModel::addFuel($db, $bindings);
    } else {
        $bindings[] = (int) $fields['fuel_id'];
        FuelModel::updateFuel($db, $bindings);
    }

    syncVehicleMileage($db, $vehicleId);

    $action = $mode === 'add' ? 'added' : 'updated';
    $feedback = [
        'type'    => 'success',
        'title'   => 'Fuel Record ' . ucfirst($action),
        'message' => "Fuel record was $action successfully.",
    ];
}


// ── Deactivate ─────────────────────────────────────────────────────────────────

function deactivateFuel(PDO $db): void
{
    global $feedback;

    $id = (int) ($_POST['fuel_id'] ?? 0);
    if ($id <= 0) {
        $feedback = ['type' => 'error', 'title' => 'Error', 'message' => 'No fuel ID provided.'];
        return;
    }

    $vehicleId = FuelModel::deactivateFuel($db, $id);

    if ($vehicleId > 0) {
        syncVehicleMileage($db, $vehicleId);
    }

    $feedback = [
        'type'    => 'success',
        'title'   => 'Fuel Record Deactivated',
        'message' => 'The fuel entry has been set to inactive.',
    ];
}


// ── Delete ─────────────────────────────────────────────────────────────────────

function deleteFuel(PDO $db): void
{
    global $feedback;

    $id = (int) ($_POST['fuel_id'] ?? 0);
    if ($id <= 0) {
        $feedback = ['type' => 'error', 'title' => 'Error', 'message' => 'No fuel ID provided.'];
        return;
    }

    $vehicleId = FuelModel::deleteFuel($db, $id);

    if ($vehicleId > 0) {
        syncVehicleMileage($db, $vehicleId);
    }

    $feedback = [
        'type'    => 'success',
        'title'   => 'Deleted',
        'message' => 'Fuel record deleted successfully.',
    ];
}
