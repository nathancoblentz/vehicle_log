<?php
// Handler: Update Vehicle

if (!empty($_POST['update_vehicle'])) {

    global $db;

    try {
        $stmt = $db->prepare("
            UPDATE vehicles SET
                vehicle_type = ?,
                vehicle_make = ?,
                vehicle_model = ?,
                vehicle_year = ?,
                vehicle_year_purchased = ?,
                vehicle_color = ?,
                vehicle_VIN = ?,
                vehicle_license_tag = ?,
                vehicle_license_state = ?,
                vehicle_purchase_price = ?,
                vehicle_purchase_mileage = ?,
                vehicle_current_mileage = ?,
                is_active = ?
            WHERE vehicle_id = ?
        ");

        $stmt->execute([
            $_POST['vehicle_type'],
            $_POST['vehicle_make'],
            $_POST['vehicle_model'],
            $_POST['vehicle_year'],
            $_POST['vehicle_year_purchased'] ?: null,
            $_POST['vehicle_color'] ?? null,
            $_POST['vehicle_VIN'],
            $_POST['vehicle_license_tag'] ?? null,
            $_POST['vehicle_license_state'] ?? null,
            $_POST['vehicle_purchase_price'] ?: null,
            $_POST['vehicle_purchase_mileage'] ?: null,
            $_POST['vehicle_current_mileage'] ?: null,
            isset($_POST['is_active']) ? 1 : 0,
            $_POST['vehicle_id']
        ]);

        $feedback = [
            'type' => 'success',
            'message' => 'Vehicle updated successfully.'
        ];

    } catch (PDOException $e) {
        $feedback = [
            'type' => 'danger',
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}
