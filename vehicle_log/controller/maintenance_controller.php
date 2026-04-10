<?php
/**
 * maintenance_controller.php
 *
 * Handles all maintenance form actions:
 *   add_maintenance, update_maintenance, deactivate_maintenance, delete_maintenance
 */

global $db, $feedback;

require_once __DIR__ . '/../includes/validate.php';
require_once __DIR__ . '/../models/MaintenanceModel.php';

// ── Routing ────────────────────────────────────────────────────────────────────

if (isset($_POST['add_maintenance'])) {
    saveMaintenance($db, 'add');
} elseif (isset($_POST['update_maintenance'])) {
    saveMaintenance($db, 'update');
} elseif (isset($_POST['deactivate_maintenance'])) {
    deactivateMaintenance($db);
} elseif (isset($_POST['delete_maintenance'])) {
    deleteMaintenance($db);
}


// ── Save (Add / Update) ────────────────────────────────────────────────────────

function collectMaintenanceFields(): array
{
    return [
        'maintenance_id'          => $_POST['maintenance_id']          ?? '',
        'vehicle_id'              => $_POST['vehicle_id']              ?? '',
        'maintenance_type_id'     => $_POST['maintenance_type_id']     ?? '',
        'maintenance_date'        => $_POST['maintenance_date']        ?? '',
        'maintenance_mileage'     => $_POST['maintenance_mileage']     ?? '',
        'maintenance_cost'        => $_POST['maintenance_cost']        ?? '',
        'vendor_id'               => $_POST['vendor_id']               ?? '',
        'maintenance_status'      => $_POST['maintenance_status']      ?? '',
        'maintenance_description' => $_POST['maintenance_description'] ?? '',
    ];
}

function syncMaintenanceMileage(PDO $db, int $vehicleId): void
{
    MaintenanceModel::syncMaintenanceMileage($db, $vehicleId);
}

function saveMaintenance(PDO $db, string $mode): void
{
    global $feedback;

    $fields = collectMaintenanceFields();

    $toValidate = $fields;
    if ($mode === 'add') unset($toValidate['maintenance_id']);

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
    $mileage   = $fields['maintenance_mileage'] !== '' ? (int) $fields['maintenance_mileage'] : null;

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
        (int) $fields['maintenance_type_id'],
        $fields['vendor_id']               !== '' ? (int) $fields['vendor_id']               : null,
        $fields['maintenance_description'] !== '' ? $fields['maintenance_description']        : null,
        $fields['maintenance_cost']        !== '' ? (float) $fields['maintenance_cost']       : null,
        $mileage,
        $fields['maintenance_date'],
        $fields['maintenance_status']      !== '' ? $fields['maintenance_status']             : null,
    ];

    if ($mode === 'add') {
        MaintenanceModel::addMaintenance($db, $bindings);
    } else {
        $bindings[] = (int) $fields['maintenance_id'];
        MaintenanceModel::updateMaintenance($db, $bindings);
    }

    syncMaintenanceMileage($db, $vehicleId);

    $action = $mode === 'add' ? 'added' : 'updated';
    $feedback = [
        'type'    => 'success',
        'title'   => 'Maintenance Record ' . ucfirst($action),
        'message' => "Maintenance record was $action successfully.",
    ];
}


// ── Deactivate ─────────────────────────────────────────────────────────────────

function deactivateMaintenance(PDO $db): void
{
    global $feedback;

    $id = (int) ($_POST['maintenance_id'] ?? 0);
    if ($id <= 0) {
        $feedback = ['type' => 'error', 'title' => 'Error', 'message' => 'No maintenance ID provided.'];
        return;
    }

    $vehicleId = MaintenanceModel::deactivateMaintenance($db, $id);

    if ($vehicleId > 0) {
        syncMaintenanceMileage($db, $vehicleId);
    }

    $feedback = [
        'type'    => 'success',
        'title'   => 'Maintenance Record Deactivated',
        'message' => 'The maintenance record has been set to inactive.',
    ];
}


// ── Delete ─────────────────────────────────────────────────────────────────────

function deleteMaintenance(PDO $db): void
{
    global $feedback;

    $id = (int) ($_POST['maintenance_id'] ?? 0);
    if ($id <= 0) {
        $feedback = ['type' => 'error', 'title' => 'Error', 'message' => 'No maintenance ID provided.'];
        return;
    }

    $vehicleId = MaintenanceModel::deleteMaintenance($db, $id);

    if ($vehicleId > 0) {
        syncMaintenanceMileage($db, $vehicleId);
    }

    $feedback = [
        'type'    => 'success',
        'title'   => 'Deleted',
        'message' => 'Maintenance record deleted successfully.',
    ];
}
