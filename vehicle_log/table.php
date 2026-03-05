<!--
    Jonathan Coblentz
    CPT283: PHP Programming
    Final Project: Vehicle Maintenance Log
    
    This project is a web-based application for logging and tracking vehicle maintenance and fuel records. It includes user authentication, CRUD operations for vehicles, maintenance types, maintenance records, and fuel records. The application is built using PHP, MySQL, and Bootstrap for styling.
    
    Lab 05 focuses on navigation.
    
    -->
<?php

require 'config.php'; // Database connection
include_once 'includes/functions.php'; // For renderVehiclesTable, renderFuelTable, renderMaintenanceTable

$feedback = null; // To store success/error messages for display in the UI

// PROCESS FORM SUBMISSIONS

// Form handlers, and modals are included in this file so they are available on the same page as the dashboard for better user experience and feedback display.  They are defined in functions.php and the actual processing logic is in the controler/ directory. This keeps the code organized and modular while still allowing for dynamic feedback on the same page.

addHandlers();

$title = 'Lab 05 | Navigation';
include_once '../includes/head.php';


?>



<div class="container">

    <div class="d-flex align-items-start">
        <div class="nav flex-column nav-pills me-2" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <!--vehicles tab-->
            <button class="nav-link active" id="v-pills-vehicles-tab" data-bs-toggle="pill"
                data-bs-target="#v-pills-vehicles" type="button" role="tab" aria-controls="v-pills-vehicles"
                aria-selected="true">
                <span class="fas fa-car-side fa-2xl"></span>
            </button>

            <!-- maintenance tab-->
            <button class="nav-link" id="v-pills-maintenance-tab" data-bs-toggle="pill"
                data-bs-target="#v-pills-maintenance" type="button" role="tab" aria-controls="v-pills-maintenance"
                aria-selected="false">
                <span class="fas fa-wrench fa-2xl"></span>
            </button>

            <!-- vendors tab -->
            <button class="nav-link" id="v-pills-vendors-tab" data-bs-toggle="pill" data-bs-target="#v-pills-vendors"
                type="button" role="tab" aria-controls="v-pills-vendors" aria-selected="false">
                <span class="fas fa-store fa-2xl"></span>
            </button>

            <!-- fuel tab -->
            <button class="nav-link" id="v-pills-fuel-tab" data-bs-toggle="pill" data-bs-target="#v-pills-fuel"
                type="button" role="tab" aria-controls="v-pills-fuel" aria-selected="false">
                <span class="fas fa-gas-pump fa-2xl"></span>
            </button>

            <!-- maintenance types tab -->
            <button class="nav-link" id="v-pills-maintenance-types-tab" data-bs-toggle="pill"
                data-bs-target="#v-pills-maintenance-types" type="button" role="tab"
                aria-controls="v-pills-maintenance-types" aria-selected="false">
                <span class="fas fa-gears fa-2xl"></span>
            </button>
        </div>


        <div class="tab-content w-100" id="v-pills-tabContent">
            <!-- vehicles tab -->
            <div class="tab-pane fade show active" id="v-pills-vehicles" role="tabpanel"
                aria-labelledby="v-pills-vehicles-tab" tabindex="0">
                <?php include('includes/vehicles.php') ?>
            </div>
            <!-- maintenance tab -->
            <div class="tab-pane fade" id="v-pills-maintenance" role="tabpanel"
                aria-labelledby="v-pills-maintenance-tab" tabindex="0">
                <?php include('includes/maintenance.php') ?>
            </div>
            <!-- vendors tab -->
            <div class="tab-pane fade" id="v-pills-vendors" role="tabpanel" aria-labelledby="v-pills-vendors-tab"
                tabindex="0">
                <?php include('includes/vendors.php') ?>
            </div>
            <!-- fuel tab -->
            <div class="tab-pane fade" id="v-pills-fuel" role="tabpanel" aria-labelledby="v-pills-fuel-tab"
                tabindex="0">
                <?php include('includes/fuel.php') ?>
            </div>
            <!-- maintenance types tab -->
            <div class="tab-pane fade" id="v-pills-maintenance-types" role="tabpanel"
                aria-labelledby="v-pills-maintenance-types-tab" tabindex="0">
                <?php include('includes/maintenance_type.php') ?>
            </div>
        </div>
    </div>
</div>






<!-- FORM MODALS -->
<?php addForms(); ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. On load, read hash from URL and activate corresponding tab
        let hash = window.location.hash;
        if (hash) {
            // Find the tab button that targets this hash (e.g., data-bs-target="#v-pills-fuel")
            let targetButton = document.querySelector('button[data-bs-target="' + hash + '"]');
            if (targetButton) {
                let tab = new bootstrap.Tab(targetButton);
                tab.show();
            }
        }

        // 2. On clicking any tab, update the URL hash
        let tabButtons = document.querySelectorAll('button[data-bs-toggle="pill"]');
        tabButtons.forEach(function (button) {
            button.addEventListener('shown.bs.tab', function (e) {
                let activeTarget = e.target.getAttribute('data-bs-target');
                if (activeTarget) {
                    // Use history API to change hash without jumping the page
                    history.replaceState(null, null, activeTarget);
                }
            });
        });
    });
</script>


<?php include_once('../includes/footer.php'); ?>