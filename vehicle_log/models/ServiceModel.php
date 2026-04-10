<?php

/**
 * ServiceModel.php
 * 
 * Centralizes all direct SQL queries relating to services.
 */
class ServiceModel {

    /**
     * Gets all services, optionally filtered by a broad search string against multiple columns.
     * Includes an aggregated usage count for maintenance records.
     * 
     * @param PDO $db The database connection
     * @param string $search_string The keyword to broadly search for
     * @return array Array of associative arrays representing services
     */
    public static function getServiceDropdowns(PDO $db): array {
        $stmt = $db->query("SELECT maintenance_id, maintenance_type, recommended_cost, maintenance_description FROM maintenance_type ORDER BY maintenance_type");
        $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $types;
    }

    public static function getFilteredTypes(PDO $db, string $search_string): array {
        $query = "SELECT mt.*,
                        (SELECT COUNT(*) FROM maintenance m WHERE m.maintenance_type_id = mt.maintenance_id) AS usage_count
                      FROM maintenance_type mt
                      WHERE mt.maintenance_code LIKE :s
                         OR mt.maintenance_type LIKE :s
                         OR mt.maintenance_description LIKE :s
                      ORDER BY mt.maintenance_type ASC";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':s', '%' . $search_string . '%');
        $stmt->execute();
        $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $types;
    }

    public static function addMaintenanceType(PDO $db, array $bindings): void {
        $sql = "INSERT INTO maintenance_type (
                    maintenance_code, maintenance_type, maintenance_description,
                    recommended_interval_miles, recommended_interval_days, recommended_cost
                ) VALUES (?, ?, ?, ?, ?, ?)";
        $db->prepare($sql)->execute($bindings);
    }

    public static function updateMaintenanceType(PDO $db, array $bindings): void {
        $sql = "UPDATE maintenance_type SET
                    maintenance_code           = ?,
                    maintenance_type           = ?,
                    maintenance_description    = ?,
                    recommended_interval_miles = ?,
                    recommended_interval_days  = ?,
                    recommended_cost           = ?,
                    is_active                  = ?
                WHERE maintenance_id = ?";
        $db->prepare($sql)->execute($bindings);
    }

    public static function deactivateMaintenanceType(PDO $db, int $id): void {
        $db->prepare("UPDATE maintenance_type SET is_active = 0 WHERE maintenance_id = ?")->execute([$id]);
    }

    public static function getUsageCount(PDO $db, int $id): int {
        $check = $db->prepare("SELECT COUNT(*) FROM maintenance WHERE maintenance_type_id = ?");
        $check->execute([$id]);
        return (int) $check->fetchColumn();
    }

    public static function deleteMaintenanceType(PDO $db, int $id): void {
        $db->prepare("DELETE FROM maintenance_type WHERE maintenance_id = ?")->execute([$id]);
    }
}
