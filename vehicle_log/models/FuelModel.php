<?php

/**
 * FuelModel.php
 * 
 * Centralizes all direct SQL queries relating to fuel records.
 */
class FuelModel {

    /**
     * Gets fuel logs dynamically filtered by various bounds.
     * 
     * @param PDO $db The database connection
     * @param array $filters Array of conditions mapping to values
     * @return array Array of associative arrays representing fuel records
     */
    public static function getFilteredFuel(PDO $db, array $filters): array {
        
        $conditions = [];
        $params = [];

        // Keyword search across multiple fields
        $conditions[] = "(vehicle_make LIKE :s
                              OR vehicle_model LIKE :s
                              OR vehicle_year LIKE :s
                              OR vehicle_color LIKE :s
                              OR vehicle_type LIKE :s
                              OR fuel_source LIKE :s
                              OR fuel_gallons LIKE :s
                              OR fuel_payment_method LIKE :s
                              OR fuel_cost_per_gallon LIKE :s)";

        // Bind the search string for all fields                              
        $params[':s'] = "%" . ($filters['search_string'] ?? '') . "%";

        // Date range filtering
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $conditions[] = "fuel.fuel_date BETWEEN :start_date AND :end_date";
            $params[':start_date'] = $filters['start_date'];
            $params[':end_date'] = $filters['end_date'];
        } elseif (!empty($filters['start_date'])) {
            $conditions[] = "fuel.fuel_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        } elseif (!empty($filters['end_date'])) {
            $conditions[] = "fuel.fuel_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        // Cost range filtering
        if ($filters['min_cost'] !== '' && $filters['max_cost'] !== '') {
            $conditions[] = '(fuel.fuel_cost_per_gallon * fuel.fuel_gallons) BETWEEN :min_cost AND :max_cost';
            $params[':min_cost'] = $filters['min_cost'];
            $params[':max_cost'] = $filters['max_cost'];
        }

        // Base query with calculated fuel cost and formatted date
        $query = "
                SELECT vehicles.*, fuel.*,
                    (fuel.fuel_cost_per_gallon * fuel.fuel_gallons) AS fuel_cost,
                    CONCAT(
                    vehicle_year, ' ',
                    vehicle_make, ' ',
                    vehicle_model, '   (',
                    LOWER(vehicle_color), ' ',
                    LOWER(vehicle_type), ')')
                    AS vehicle_full,
                    DATE_FORMAT(fuel.fuel_date, '%b %e, %Y') AS fuel_date_formatted
                FROM vehicles
                JOIN fuel ON vehicles.vehicle_id = fuel.vehicle_id
            ";

        // Append conditions if any
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        // Order by most recent fuel date
        $query .= " ORDER BY fuel.fuel_date DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $vehicles;
    }

    // Basic CRUD operations
        
    public static function addFuel(PDO $db, array $bindings): void {
        $sql = "INSERT INTO fuel (
                    vehicle_id, fuel_date, fuel_source, fuel_gallons,
                    fuel_cost_per_gallon, fuel_mileage, fuel_payment_method,
                    fuel_notes, fuel_receipt_url
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $db->prepare($sql)->execute($bindings);
    }

    public static function updateFuel(PDO $db, array $bindings): void {
        $sql = "UPDATE fuel SET
                    vehicle_id           = ?,
                    fuel_date            = ?,
                    fuel_source          = ?,
                    fuel_gallons         = ?,
                    fuel_cost_per_gallon = ?,
                    fuel_mileage         = ?,
                    fuel_payment_method  = ?,
                    fuel_notes           = ?,
                    fuel_receipt_url     = ?
                WHERE fuel_id = ?";
        $db->prepare($sql)->execute($bindings);
    }

    public static function deactivateFuel(PDO $db, int $id): int {
        $stmt = $db->prepare("SELECT vehicle_id FROM fuel WHERE fuel_id = ?");
        $stmt->execute([$id]);
        $vehicleId = (int) $stmt->fetchColumn();

        $db->prepare("UPDATE fuel SET is_active = 0 WHERE fuel_id = ?")->execute([$id]);
        return $vehicleId;
    }

    public static function deleteFuel(PDO $db, int $id): int {
        $stmt = $db->prepare("SELECT vehicle_id FROM fuel WHERE fuel_id = ?");
        $stmt->execute([$id]);
        $vehicleId = (int) $stmt->fetchColumn();

        $db->prepare("DELETE FROM fuel WHERE fuel_id = ?")->execute([$id]);
        return $vehicleId;
    }

    public static function syncVehicleMileage(PDO $db, int $vehicleId): void {
        $db->prepare("
            UPDATE vehicles
            SET vehicle_current_mileage = GREATEST(
                vehicle_purchase_mileage,
                IFNULL((SELECT MAX(maintenance_mileage) FROM maintenance WHERE vehicle_id = :id AND is_active = 1), 0),
                IFNULL((SELECT MAX(fuel_mileage)        FROM fuel        WHERE vehicle_id = :id AND is_active = 1), 0)
            )
            WHERE vehicle_id = :id
        ")->execute(['id' => $vehicleId]);
    }
}
