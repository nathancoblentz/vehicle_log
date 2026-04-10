<?php
/**
 * admin/index.php - Administrative Access Points
 * 
 * Instructions:
 * 1. Log in to your virtual host Control Panel.
 * 2. Navigate to vehicle_log/admin and ensure security is enabled.
 * 3. Use the centralized 'requireAdmin' guard to restrict this view.
 */

// ── SECURITY GUARD ───────────────────────────────────────────
require_once '../includes/init.php';
requireAdmin('../login.php');

$title = 'Admin Table View';
$hideNav = true;
require_once '../includes/header_bundle.php';
?>


<div class="container mt-4">
<?php
$headerTitle   = 'List View';
$headerIcon    = 'fa-list';
$headerActive  = 'list';
$headerListUrl = 'index.php';
$headerInfoUrl = 'info.php';
$headerBadge   = 'ADMIN';
include __DIR__ . '/../view/partials/_page_header.php';
?>
    <div class="d-flex align-items-start">

        <?php
        $tabs = [
            "vehicles" => ["label" => "Vehicles", "icon" => "fa-car-side", "file" => "../view/vehicles_table.php"],
            "maintenance" => ["label" => "Maintenance Logs", "icon" => "fa-wrench", "file" => "../view/maintenance_table.php"],
            "vendors" => ["label" => "Vendors", "icon" => "fa-store", "file" => "../view/vendors_table.php"],
            "fuel" => ["label" => "Fuel", "icon" => "fa-gas-pump", "file" => "../view/fuel_table.php"],
            "services" => ["label" => "Services", "icon" => "fa-screwdriver-wrench", "file" => "../view/service_table.php"],
            "users" => ["label" => "Users", "icon" => "fa-users", "file" => "../view/user_table.php"]
        ];

        // Allow delete button in the admin dashboard
        $allow_delete = true;

        // Remove users tab for non-admins
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            unset($tabs['users']);
        }

        $active_tab_id = $_GET['tab'] ?? array_key_first($tabs);
        ?>
        <!-- =========================
             VERTICAL NAVIGATION
        ========================= -->
        <?php include __DIR__ . '/../view/partials/_vertical_tabs.php'; ?>

        <!-- =========================
             TAB CONTENT
        ========================= -->
        <div class="tab-content w-100" id="v-pills-tabContent">
            <?php
            foreach ($tabs as $id => $tab) {
                $active = ($id === $active_tab_id) ? "show active" : "";
                echo "
                <div class='tab-pane fade $active' id='v-pills-$id' role='tabpanel' 
                     aria-labelledby='v-pills-$id-tab' tabindex='0'>";
                include($tab['file']);
                echo "</div>";
            }
            ?>
        </div>

    </div>
</div>

<!-- =========================
     FORM OVERLAYS
========================= -->
<?php addForms(); ?>
<?php addFeedback(); ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    /* Restore tab from hash */
    let hash = window.location.hash;
    if (hash) {
        let targetButton = document.querySelector('button[data-bs-target="' + hash + '"]');
        if (targetButton) {
            let tab = new bootstrap.Tab(targetButton);
            tab.show();
        }
    }

    /* Update hash on tab change */
    let tabButtons = document.querySelectorAll('button[data-bs-toggle="pill"]');
    tabButtons.forEach(function (button) {
        button.addEventListener('shown.bs.tab', function (e) {
            let activeTarget = e.target.getAttribute('data-bs-target');
            if (activeTarget) { history.replaceState(null, null, activeTarget); }
        });
    });
});

/* Deactivation Toggle Helper */
window.toggle_active = function (checkbox, countId) {
    let inactive = document.querySelectorAll("tr.text-decoration-line-through");
    inactive.forEach(row => {
        if (checkbox.checked) { row.classList.remove("d-none"); } 
        else { row.classList.add("d-none"); }
    });

    let countEl = document.getElementById(countId);
    if (countEl) {
        let count = checkbox.checked ? countEl.dataset.totalCount : countEl.dataset.activeCount;
        countEl.textContent = count + ' result(s) for "' + countEl.dataset.label + '"';
    }
};
</script>

<?php include_once('../../includes/footer.php'); ?>