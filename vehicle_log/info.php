<?php
/*
    Jonathan Coblentz
    CPT283: PHP Programming
    Final Project: Vehicle Maintenance Log

    Generic Info Page — Consolidates vehicle, service, and vendor reporting.
    Uses vertical tabbed navigation similar to table.php.
*/

require 'config.php';
$title = 'Record View';
$hideNav = true; 
$requireAuth = true;
require_once 'includes/header_bundle.php';
include_once 'includes/functions.php';

addHandlers();

/* ------------------------------
   FETCH ALL LISTS (FOR DROPDOWNS)
------------------------------ */

// Vehicles
$stmt = $db->query("SELECT vehicle_id, vehicle_year, vehicle_make, vehicle_model FROM vehicles ORDER BY vehicle_year DESC, vehicle_make, vehicle_model");
$all_vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Services
$stmt = $db->query("SELECT maintenance_id, maintenance_type, maintenance_code FROM maintenance_type ORDER BY maintenance_type");
$all_services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Maintenance (The Jobs/Logs)
$stmt = $db->query("SELECT m.maintenance_id, m.maintenance_date, mt.maintenance_type, v.vehicle_year, v.vehicle_make, v.vehicle_model
                    FROM maintenance m
                    JOIN maintenance_type mt ON m.maintenance_type_id = mt.maintenance_id
                    JOIN vehicles v ON m.vehicle_id = v.vehicle_id
                    ORDER BY m.maintenance_date DESC");
$all_maintenance_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fuel (The Fuel Ups)
$stmt = $db->query("SELECT f.fuel_id, f.fuel_date, v.vehicle_year, v.vehicle_make, v.vehicle_model, f.fuel_gallons
                    FROM fuel f
                    JOIN vehicles v ON f.vehicle_id = v.vehicle_id
                    ORDER BY f.fuel_date DESC");
$all_fuel_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vendors
$stmt = $db->query("SELECT vendor_id, vendor_name FROM vendors ORDER BY vendor_name");
$all_vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* ------------------------------
   DATA FETCHING FOR SELECTED ITEMS
------------------------------ */

// 1. VEHICLE DATA
$vehicle_id = $_GET['vehicle_id'] ?? null;
$vehicle = null;
$fuel_logs = [];
$maintenance_logs_v = [];

if ($vehicle_id) {
    $stmt = $db->prepare("SELECT * FROM vehicles WHERE vehicle_id = :vid");
    $stmt->execute([':vid' => $vehicle_id]);
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vehicle) {
        $stmt = $db->prepare("SELECT *, (fuel_gallons * fuel_cost_per_gallon) AS fuel_cost, DATE_FORMAT(fuel_date, '%b %e, %Y') AS fuel_date_formatted 
                              FROM fuel WHERE vehicle_id = :vid ORDER BY fuel_date DESC");
        $stmt->execute([':vid' => $vehicle_id]);
        $fuel_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT m.*, mt.maintenance_type AS type_name, v.vendor_name,
                              DATE_FORMAT(maintenance_date, '%b %e, %Y') AS maintenance_date_formatted 
                              FROM maintenance m
                              LEFT JOIN maintenance_type mt ON m.maintenance_type_id = mt.maintenance_id
                              LEFT JOIN vendors v ON v.vendor_id = m.vendor_id
                              WHERE m.vehicle_id = :vid ORDER BY maintenance_date DESC");
        $stmt->execute([':vid' => $vehicle_id]);
        $maintenance_logs_v = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// 2. SERVICE DATA
$service_id = $_GET['service_id'] ?? null;
$service_info = null;
$maintenance_logs_serv = [];
$total_cost_serv = 0;

if ($service_id) {
    $stmt = $db->prepare("SELECT * FROM maintenance_type WHERE maintenance_id = :mid");
    $stmt->execute([':mid' => $service_id]);
    $service_info = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($service_info) {
        $stmt = $db->prepare("
            SELECT m.*, v.vendor_name,
                   CONCAT(veh.vehicle_year, ' ', veh.vehicle_make, ' ', veh.vehicle_model,
                          ' (', LOWER(veh.vehicle_color), ' ', LOWER(veh.vehicle_type), ')') AS vehicle_full,
                   DATE_FORMAT(m.maintenance_date, '%b %e, %Y') AS maintenance_date_formatted
            FROM maintenance m
            JOIN vehicles veh ON veh.vehicle_id = m.vehicle_id
            LEFT JOIN vendors v ON v.vendor_id = m.vendor_id
            WHERE m.maintenance_type_id = :mid
            ORDER BY m.maintenance_date DESC
        ");
        $stmt->execute([':mid' => $service_id]);
        $maintenance_logs_serv = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total_cost_serv = array_sum(array_column($maintenance_logs_serv, 'maintenance_cost'));
    }
}

// 3. VENDOR DATA
$vendor_id = $_GET['vendor_id'] ?? null;
$vendor = null;
$maintenance_logs_vend = [];
$total_cost_vend = 0;

if ($vendor_id) {
    $stmt = $db->prepare("SELECT * FROM vendors WHERE vendor_id = :vid");
    $stmt->execute([':vid' => $vendor_id]);
    $vendor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vendor) {
        $stmt = $db->prepare("
            SELECT m.*, mt.maintenance_type AS type_name,
                   CONCAT(v.vehicle_year, ' ', v.vehicle_make, ' ', v.vehicle_model,
                          ' (', LOWER(v.vehicle_color), ' ', LOWER(v.vehicle_type), ')') AS vehicle_full,
                   DATE_FORMAT(m.maintenance_date, '%b %e, %Y') AS maintenance_date_formatted
            FROM maintenance m
            JOIN vehicles v ON v.vehicle_id = m.vehicle_id
            LEFT JOIN maintenance_type mt ON mt.maintenance_id = m.maintenance_type_id
            WHERE m.vendor_id = :vid
            ORDER BY m.maintenance_date DESC
        ");
        $stmt->execute([':vid' => $vendor_id]);
        $maintenance_logs_vend = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total_cost_vend = array_sum(array_column($maintenance_logs_vend, 'maintenance_cost'));
    }
}

// 4. MAINTENANCE LOG DATA (Specific drill-down)
$log_id = $_GET['log_id'] ?? null;
$maint_log_detail = null;
if ($log_id) {
    $stmt = $db->prepare("SELECT m.*, mt.maintenance_type AS type_name, mt.maintenance_code,
                                 v.vendor_name, v.vendor_phone,
                                 CONCAT(veh.vehicle_year, ' ', veh.vehicle_make, ' ', veh.vehicle_model) AS vehicle_full,
                                 DATE_FORMAT(m.maintenance_date, '%b %e, %Y') AS maintenance_date_formatted
                          FROM maintenance m
                          JOIN vehicles veh ON veh.vehicle_id = m.vehicle_id
                          JOIN maintenance_type mt ON mt.maintenance_id = m.maintenance_type_id
                          LEFT JOIN vendors v ON v.vendor_id = m.vendor_id
                          WHERE m.maintenance_id = :lid");
    $stmt->execute([':lid' => $log_id]);
    $maint_log_detail = $stmt->fetch(PDO::FETCH_ASSOC);
}

// 5. FUEL LOG DATA (Specific drill-down)
$fuel_id_selection = $_GET['f_id'] ?? null; // using f_id to avoid conflict with tab names
$fuel_entry_detail = null;
if ($fuel_id_selection) {
    $stmt = $db->prepare("SELECT f.*, (f.fuel_gallons * f.fuel_cost_per_gallon) AS fuel_total,
                                 CONCAT(veh.vehicle_year, ' ', veh.vehicle_make, ' ', veh.vehicle_model) AS vehicle_full,
                                 DATE_FORMAT(f.fuel_date, '%b %e, %Y') AS fuel_date_formatted
                          FROM fuel f
                          JOIN vehicles veh ON veh.vehicle_id = f.vehicle_id
                          WHERE f.fuel_id = :fid");
    $stmt->execute([':fid' => $fuel_id_selection]);
    $fuel_entry_detail = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* ------------------------------
   TAB CONFIGURATION
------------------------------ */

$tabs = [
    "vehicles" => [
        "label" => "Vehicles",
        "icon" => "fa-car-side",
        "file" => "view/partials/_vehicle_info.php",
        "data" => [
            "vehicle" => $vehicle,
            "fuel_logs" => $fuel_logs,
            "maintenance_logs" => $maintenance_logs_v,
            "all_vehicles" => $all_vehicles,
            "vehicle_id" => $vehicle_id
        ]
    ],
    "maintenance" => [
        "label" => "Maintenance Logs",
        "icon" => "fa-wrench",
        "file" => "view/partials/_maintenance_info.php",
        "data" => [
            "maint_log_detail" => $maint_log_detail,
            "all_maintenance_logs" => $all_maintenance_logs,
            "log_id" => $log_id
        ]
    ],
    "vendors" => [
        "label" => "Vendors",
        "icon" => "fa-store",
        "file" => "view/partials/_vendor_info.php",
        "data" => [
            "vendor" => $vendor,
            "maintenance_logs" => $maintenance_logs_vend,
            "total_cost" => $total_cost_vend,
            "all_vendors" => $all_vendors,
            "vendor_id" => $vendor_id
        ]
    ],
    "fuel" => [
        "label" => "Fuel Logs",
        "icon" => "fa-gas-pump",
        "file" => "view/partials/_fuel_info.php",
        "data" => [
            "fuel_entry_detail" => $fuel_entry_detail,
            "all_fuel_entries" => $all_fuel_entries,
            "f_id" => $fuel_id_selection
        ]
    ],
    "services" => [
        "label" => "Services",
        "icon" => "fa-screwdriver-wrench",
        "file" => "view/partials/_service_info.php",
        "data" => [
            "service_info" => $service_info,
            "maintenance_logs" => $maintenance_logs_serv,
            "total_cost" => $total_cost_serv,
            "all_services" => $all_services,
            "service_id" => $service_id
        ]
    ],
];

// Determine the active tab from URL or default to vehicles
$active_tab_id = $_GET['tab'] ?? 'vehicles';

?>

<div class="container mt-4">
<?php
$headerTitle   = 'Record View';
$headerIcon    = 'fa-circle-info';
$headerActive  = 'info';
$headerListUrl = 'table.php';
$headerInfoUrl = 'info.php';
$headerBadge   = '';
include __DIR__ . '/view/partials/_page_header.php';
?>

    <div class="d-flex align-items-start">
        <!-- =========================
             VERTICAL NAVIGATION
        ========================= -->
        <?php include __DIR__ . '/view/partials/_vertical_tabs.php'; ?>

        <!-- =========================
             TAB CONTENT
        ========================= -->
        <div class="tab-content w-100" id="v-pills-tabContent">
            <?php foreach ($tabs as $id => $tab): 
                $active = ($id === $active_tab_id) ? "show active" : "";
                
                // Extract data for the partial
                foreach ($tab['data'] as $key => $value) {
                    ${$key} = $value;
                }
            ?>
                <div
                    class="tab-pane fade <?= $active ?>"
                    id="v-pills-<?= $id ?>"
                    role="tabpanel"
                    aria-labelledby="v-pills-<?= $id ?>-tab"
                    tabindex="0"
                >
                    <?php include($tab['file']); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- =========================
     FORM MODALS & SCRIPTS
========================= -->
<?php addForms(); ?>
<?php addFeedback(); ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        /* -------------------------
           Load tab from URL hash
        ------------------------- */
        let hash = window.location.hash;
        if (hash) {
            let targetButton = document.querySelector('button[data-bs-target="' + hash + '"]');
            if (targetButton) {
                let tab = new bootstrap.Tab(targetButton);
                tab.show();
            }
        }

        /* -------------------------
           Update URL when tab changes
        ------------------------- */
        let tabButtons = document.querySelectorAll('button[data-bs-toggle="pill"]');
        tabButtons.forEach(function (button) {
            button.addEventListener('shown.bs.tab', function (e) {
                let activeTarget = e.target.getAttribute('data-bs-target');
                if (activeTarget) {
                    history.replaceState(null, null, activeTarget);
                }
            });
        });
    });
</script>

<?php include_once('../includes/footer.php'); ?>
