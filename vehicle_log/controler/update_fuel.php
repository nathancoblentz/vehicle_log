<?php
// Handler: Update Fuel Record

if (!empty($_POST['update_fuel'])) {

    global $db;

    try {
        $stmt = $db->prepare("
            UPDATE fuel SET
                vehicle_id = ?,
                fuel_date = ?,
                fuel_source = ?,
                fuel_gallons = ?,
                fuel_cost_per_gallon = ?,
                fuel_mileage = ?,
                fuel_payment_method = ?,
                fuel_notes = ?,
                fuel_receipt_url = ?
            WHERE fuel_id = ?
        ");

        $stmt->execute([
            $_POST['vehicle_id'],
            $_POST['fuel_date'],
            $_POST['fuel_source'] ?? null,
            $_POST['fuel_gallons'],
            $_POST['fuel_cost'],
            $_POST['fuel_mileage'] ?: null,
            $_POST['fuel_payment_method'] ?? null,
            $_POST['fuel_notes'] ?? null,
            $_POST['fuel_receipt_url'] ?? null,
            $_POST['fuel_id']
        ]);

        // Update the vehicle's current mileage if the parsed fuel mileage is higher
        if (!empty($_POST['fuel_mileage'])) {
            $updateStmt = $db->prepare("
                UPDATE vehicles 
                SET vehicle_current_mileage = ? 
                WHERE vehicle_id = ? AND vehicle_current_mileage < ?
            ");
            $updateStmt->execute([
                $_POST['fuel_mileage'],
                $_POST['vehicle_id'],
                $_POST['fuel_mileage']
            ]);
        }

        $feedback = [
            'type' => 'success',
            'message' => 'Fuel record updated successfully.'
        ];

    } catch (PDOException $e) {
        $feedback = [
            'type' => 'danger',
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}
