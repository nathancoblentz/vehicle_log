# Fleet Management Web Application (Vehicle Log)

Welcome to the **Fleet Management Web Application**! This project is a comprehensive, database-driven web application designed to track, manage, and analyze a fleet of vehicles, including their fuel consumption and maintenance histories. 

This project was built from the ground up to demonstrate a full-stack skill set, focusing on robust database architecture, secure backend logic, and a clean, responsive, and dynamic user interface.

## 🚀 Features

### Core Functionality
*   **Complete CRUD Operations:** Create, Read, Update, and Delete capabilities across 5 distinct data models: Vehicles, Vendors, Maintenance Types, Maintenance Records, and Fuel Records.
*   **Comprehensive Dashboards:** View aggregated data seamlessly using a Bootstrap vertical-pill tab interface that organizes thousands of records cleanly.
*   **Detailed Reporting Views:** Drill down into specific entities with dedicated detail pages (`vehicle_info.php`, `vendor_info.php`, `maintenance_type_info.php`) that show aggregated metrics (e.g., total spent at a specific vendor, lifetime maintenance cost of a specific vehicle).

### User Experience (UX) & Interface (UI)
*   **Deep Linking & Tab Memory:** Custom JavaScript integrations with the browser's History API allow users to bookmark specific dashboard tabs and comfortably use the browser's "Back" button without losing their place.
*   **Contextual Breadcrumbs:** Clean hierarchical navigation dynamically generated to prevent dead-ends.
*   **Dynamic Modals & Forms:** All data entry happens within responsive Bootstrap Modals.
*   **Smart Auto-population:** Forms utilize JavaScript and PHP-injected JSON maps to intelligently auto-populate fields (like Maintenance Descriptions or Vehicle Mileage) while still allowing user overrides.
*   **Real-time Client-side Math:** JavaScript event listeners calculate form totals (e.g., `gallons * cost_per_gallon = total`) in real-time as the user types.

### Database Architecture & Integrity
*   **Advanced Relational Schema:** Enforces strict data consistency across 6 relational tables using `ON DELETE CASCADE`, `ON DELETE SET NULL`, and `ON DELETE RESTRICT` constraints appropriately.
*   **MySQL Triggers:** The database actively maintains its own state. When a new fuel or maintenance record is inserted, a database trigger automatically updates the parent vehicle's `current_mileage` to ensure the dashboard odometer is always accurate without relying on the application tier.
*   **Table Constraints:** Uses `CHECK` constraints to prevent impossible data states (e.g., negative costs, or inserting zero gallons of fuel).
*   **Performant Indexing:** Strategic use of B-Tree indexes on commonly sorted or searched columns (like dates).

## 🛠️ Technology Stack

*   **Frontend:** HTML5, CSS3, JavaScript (ES6), Bootstrap 5, FontAwesome
*   **Backend:** PHP 8+
*   **Database:** MySQL / MariaDB (Interfaced using PHP Data Objects - PDO)
*   **Security:** Prepared SQL Statements to prevent SQL injection; strict data typing; XSS prevention via `htmlspecialchars()`.

## 📂 Project Structure

*   **/controler/** - Contains the PHP scripts that handle form submissions, database inserts/updates/deletes, and redirection logic.
*   **/view/** - Contains the HTML markup for the Bootstrap forms and modals.
*   **/includes/** - Reusable PHP components like headers, navigation bars, breadcrumbs, and table rendering functions.
*   **/data/** - Contains the SQL scripts required to construct the database schema (`vehicle_log.sql`) and populate it with realistic test data (`populate.sql`).

## ⚙️ Setup Instructions

To run this project locally:

1.  **Clone the repository** to your local machine (e.g., into the `htdocs` folder if using XAMPP).
2.  **Database Configuration:**
    *   Open your MySQL environment (like phpMyAdmin) and create a new database.
    *   Execute the `vehicle_log/data/vehicle_log.sql` script to build the schema.
    *   Execute the `vehicle_log/data/populate.sql` script to seed the database with test data.
3.  **App Configuration:**
    *   Open `vehicle_log/config.php` and update the database credentials (`$user`, `$password`, `$dbname`) to match your local environment.
4.  **Launch:** Navigate to `http://localhost/.../vehicle_log/landing_page.php` in your browser.
