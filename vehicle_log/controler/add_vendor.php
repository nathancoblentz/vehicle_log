<?php
// Handler: Add Vendor

if (!empty($_POST['add_vendor'])) {

    global $db;

    try {
        $stmt = $db->prepare("
            INSERT INTO vendors (
                vendor_name,
                vendor_address,
                vendor_city,
                vendor_state,
                vendor_zip,
                vendor_phone,
                vendor_email
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $_POST['vendor_name'],
            $_POST['vendor_address'] ?? null,
            $_POST['vendor_city'] ?? null,
            $_POST['vendor_state'] ?? null,
            $_POST['vendor_zip'] ?? null,
            $_POST['vendor_phone'] ?? null,
            $_POST['vendor_email'] ?? null
        ]);

        $feedback = [
            'type' => 'success',
            'message' => 'Vendor added successfully.'
        ];

    } catch (PDOException $e) {
        $feedback = [
            'type' => 'danger',
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}
