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

$title = 'Lab 03 | Adding New Records';
include_once '../includes/head.php';
include_once '../includes/nav.php';
include_once '../includes/hero.php';
?>

<div class="container mt-4">

    <!-- HEADER CARD -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
            <span class="badge bg-light text-primary me-2">Lab 03</span>
            <h5 class="d-inline mb-0">Adding New Records to the Database</h5>
        </div>
        <div class="card-body">
            <p class="lead">
                As of today (February 15, 2026), I've added the functionality to add new vehicles and fuel records to
                the database. The next steps will be to implement the update and delete operations, as well as adding
                the maintenance and maintenance type tables. I'm using Bootstrap modals for the forms, which keeps the
                interface clean and user-friendly.
                I'm utilizing ChatGPT and Google's <a href="https://antigravity.google.com/" target="_blank">Antigravity
                    IDE</a>
                to help with project scaffolding and formatting. The add handlers are set up to process form submissions
                and
                provide feedback on success or failure. Overall, I'm making good progress on the CRUD operations for the
                fleet management dashboard.
            </p>
            <p class="lead mb-2">CRUD Operations &mdash; Data Flow Summary</p>
            <ol class="text-muted mb-0">
                <li><strong>Button Click</strong> &mdash; User clicks an &ldquo;Add&rdquo; button, opening a Bootstrap
                    modal form via <code>data-bs-target</code>.</li>
                <li><strong>Modal Form</strong> &mdash; The modal contains a <code>&lt;form&gt;</code> with a hidden
                    field and all input fields. Submits via <code>POST</code> back to this page.</li>
                <li><strong>Handler Processing</strong> &mdash; <code>addHandlers()</code> checks <code>$_POST</code>
                    for the hidden field and <code>require</code>s the matching controller file.</li>
                <li><strong>Database Insert</strong> &mdash; The handler validates input, executes a PDO prepared
                    statement, and sets a <code>$feedback</code> message.</li>
                <li><strong>Feedback Modal</strong> &mdash; <code>addFeedback()</code> includes the feedback modal,
                    which auto-displays the result message via JavaScript.</li>
            </ol>
        </div>
    </div>

    <h2>Fleet Management Dashboard</h2>

    <?php include_once('includes/dashboard.php'); ?>

</div>



<!-- FORM MODALS -->
<?php addForms(); ?>

<!-- FEEDBACK MODAL -->
<?php addFeedback(); ?>


<?php include_once('../includes/footer.php'); ?>

