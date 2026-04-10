<?php
/**
 * service_controller.php
 *
 * Handles all service form actions:
 *   add_service, update_service,
 *   deactivate_service, delete_service
 */

global $db, $feedback;

require_once __DIR__ . '/../includes/validate.php';
require_once __DIR__ . '/../models/ServiceModel.php';

// ── Routing ────────────────────────────────────────────────────────────────────

if (isset($_POST['add_service'])) {
    saveMaintenanceType($db, 'add');
} elseif (isset($_POST['update_service'])) {
    saveMaintenanceType($db, 'update');
} elseif (isset($_POST['deactivate_service'])) {
    deactivateMaintenanceType($db);
} elseif (isset($_POST['delete_service'])) {
    deleteMaintenanceType($db);
}


// ── Save (Add / Update) ────────────────────────────────────────────────────────

function collectMaintenanceTypeFields(): array
{
    return [
        'maintenance_id'             => $_POST['maintenance_id']             ?? '',
        'maintenance_code'           => $_POST['maintenance_code']           ?? '',
        'maintenance_type'           => $_POST['maintenance_type']           ?? '',
        'maintenance_description'    => $_POST['maintenance_description']    ?? '',
        'recommended_interval_miles' => $_POST['recommended_interval_miles'] ?? '',
        'recommended_interval_days'  => $_POST['recommended_interval_days']  ?? '',
        'recommended_cost'           => $_POST['recommended_cost']           ?? '',
        'is_active'                  => $_POST['is_active']                  ?? '0',
    ];
}

function saveMaintenanceType(PDO $db, string $mode): void
{
    global $feedback;

    $fields = collectMaintenanceTypeFields();

    $toValidate = $fields;
    if ($mode === 'add') unset($toValidate['maintenance_id'], $toValidate['is_active']);

    $errors = validateFields($toValidate);
    if (!empty($errors)) {
        $feedback = [
            'type'    => 'error',
            'title'   => 'Please fix the following errors',
            'message' => implode(' ', $errors),
        ];
        return;
    }

    $bindings = [
        $fields['maintenance_code']           !== '' ? $fields['maintenance_code']                     : null,
        $fields['maintenance_type'],
        $fields['maintenance_description']    !== '' ? $fields['maintenance_description']               : null,
        $fields['recommended_interval_miles'] !== '' ? (int)   $fields['recommended_interval_miles']   : null,
        $fields['recommended_interval_days']  !== '' ? (int)   $fields['recommended_interval_days']    : null,
        $fields['recommended_cost']           !== '' ? (float) $fields['recommended_cost']             : null,
    ];

    if ($mode === 'add') {
        ServiceModel::addMaintenanceType($db, $bindings);
    } else {
        $bindings[] = (int) $fields['is_active'];
        $bindings[] = (int) $fields['maintenance_id'];
        ServiceModel::updateMaintenanceType($db, $bindings);
    }

    $action   = $mode === 'add' ? 'added' : 'updated';
    $typeName = htmlspecialchars($fields['maintenance_type']);
    $feedback = [
        'type'    => 'success',
        'title'   => 'Service ' . ucfirst($action),
        'message' => "$typeName was $action successfully.",
    ];
}


// ── Deactivate ─────────────────────────────────────────────────────────────────

function deactivateMaintenanceType(PDO $db): void
{
    global $feedback;

    $id = (int) ($_POST['maintenance_id'] ?? 0);
    if ($id <= 0) {
        $feedback = ['type' => 'error', 'title' => 'Error', 'message' => 'No service ID provided.'];
        return;
    }

    ServiceModel::deactivateMaintenanceType($db, $id);

    $feedback = [
        'type'    => 'success',
        'title'   => 'Service Deactivated',
        'message' => 'The service has been set to inactive. It will no longer appear as an option for new maintenance records.',
    ];
}


// ── Delete ─────────────────────────────────────────────────────────────────────

function deleteMaintenanceType(PDO $db): void
{
    global $feedback;

    $id = (int) ($_POST['maintenance_id'] ?? 0);
    if ($id <= 0) {
        $feedback = ['type' => 'error', 'title' => 'Error', 'message' => 'No service ID provided.'];
        return;
    }

    // Block delete if any maintenance records still reference this type
    $count = ServiceModel::getUsageCount($db, $id);

    if ($count > 0) {
        $feedback = [
            'type'    => 'error',
            'title'   => 'Cannot Delete',
            'message' => "This service is referenced by $count maintenance record(s). Remove or reassign those records first.",
        ];
        return;
    }

    ServiceModel::deleteMaintenanceType($db, $id);

    $feedback = [
        'type'    => 'success',
        'title'   => 'Deleted',
        'message' => 'Service deleted successfully.',
    ];
}
