<?php

/**
 * MaintenanceModel.php
 * 
 * Centralizes all direct SQL queries relating to maintenance records.
 */
class MaintenanceModel {

    /**
     * Gets maintenance logs dynamically filtered by various bounds.
     * 
     * @param PDO $db The database connection
     * @param array $filters Array of conditions mapping to values
     * @return array Array of associative arrays representing maintenance records
     */
    public static function getFilteredMaintenance(PDO $db, array $filters): array {
        
        $conditions = [];
        $params = [];

        // Keyword search
        if (!empty($filters['search_string'])) {
            $conditions[] = "(vehicle_make LIKE :s
                                  OR vehicle_model LIKE :s
                                  OR vehicle_year LIKE :s
                                  OR vehicle_color LIKE :s
                                  OR vehicle_type LIKE :s
                                  OR v.vendor_name LIKE :s
                                  OR maintenance_description LIKE :s
                                  OR maintenance_status LIKE :s)";
            $params[':s'] = "%" . $filters['search_string'] . "%";
        }

        // Date range
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $conditions[] = "maintenance.maintenance_date BETWEEN :start_date AND :end_date";
            $params[':start_date'] = $filters['start_date'];
            $params[':end_date'] = $filters['end_date'];
        } elseif (!empty($filters['start_date'])) {
            $conditions[] = "maintenance.maintenance_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        } elseif (!empty($filters['end_date'])) {
            $conditions[] = "maintenance.maintenance_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        // Cost range
        if ($filters['min_cost'] !== '' && $filters['max_cost'] !== '') {
            $conditions[] = "maintenance.maintenance_cost BETWEEN :min_cost AND :max_cost";
            $params[':min_cost'] = $filters['min_cost'];
            $params[':max_cost'] = $filters['max_cost'];
        } elseif ($filters['min_cost'] !== '') {
            $conditions[] = "maintenance.maintenance_cost >= :min_cost";
            $params[':min_cost'] = $filters['min_cost'];
        } elseif ($filters['max_cost'] !== '') {
            $conditions[] = "maintenance.maintenance_cost <= :max_cost";
            $params[':max_cost'] = $filters['max_cost'];
        }

        $query = "
                SELECT maintenance.*, maintenance.maintenance_mileage,
                    CONCAT(
                        vehicle_year, ' ',
                        vehicle_make, ' ',
                        vehicle_model, ' (',
                        LOWER(vehicle_color), ' ',
                        LOWER(vehicle_type), ')'
                    ) AS vehicle_full,
                    v.vendor_name,
                    mt.maintenance_type AS type_name,
                    DATE_FORMAT(maintenance.maintenance_date, '%b %e, %Y') AS maintenance_date_formatted
                FROM vehicles
                JOIN maintenance ON vehicles.vehicle_id = maintenance.vehicle_id
                LEFT JOIN vendors v ON v.vendor_id = maintenance.vendor_id
                LEFT JOIN maintenance_type mt ON mt.maintenance_id = maintenance.maintenance_type_id
            ";

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY maintenance.maintenance_date DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $maintenance = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $maintenance;
    }

    public static function addMaintenance(PDO $db, array $bindings): void {
        $sql = "INSERT INTO maintenance (
                    vehicle_id, maintenance_type_id, vendor_id,
                    maintenance_description, maintenance_cost, maintenance_mileage,
                    maintenance_date, maintenance_status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $db->prepare($sql)->execute($bindings);
    }

    public static function updateMaintenance(PDO $db, array $bindings): void {
        $sql = "UPDATE maintenance SET
                    vehicle_id              = ?,
                    maintenance_type_id     = ?,
                    vendor_id               = ?,
                    maintenance_description = ?,
                    maintenance_cost        = ?,
                    maintenance_mileage     = ?,
                    maintenance_date        = ?,
                    maintenance_status      = ?
                WHERE maintenance_id = ?";
        $db->prepare($sql)->execute($bindings);
    }

    public static function deactivateMaintenance(PDO $db, int $id): int {
        $stmt = $db->prepare("SELECT vehicle_id FROM maintenance WHERE maintenance_id = ?");
        $stmt->execute([$id]);
        $vehicleId = (int) $stmt->fetchColumn();

        $db->prepare("UPDATE maintenance SET is_active = 0 WHERE maintenance_id = ?")->execute([$id]);
        return $vehicleId;
    }

    public static function deleteMaintenance(PDO $db, int $id): int {
        $stmt = $db->prepare("SELECT vehicle_id FROM maintenance WHERE maintenance_id = ?");
        $stmt->execute([$id]);
        $vehicleId = (int) $stmt->fetchColumn();

        $db->prepare("DELETE FROM maintenance WHERE maintenance_id = ?")->execute([$id]);
        return $vehicleId;
    }

    public static function syncMaintenanceMileage(PDO $db, int $vehicleId): void {
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
