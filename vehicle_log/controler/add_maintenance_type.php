<?php
// Handler: Add Maintenance Type

if (!empty($_POST['add_maintenance_type'])) {

    global $db;

    try {

        $stmt = $db->prepare("
        INSERT INTO maintenance_type (
        maintenance_code,
        maintenance_type,
        maintenance_description,
        recommended_interval_miles,
        recommended_interval_days,
        recommended_cost
        ) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

        $stmt->execute([
            $_POST['maintenance_code'],
            $_POST['maintenance_type'],
            $_POST['maintenance_description'],
            $_POST['recommended_interval_miles'],
            $_POST['recommended_interval_days'],
            $_POST['recommended_cost'] ?: null
        ]);

        $feedback = [
            'type' => 'success',
            'message' => 'Maintenance type added successfully.'
        ];

    } catch (Exception $e) {
        $feedback = [
            'type' => 'danger',
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}

