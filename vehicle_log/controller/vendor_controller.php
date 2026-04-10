<?php
/**
 * vendor_controller.php
 *
 * Handles all vendor form actions:
 *   add_vendor, update_vendor, deactivate_vendor, delete_vendor
 */

global $db, $feedback;

require_once __DIR__ . '/../includes/validate.php';
require_once __DIR__ . '/../models/VendorModel.php';

// ── Routing ────────────────────────────────────────────────────────────────────

if (isset($_POST['add_vendor'])) {
    saveVendor($db, 'add');
} elseif (isset($_POST['update_vendor'])) {
    saveVendor($db, 'update');
} elseif (isset($_POST['deactivate_vendor'])) {
    deactivateVendor($db);
} elseif (isset($_POST['delete_vendor'])) {
    deleteVendor($db);
}


// ── Save (Add / Update) ────────────────────────────────────────────────────────

function collectVendorFields(): array
{
    return [
        'vendor_id'      => $_POST['vendor_id']      ?? '',
        'vendor_name'    => $_POST['vendor_name']    ?? '',
        'vendor_address' => $_POST['vendor_address'] ?? '',
        'vendor_city'    => $_POST['vendor_city']    ?? '',
        'vendor_state'   => $_POST['vendor_state']   ?? '',
        'vendor_zip'     => $_POST['vendor_zip']     ?? '',
        'vendor_phone'   => formatPhone($_POST['vendor_phone'] ?? ''),
        'vendor_email'   => $_POST['vendor_email']   ?? '',
        'is_active'      => $_POST['is_active']      ?? '0',
    ];
}

function saveVendor(PDO $db, string $mode): void
{
    global $feedback;

    $fields = collectVendorFields();

    $toValidate = $fields;
    if ($mode === 'add') unset($toValidate['vendor_id'], $toValidate['is_active']);

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
        $fields['vendor_name'],
        $fields['vendor_address'] !== '' ? $fields['vendor_address']               : null,
        $fields['vendor_city']    !== '' ? $fields['vendor_city']                  : null,
        $fields['vendor_state']   !== '' ? strtoupper($fields['vendor_state'])     : null,
        $fields['vendor_zip']     !== '' ? $fields['vendor_zip']                   : null,
        $fields['vendor_phone']   !== '' ? $fields['vendor_phone']                 : null,
        $fields['vendor_email']   !== '' ? strtolower($fields['vendor_email'])     : null,
    ];

    if ($mode === 'add') {
        VendorModel::addVendor($db, $bindings);
    } else {
        $bindings[] = (int) $fields['is_active'];
        $bindings[] = (int) $fields['vendor_id'];
        VendorModel::updateVendor($db, $bindings);
    }

    $action = $mode === 'add' ? 'added' : 'updated';
    $name   = htmlspecialchars($fields['vendor_name']);
    $feedback = [
        'type'    => 'success',
        'title'   => 'Vendor ' . ucfirst($action),
        'message' => "$name was $action successfully.",
    ];
}


// ── Deactivate ─────────────────────────────────────────────────────────────────

function deactivateVendor(PDO $db): void
{
    global $feedback;

    $id = (int) ($_POST['vendor_id'] ?? 0);
    if ($id <= 0) {
        $feedback = ['type' => 'error', 'title' => 'Error', 'message' => 'No vendor ID provided.'];
        return;
    }

    VendorModel::deactivateVendor($db, $id);

    $feedback = [
        'type'    => 'success',
        'title'   => 'Vendor Deactivated',
        'message' => 'The vendor has been set to inactive. It will no longer appear as an option for new maintenance records.',
    ];
}


// ── Delete ─────────────────────────────────────────────────────────────────────

function deleteVendor(PDO $db): void
{
    global $feedback;

    $id = (int) ($_POST['vendor_id'] ?? 0);
    if ($id <= 0) {
        $feedback = ['type' => 'error', 'title' => 'Error', 'message' => 'No vendor ID provided.'];
        return;
    }

    // Block delete if any maintenance records still reference this vendor
    $count = VendorModel::getMaintenanceCount($db, $id);

    if ($count > 0) {
        $feedback = [
            'type'    => 'error',
            'title'   => 'Cannot Delete',
            'message' => "This vendor has $count maintenance record(s). Remove or reassign those records first.",
        ];
        return;
    }

    VendorModel::deleteVendor($db, $id);

    $feedback = [
        'type'    => 'success',
        'title'   => 'Deleted',
        'message' => 'Vendor deleted successfully.',
    ];
}
