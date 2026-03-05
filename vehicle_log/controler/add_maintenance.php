<?php


if (!empty($_POST['add_maintenance'])) {

    global $db;

    try {
        $stmt = $db->prepare("
            INSERT INTO maintenance (
                vehicle_id,
                maintenance_type_id,
                vendor_id,
                maintenance_description,
                maintenance_cost,
                maintenance_mileage,
                maintenance_date,
                maintenance_status
                
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

        $stmt->execute([
            $_POST['vehicle_id'],
            $_POST['maintenance_type_id'],
            $_POST['vendor_id'] ?: null,
            $_POST['maintenance_description'] ?? null,
            $_POST['maintenance_cost'] ?: null,
            $_POST['maintenance_mileage'] ?: null,
            $_POST['maintenance_date'] ?: null,
            $_POST['maintenance_status'] ?? null
            // $_POST['maintenance_notes'] ?? null
        ]);

        // Update the vehicle's current mileage if the parsed maintenance mileage is higher
        if (!empty($_POST['maintenance_mileage'])) {
            $updateStmt = $db->prepare("
                UPDATE vehicles 
                SET vehicle_current_mileage = ? 
                WHERE vehicle_id = ? AND vehicle_current_mileage < ?
            ");
            $updateStmt->execute([
                $_POST['maintenance_mileage'],
                $_POST['vehicle_id'],
                $_POST['maintenance_mileage']
            ]);
        }

        $feedback = [
            'type' => 'success',
            'message' => 'Maintenance record added successfully.'
        ];

    } catch (PDOException $e) {

        $feedback = [
            'type' => 'danger',
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}
