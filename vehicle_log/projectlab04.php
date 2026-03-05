<!--
Jonathan Coblentz
CPT283: PHP Programming
Final Project: Vehicle Maintenance Log

This project is a web-based application for logging and tracking vehicle maintenance and fuel records. It includes user authentication, CRUD operations for vehicles, maintenance types, maintenance records, and fuel records. The application is built using PHP, MySQL, and Bootstrap for styling.

Lab 03 focuses on implementing the "Create" functionality for adding new records to the database. This includes building forms for adding vehicles and fuel records, processing form submissions, and providing feedback to the user on the success or failure of their actions.
-->


<?php

require 'config.php'; // Database connection
include_once 'includes/functions.php'; // For renderVehiclesTable, renderFuelTable, renderMaintenanceTable

$feedback = null; // To store success/error messages for display in the UI

// PROCESS FORM SUBMISSIONS

// Form handlers, and modals are included in this file so they are available on the same page as the dashboard for better user experience and feedback display.  They are defined in functions.php and the actual processing logic is in the controler/ directory. This keeps the code organized and modular while still allowing for dynamic feedback on the same page.

addHandlers();

$title = 'Lab 04 | Searching, Editing and Deleting Records in the Database';
include_once '../includes/head.php';
include_once '../includes/nav.php';
include_once '../includes/hero.php';
?>

<div class="container mt-4">

    <!-- HEADER CARD -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
            <span class="badge bg-light text-primary me-2">Lab 04</span>
            <h5 class="d-inline mb-0">Searching, Editing and Deleting Records in the Database</h5>
        </div>
        <div class="card-body">
            <p class="lead">
                This lab builds on Lab 03 by adding the ability to <strong>search</strong>, <strong>edit</strong>, and
                <strong>delete</strong> records in the database. Each entity — Vehicles, Fuel, Maintenance, Maintenance
                Types, and Vendors — has a dedicated edit page with a search bar for filtering records, inline action
                buttons for editing and deleting, and modal forms for updating existing data. Delete operations include
                confirmation prompts and referential integrity checks to prevent orphaned records.
            </p>
            <ul class="list-group">
                <li class="list-group-item"><a href="edit_vehicle.php">Edit Vehicle</a></li>
                <li class="list-group-item"><a href="edit_maintenance.php">Edit Maintenance</a></li>
                <li class="list-group-item"><a href="edit_fuel.php">Edit Fuel Log</a></li>
                <li class="list-group-item"><a href="edit_maintenance_type.php">Edit Maintenance Type</a></li>
                <li class="list-group-item"><a href="edit_vendors.php">Edit Vendors</a></li>
            </ul>

        </div>
    </div>



</div>



<!-- FORM MODALS -->
<?php addForms(); ?>

<!-- FEEDBACK MODAL -->
<?php addFeedback(); ?>


<?php include_once('../includes/footer.php'); ?>