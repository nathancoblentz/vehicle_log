<?php
/*
    Jonathan Coblentz
    CPT283: PHP Programming
    Final Project: Vehicle Maintenance Log

    This project is a web-based application for logging and tracking vehicle maintenance
    and fuel records. It includes user authentication, CRUD operations for vehicles,
    services, maintenance records, and fuel records.

    Lab 05 focuses on navigation.
*/

require 'config.php';
$title = 'Table View';
$hideNav = true; 
$requireAuth = true;
require_once 'includes/header_bundle.php';
include_once 'includes/functions.php';



/* ------------------------------
   PAGE HEADER
------------------------------ */

$title = 'Table View';
include_once '../includes/head.php';



/* ------------------------------
   NAVIGATION CONFIGURATION
------------------------------ */

$tabs = [
    "vehicles" => [
        "label" => "Vehicles",
        "icon" => "fa-car-side",
        "file" => "view/vehicles_table.php"
    ],

    "maintenance" => [
        "label" => "Maintenance Logs",
        "icon" => "fa-wrench",
        "file" => "view/maintenance_table.php"
    ],

    "vendors" => [
        "label" => "Vendors",
        "icon" => "fa-store",
        "file" => "view/vendors_table.php"
    ],

    "fuel" => [
        "label" => "Fuel",
        "icon" => "fa-gas-pump",
        "file" => "view/fuel_table.php"
    ],

    "services" => [
        "label" => "Services",
        "icon" => "fa-screwdriver-wrench",
        "file" => "view/service_table.php"
    ],

    "users" => [
        "label" => "Users",
        "icon" => "fa-users",
        "file" => "view/user_table.php"
    ]
];

// Hide delete button from ALL users in the standard table view
$allow_delete = false;

// Remove users tab for non-admins
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    unset($tabs['users']);
}

$active_tab_id = $_GET['tab'] ?? array_key_first($tabs);

?>


<div class="container mt-4">
<?php
$headerTitle   = 'List View';
$headerIcon    = 'fa-list';
$headerActive  = 'list';
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

            <?php
            foreach ($tabs as $id => $tab) {

                $active = ($id === $active_tab_id) ? "show active" : "";

                echo "
    <div
        class='tab-pane fade $active'
        id='v-pills-$id'
        role='tabpanel'
        aria-labelledby='v-pills-$id-tab'
        tabindex='0'
    >";

                include($tab['file']);

                echo "</div>";

            }
            ?>

        </div>

    </div>
</div>



<!-- =========================
     FORM MODALS
========================= -->

<?php addForms(); ?>
<?php addFeedback(); ?>



<!-- =========================
     TAB HASH SCRIPT
========================= -->

<script>

    document.addEventListener("DOMContentLoaded", function () {

        /* -------------------------
           Load tab from URL hash
        ------------------------- */

        let hash = window.location.hash;

        if (hash) {

            let targetButton = document.querySelector(
                'button[data-bs-target="' + hash + '"]'
            );

            if (targetButton) {

                let tab = new bootstrap.Tab(targetButton);
                tab.show();

            }

        }



        /* -------------------------
           Update URL when tab changes
        ------------------------- */

        let tabButtons = document.querySelectorAll(
            'button[data-bs-toggle="pill"]'
        );

        tabButtons.forEach(function (button) {

            button.addEventListener('shown.bs.tab', function (e) {

                let activeTarget = e.target.getAttribute('data-bs-target');

                if (activeTarget) {

                    history.replaceState(null, null, activeTarget);

                }

            });

        });

    });


    /* -------------------------
       Toggle switch to hide or show active records
    ------------------------- */

    window.toggle_active = function (checkbox, countId) {

        let inactive = document.querySelectorAll("tr.text-decoration-line-through");

        inactive.forEach(row => {
            if (checkbox.checked) {
                row.classList.remove("d-none");
            } else {
                row.classList.add("d-none");
            }
        });

        let countEl = document.getElementById(countId);

        if (countEl) {
            let count = checkbox.checked ? countEl.dataset.totalCount : countEl.dataset.activeCount;
            let label = countEl.dataset.label;
            countEl.textContent = count + ' result(s) for "' + label + '"';
        }
    };



</script>



<?php include_once('../includes/footer.php'); ?>