# Vehicle Log Database Schema

This directory contains the database setup scripts for the Vehicle Log application.

## Files

- **`vehicle_log.sql`**: The primary database schema definition file. It drops existing tables and recreates them with all relationships, constraints, and triggers.
- **`populate.sql`**: Contains sample seed data to populate the tables for testing and development.
- **`reset_auto_increments.sql`**: A utility script to reset `AUTO_INCREMENT` values if needed.

## Database Tables

The database consists of six core tables:

1. **`users`**: Stores system users and their roles.
2. **`vehicles`**: Stores vehicle information, purchase details, and current mileage. Links to `users` via `assigned_user_id`.
3. **`maintenance_type`**: A lookup table defining types of maintenance (e.g., Oil Change, Tire Rotation) and their recommended intervals in miles/days.
4. **`vendors`**: Stores contact information for maintenance vendors or repair shops.
5. **`maintenance`**: Records individual maintenance events for a vehicle. Links to `maintenance_type`, `vehicles`, and `vendors`.
6. **`fuel`**: Records fuel purchases for a vehicle. Links to `vehicles`.

## Advanced Data Integrity Enhancements

The schema leverages InnoDB engine features to strongly enforce data integrity at the database level:

### 1. Foreign Key Cascades & Restrictions
- Deleting a `vehicle` automatically deletes all of its `fuel` and `maintenance` records (`ON DELETE CASCADE`).
- Deleting a `user` sets the `assigned_user_id` on their vehicles to `NULL` (`ON DELETE SET NULL`), preserving the vehicle data.
- Attempting to delete a `maintenance_type` that is currently used in the `maintenance` table will be blocked (`ON DELETE RESTRICT`).

### 2. ENUM Data Types
Several columns strictly limit inputs to specific string values to prevent typos or invalid formats from the application tier:
- `users.user_role`: Must be either `'admin'` or `'user'`.
- `maintenance.maintenance_status`: Must be `'completed'`, `'pending'`, or `'overdue'`.
- `fuel.fuel_payment_method`: Must be `'Cash'`, `'Credit Card'`, `'Debit Card'`, or `'Fleet Card'`.

### 3. CHECK Constraints
MySQL `CHECK` constraints prevent impossible mathematical data from being saved:
- **No Negative Costs:** `vehicle_purchase_price >= 0`, `recommended_cost >= 0`, `maintenance_cost >= 0`, `fuel_cost_per_gallon >= 0`.
- **Valid Mileage:** `fuel_mileage >= 0`, `maintenance_mileage >= 0`, and `vehicle_current_mileage >= vehicle_purchase_mileage`.
- **Valid Gallons:** `fuel_gallons > 0` (cannot insert a fuel record for 0 gallons).
- **Time Constraints:** `vehicle_year_purchased >= vehicle_year` (cannot buy a car before its model year).

### 4. Database Triggers (Automatic Mileage Sync)
The dashboard odometer is designed to be a living number that never goes backwards. Two MySQL triggers run automatically:
- **`after_fuel_insert`**: Upon inserting a fuel log, if the `fuel_mileage` is higher than the `vehicle_current_mileage`, the vehicle's current mileage is automatically updated.
- **`after_maintenance_insert`**: Same logic, but triggers when a maintenance log is added.

### 5. Indexing for Query Performance
Indexes provide hyper-fast lookup maps for the database engine.
- `idx_maintenance_date`: Added to `maintenance.maintenance_date` to vastly speed up reports calculating upcoming maintenance.
- `idx_fuel_date`: Added to `fuel.fuel_date` to speed up historical fuel reporting and graphing.
