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

$title = 'Lab 05 | Navigation and Interface Elements';
include_once '../includes/head.php';
include_once '../includes/nav.php';
include_once '../includes/hero.php';
?>



<div class="container">
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
            <span class="badge bg-light text-primary me-2">Lab 05</span>
            <h5 class="d-inline mb-0">Navigation and Interface Elements</h5>
        </div>
        <div class="card-body">
            <p class="lead">
                This lab builds focuses of navigation and interface elements for the vehicle log database.  I've added a landing page to welcome the user to the the app, a 'Table' page with bootstrap vertical pill style navigation to switch between Vehicle, Vendor, Maintenance, Fuel, and Maintenance Type tables.  Each table has a search area for the user to search by any relevant keywords; the fuel and maintenance records allow the user to search by a date range or cost range.  Each table includes an Add button, and each table row includes an edit and delete button, that trigger a Bootstrap modal for the user to add or edit a record.  The Vehicle, Maintenance Type and Vendor tables also include an 'Info' page that allows the user to drill down for more details about an individual record, with reports showing fuel and maintenance logs for an individaul vehicle, maintenance records by vendor, or records of each service type.
            </p>
            <ul class="list-group">
                <li class="list-group-item"><a href="landing_page.php"></a></li>
                <li class="list-group-item"><a href="table.php">Table View</a></li>
                <li class="list-group-item"><a href="vehicle_info.php">Vehicle Info Page</a></li>
                <li class="list-group-item"><a href="vendor_info.php">Vendor Info Page</a></li>
                <li class="list-group-item"><a href="maintenance_type_info.php">Maintenance Type Info Page</a></li>
            </ul>
                            
                
        </div>
    </div>






</div>



<!-- FORM MODALS -->
<?php addForms(); ?>

<!-- FEEDBACK MODAL -->
<?php addFeedback(); ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Restore active tab from sessionStorage
        var activeTabSelector = sessionStorage.getItem('activeTab_projectlab05');
        if (activeTabSelector) {
            var activeTabEl = document.querySelector(activeTabSelector);
            if (activeTabEl) {
                var tab = new bootstrap.Tab(activeTabEl);
                tab.show();
            }
        }

        // Save active tab to sessionStorage on tab show
        var tabTriggerList = document.querySelectorAll('button[data-bs-toggle="pill"]');
        tabTriggerList.forEach(function (tabEl) {
            tabEl.addEventListener('shown.bs.tab', function (event) {
                sessionStorage.setItem('activeTab_projectlab05', '#' + event.target.id);
            });
        });
    });
</script>

<?php include_once('../includes/footer.php'); ?>