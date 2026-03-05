<?php

require 'config.php';
$title = 'Lab 01-01 | Project Overview & Timeline';
include_once '../includes/head.php';
include_once '../includes/nav.php';
include_once '../includes/hero.php';
include_once 'includes/functions.php';

?>

<div class="container mt-4">

    <!-- HEADER CARD -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
            <span class="badge bg-light text-primary me-2">Lab 01-01</span>
            <h5 class="d-inline mb-0">Project Overview &amp; Timeline</h5>
        </div>
        <div class="card-body">
            <p class="lead mb-2">Vehicle Maintenance Log &mdash; Project Planning</p>
            <p class="text-muted mb-0">A big-picture overview of the requirements and timeline for the project. The
                table below outlines key milestones, deliverables, and objectives for each week to stay on track and
                meet all requirements.</p>
        </div>
    </div>
    <!-- AI Utilization Section -->
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h5 class="card-title">AI utilization</h5>
            <p class="card-text">
                I used ChatGPT to generate an initial framework for organizing the Vehicle Maintenance Log project. The
                structured breakdown clarifies the project’s scope and supports effective planning by dividing the work
                into manageable stages. This approach helps ensure that all required components are addressed in a
                logical and timely manner.
            </p>
        </div>
    </div>

    <!-- Example Code Section -->
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h5 class="card-title">Example Code / Exercises</h5>
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
                            <td>Create MySQL database <code>vehicle_log</code> and tables (users, vehicles,
                                maintenance_type, maintenance, fuel). Set up /vehicle_log and /vehicle_log/admin
                                directories. Configure .htaccess for admin folder.</td>
                        </tr>
                        <tr>
                            <td>Week 2</td>
                            <td>Feb 2 – Feb 8</td>
                            <td>Basic Authentication & Login</td>
                            <td>Implement login page, basic authentication (admin vs user), session management. Test
                                login and redirect based on role.</td>
                        </tr>
                        <tr>
                            <td>Week 3</td>
                            <td>Feb 9 – Feb 15</td>
                            <td>CRUD: Vehicles & Maintenance Types</td>
                            <td>Build forms to add/edit/list vehicles and maintenance types. Ensure admin-only delete
                                functionality.</td>
                        </tr>
                        <tr>
                            <td>Week 4</td>
                            <td>Feb 16 – Feb 22</td>
                            <td>CRUD: Maintenance & Fuel Records</td>
                            <td>Build forms to add/edit/list maintenance and fuel records. Add date, numeric, and basic
                                validation.</td>
                        </tr>
                        <tr>
                            <td>Week 5</td>
                            <td>Feb 23 – Mar 1</td>
                            <td>Reports: Vehicle Overview</td>
                            <td>Generate reports for each vehicle: maintenance history, fuel consumption, MPG, cost over
                                time. Begin testing report calculations.</td>
                        </tr>
                        <tr>
                            <td>Week 6</td>
                            <td>Mar 2 – Mar 8</td>
                            <td>Finalize Reports</td>
                            <td>Complete all vehicle report features, ensure accurate calculations and display of
                                maintenance, fuel, and cost data.</td>
                        </tr>
                        <tr>
                            <td>Week 7</td>
                            <td>Mar 9 – Mar 15</td>
                            <td>UI Improvements & Templates</td>
                            <td>Implement navigation, header/footer, reusable templates. Prepare initial responsive
                                layout using Bootstrap.</td>
                        </tr>
                        <tr>
                            <td>Week 8</td>
                            <td>Mar 16 – Mar 22</td>
                            <td>Extra Features: TinyMCE & Dashboard</td>
                            <td>Integrate TinyMCE for maintenance descriptions. Begin student-added features (dashboard,
                                alerts, graphs, etc.)</td>
                        </tr>
                        <tr>
                            <td>Week 9</td>
                            <td>Mar 23 – Mar 29</td>
                            <td>MVC Refactor & Code Organization</td>
                            <td>Separate code into Models, Views, Controllers. Refactor forms, lists, and reports into
                                reusable components.</td>
                        </tr>
                        <tr>
                            <td>Week 10</td>
                            <td>Mar 30 – Apr 5</td>
                            <td>Testing & Debugging</td>
                            <td>Test all CRUD operations, login/roles, and reports. Fix bugs and verify input
                                validation.</td>
                        </tr>
                        <tr>
                            <td>Week 11</td>
                            <td>Apr 6 – Apr 12</td>
                            <td>Documentation</td>
                            <td>Add comments and documentation for all pages and functions. Prepare README or project
                                overview.</td>
                        </tr>
                        <tr>
                            <td>Week 12</td>
                            <td>Apr 13 – Apr 19</td>
                            <td>Final Feature Completion</td>
                            <td>Complete remaining extra features, polish UI, finalize Bootstrap responsiveness. Ensure
                                admin functionality is fully working.</td>
                        </tr>
                        <tr>
                            <td>Week 13</td>
                            <td>Apr 20 – Apr 26</td>
                            <td>Final Testing & Demo Prep</td>
                            <td>Conduct full project walkthrough, finalize testing, prepare for demonstration, and
                                ensure deployment is ready.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>


<?php include_once('../includes/footer.php'); ?>





