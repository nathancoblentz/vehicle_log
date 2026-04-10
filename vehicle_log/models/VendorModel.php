<?php

/**
 * VendorModel.php
 * 
 * Centralizes all direct SQL queries relating to vendors.
 */
class VendorModel {

    /**
     * Gets all vendors, optionally filtered by a broad search string against multiple columns.
     * Includes an aggregated usage count for maintenance records.
     * 
     * @param PDO $db The database connection
     * @param string $search_string The keyword to broadly search for
     * @return array Array of associative arrays representing vendors
     */
    public static function getVendorDropdowns(PDO $db): array {
        $stmt = $db->query("SELECT vendor_id, vendor_name FROM vendors WHERE is_active = 1 ORDER BY vendor_name");
        $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $vendors;
    }

    /**
     * Gets all vendors, optionally filtered by a broad search string against multiple columns.
     */
    public static function getFilteredVendors(PDO $db, string $search_string): array {
        $query = "SELECT v.*,
                        (SELECT COUNT(*) FROM maintenance m WHERE m.vendor_id = v.vendor_id) AS usage_count
                      FROM vendors v
                      WHERE v.vendor_name LIKE :s
                         OR v.vendor_address LIKE :s
                         OR v.vendor_city LIKE :s
                         OR v.vendor_state LIKE :s
                         OR v.vendor_zip LIKE :s
                         OR v.vendor_phone LIKE :s
                         OR v.vendor_email LIKE :s
                      ORDER BY v.vendor_name ASC";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':s', '%' . $search_string . '%');
        $stmt->execute();
        $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $vendors;
    }

    public static function addVendor(PDO $db, array $bindings): void {
        $sql = "INSERT INTO vendors (
                    vendor_name, vendor_address, vendor_city, vendor_state,
                    vendor_zip, vendor_phone, vendor_email
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $db->prepare($sql)->execute($bindings);
    }

    public static function updateVendor(PDO $db, array $bindings): void {
        $sql = "UPDATE vendors SET
                    vendor_name    = ?,
                    vendor_address = ?,
                    vendor_city    = ?,
                    vendor_state   = ?,
                    vendor_zip     = ?,
                    vendor_phone   = ?,
                    vendor_email   = ?,
                    is_active      = ?
                WHERE vendor_id = ?";
        $db->prepare($sql)->execute($bindings);
    }

    public static function deactivateVendor(PDO $db, int $id): void {
        $db->prepare("UPDATE vendors SET is_active = 0 WHERE vendor_id = ?")->execute([$id]);
    }

    public static function getMaintenanceCount(PDO $db, int $id): int {
        $check = $db->prepare("SELECT COUNT(*) FROM maintenance WHERE vendor_id = ?");
        $check->execute([$id]);
        return (int) $check->fetchColumn();
    }

    public static function deleteVendor(PDO $db, int $id): void {
        $db->prepare("DELETE FROM vendors WHERE vendor_id = ?")->execute([$id]);
    }
}
