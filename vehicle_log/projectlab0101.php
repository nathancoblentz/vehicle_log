<?php
/*
    Jonathan Coblentz
    CPT283: PHP Programming
    Final Project: Vehicle Maintenance Log
    
    This project is a web-based application for logging and tracking vehicle maintenance and fuel records. 
    It includes user authentication, CRUD operations for vehicles, services, maintenance records, and fuel records.
    
    Lab 01-01 focuses on Project Overview & Timeline.
*/

$title = 'Lab 01-01 | Project Overview & Timeline';
$showHero = true;
require_once 'includes/header_bundle.php';
include_once 'includes/functions.php';
?>

<div class="container mt-5">

    <!-- HEADER CARD -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
            <span class="badge bg-white text-primary me-2">Lab 01-01</span>
            <h5 class="d-inline mb-0">Project Overview &amp; Timeline</h5>
        </div>
        <div class="card-body">
            <p class="lead mb-2">Vehicle Maintenance Log &mdash; Project Planning</p>
            <p class="text-muted mb-0 small">
                A big-picture overview of the requirements and timeline for the project. The table below outlines key milestones, deliverables, and objectives for each week to stay on track and meet all requirements.
            </p>
        </div>
    </div>

    <!-- AI UTILIZATION SECTION -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <h3 class="text-primary mb-3">AI Utilization</h3>
            <p class="mb-0">
                I used ChatGPT to generate an initial framework for organizing the Vehicle Maintenance Log project. The structured breakdown clarifies the project’s scope and supports effective planning by dividing the work into manageable stages. This approach helps ensure that all required components are addressed in a logical and timely manner.
            </p>
        </div>
    </div>

    <!-- MILESTONES SECTION -->
    <div class="card shadow-sm mb-5 border-0">
        <div class="card-body">
            <h3 class="text-primary mb-3">Project Milestones & Timeline</h3>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Week</th>
                            <th scope="col">Dates</th>
                            <th scope="col">Milestone / Deliverable</th>
                            <th scope="col">Notes / Goals</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Week 1</td>
                            <td>Jan 25 – Feb 1</td>
                            <td>Project Kickoff & Database Setup</td>
                            <td>Create MySQL database <code>vehicle_log</code> and tables. Set up directories and configure <code>.htaccess</code>.</td>
                        </tr>
                        <tr>
                            <td>Week 2</td>
                            <td>Feb 2 – Feb 8</td>
                            <td>Basic Authentication & Login</td>
                            <td>Implement login page, role-based redirection, and session management.</td>
                        </tr>
                        <tr>
                            <td>Week 3</td>
                            <td>Feb 9 – Feb 15</td>
                            <td>CRUD: Vehicles & Services</td>
                            <td>Build initial forms and handlers for core data entities.</td>
                        </tr>
                        <tr>
                            <td>Week 4</td>
                            <td>Feb 16 – Feb 22</td>
                            <td>CRUD: Maintenance & Fuel Records</td>
                            <td>Implement data entry for logs with initial numeric validation.</td>
                        </tr>
                        <tr>
                            <td>Week 5</td>
                            <td>Feb 23 – Mar 1</td>
                            <td>Reports: Vehicle Overview</td>
                            <td>Generate aggregated metrics for fuel and maintenance history.</td>
                        </tr>
                        <tr>
                            <td>Week 6</td>
                            <td>Mar 2 – Mar 15</td>
                            <td>UI Refinement & Dashboard</td>
                            <td>Develop the main dashboard interface and responsive table views.</td>
                        </tr>
                        <tr>
                            <td>Week 7</td>
                            <td>Mar 16 – Mar 29</td>
                            <td>Advanced Logic & MVC</td>
                            <td>Refactor code into a clean MVC pattern and implement complex server-side hooks.</td>
                        </tr>
                        <tr>
                            <td>Week 8</td>
                            <td>Mar 30 – Apr 12</td>
                            <td>Security & Error Recovery</td>
                            <td>Finalize role-based access control and the "Sticky Form" recovery system.</td>
                        </tr>
                        <tr>
                            <td>Week 9</td>
                            <td>Apr 13 – Apr 26</td>
                            <td>Final Polish & Deployment</td>
                            <td>Comprehensive testing, documentation completion, and final demo preparation.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php include_once('../includes/footer.php'); ?>
