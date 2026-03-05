<?php
// Handler: Update Maintenance Type

if (!empty($_POST['update_maintenance_type'])) {

    global $db;

    try {
        $stmt = $db->prepare("
            UPDATE maintenance_type SET
                maintenance_code = ?,
                maintenance_type = ?,
                maintenance_description = ?,
                recommended_interval_miles = ?,
                recommended_interval_days = ?,
                recommended_cost = ?,
                is_active = ?
            WHERE maintenance_id = ?
        ");

        $stmt->execute([
            $_POST['maintenance_code'] ?? null,
            $_POST['maintenance_type'],
            $_POST['maintenance_description'] ?? null,
            $_POST['recommended_interval_miles'] ?: null,
            $_POST['recommended_interval_days'] ?: null,
            $_POST['recommended_cost'] ?: null,
            isset($_POST['is_active']) ? 1 : 0,
            $_POST['maintenance_id']
        ]);

        $feedback = [
            'type' => 'success',
            'message' => 'Maintenance type updated successfully.'
        ];

    } catch (Exception $e) {
        $feedback = [
            'type' => 'danger',
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}
