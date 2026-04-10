<?php

/**
 * VehicleModel.php
 * 
 * Centralizes all direct SQL queries relating to vehicles.
 */
class VehicleModel {

    /**
     * Gets all vehicles, optionally filtered by a broad search string against multiple columns.
     * Includes aggregated counts for maintenance and fuel records.
     * 
     * @param PDO $db The database connection
     * @param string $search_string The keyword to broadly search for
     * @return array Array of associative arrays representing vehicles
     */
    public static function getVehicleDropdowns(PDO $db): array {
        $stmt = $db->query("SELECT vehicle_id, vehicle_make, vehicle_model, vehicle_year, vehicle_current_mileage FROM vehicles ORDER BY vehicle_make, vehicle_model");
        $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $vehicles;
    }

    /**
     * Gets all vehicles, optionally filtered by a broad search string against multiple columns.
     */
    public static function getFilteredVehicles(PDO $db, string $search_string): array {
        $query = "SELECT *,
                (SELECT COUNT(*) FROM maintenance m WHERE m.vehicle_id = vehicles.vehicle_id) AS maint_count,
                CONCAT(
                    vehicle_year, ' ',
                    vehicle_make, ' ',
                    vehicle_model, ' (',
                    LOWER(vehicle_color), ' ',
                    LOWER(vehicle_type), ')'
                ) AS vehicle_full,
                (SELECT COUNT(*) FROM fuel f WHERE f.vehicle_id = vehicles.vehicle_id) AS fuel_count
                FROM vehicles
                WHERE (vehicle_make LIKE :s
                OR vehicle_model LIKE :s
                OR vehicle_year LIKE :s
                OR vehicle_type LIKE :s
                OR vehicle_color LIKE :s
                OR vehicle_VIN LIKE :s
                OR vehicle_license_tag LIKE :s
                OR vehicle_license_state LIKE :s)
                ORDER BY is_active DESC, vehicle_make ASC";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':s', '%' . $search_string . '%');
        $stmt->execute();
        $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $vehicles;
    }

    public static function isVINTaken(PDO $db, string $vin, int $excludeId = 0): bool {
        $stmt = $db->prepare("SELECT COUNT(*) FROM vehicles WHERE vehicle_VIN = ? AND vehicle_id != ?");
        $stmt->execute([$vin, $excludeId]);
        return (bool) $stmt->fetchColumn();
    }

    public static function addVehicle(PDO $db, array $bindings): void {
        $sql = "INSERT INTO vehicles (
                    vehicle_type, vehicle_make, vehicle_model, vehicle_year,
                    vehicle_year_purchased, vehicle_color, vehicle_VIN,
                    vehicle_license_tag, vehicle_license_state,
                    vehicle_purchase_price, vehicle_purchase_mileage,
                    vehicle_current_mileage, is_active
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $db->prepare($sql)->execute($bindings);
    }

    public static function updateVehicle(PDO $db, array $bindings): void {
        $sql = "UPDATE vehicles SET
                    vehicle_type             = ?,
                    vehicle_make             = ?,
                    vehicle_model            = ?,
                    vehicle_year             = ?,
                    vehicle_year_purchased   = ?,
                    vehicle_color            = ?,
                    vehicle_VIN              = ?,
                    vehicle_license_tag      = ?,
                    vehicle_license_state    = ?,
                    vehicle_purchase_price   = ?,
                    vehicle_purchase_mileage = ?,
                    vehicle_current_mileage  = ?,
                    is_active                = ?
                WHERE vehicle_id = ?";
        $db->prepare($sql)->execute($bindings);
    }

    public static function deactivateVehicle(PDO $db, int $id): void {
        $db->prepare("UPDATE vehicles SET is_active = 0 WHERE vehicle_id = ?")->execute([$id]);
    }

    public static function getMaintCount(PDO $db, int $id): int {
        $stmt = $db->prepare("SELECT COUNT(*) FROM maintenance WHERE vehicle_id = ?");
        $stmt->execute([$id]);
        return (int) $stmt->fetchColumn();
    }

    public static function getFuelCount(PDO $db, int $id): int {
        $stmt = $db->prepare("SELECT COUNT(*) FROM fuel WHERE vehicle_id = ?");
        $stmt->execute([$id]);
        return (int) $stmt->fetchColumn();
    }

    public static function deleteVehicle(PDO $db, int $id): void {
        $db->prepare("DELETE FROM vehicles WHERE vehicle_id = ?")->execute([$id]);
    }
}
