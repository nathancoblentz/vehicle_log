<?php

/**
 * UserModel.php
 * 
 * Centralizes all direct SQL queries relating to system users.
 */
class UserModel {

    /**
     * Gets all users ordered alphabetically.
     * 
     * @param PDO $db The database connection
     * @return array Array of associative arrays representing users
     */
    public static function getAllUsers(PDO $db): array {
        $query = "SELECT * FROM users ORDER BY last_name ASC, first_name ASC";
        $stmt = $db->query($query);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $users;
    }

    public static function isEmailTaken(PDO $db, string $email, int $excludeId = 0): bool {
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND user_id != ?");
        $stmt->execute([$email, $excludeId]);
        return (bool) $stmt->fetchColumn();
    }

    public static function addUser(PDO $db, array $bindings): void {
        $db->prepare("
            INSERT INTO users (first_name, last_name, email, user_password, user_role, is_active)
            VALUES (?, ?, ?, ?, ?, 1)
        ")->execute($bindings);
    }

    public static function updateUserWithPassword(PDO $db, array $bindings): void {
        $db->prepare("
            UPDATE users
            SET first_name = ?, last_name = ?, email = ?, user_role = ?, is_active = ?, user_password = ?
            WHERE user_id = ?
        ")->execute($bindings);
    }

    public static function updateUserWithoutPassword(PDO $db, array $bindings): void {
        $db->prepare("
            UPDATE users
            SET first_name = ?, last_name = ?, email = ?, user_role = ?, is_active = ?
            WHERE user_id = ?
        ")->execute($bindings);
    }

    public static function deactivateUser(PDO $db, int $id): void {
        $db->prepare("UPDATE users SET is_active = 0 WHERE user_id = ?")->execute([$id]);
    }

    public static function deleteUser(PDO $db, int $id): void {
        $db->prepare("DELETE FROM users WHERE user_id = ?")->execute([$id]);
    }
}
