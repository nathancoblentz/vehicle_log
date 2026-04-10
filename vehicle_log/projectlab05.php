<?php
/*
    Jonathan Coblentz
    CPT283: PHP Programming
    Final Project: Vehicle Maintenance Log
    
    This project is a web-based application for logging and tracking vehicle maintenance and fuel records.
    It includes user authentication, CRUD operations for vehicles, services, maintenance records, and fuel records.
    
    Lab 05 focuses on Navigation, Landing Page, and tabbed Table interfaces.
*/

$title = 'Lab 05 | Navigation & Interface Elements';
$showHero = true;
$requireAuth = true;
require_once 'includes/header_bundle.php';
include_once 'includes/functions.php';

$feedback = null;

// PROCESS FORM SUBMISSIONS
addHandlers();
?>

<div class="container mt-5">

    <!-- HEADER CARD -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
            <span class="badge bg-white text-primary me-2">Lab 05</span>
            <h5 class="d-inline mb-0">Navigation and Interface Elements</h5>
        </div>
        <div class="card-body">
            <p class="lead mb-2">Enhancing User Workflow &mdash; Landing Pages &amp; Tabs</p>
            <p class="text-muted mb-0 small">
                Implementation of a comprehensive navigation system featuring a dedicated Landing Page and a tabbed "Table View." Using Bootstrap vertical pills and session storage, the interface now persists the user's active table focus across page reloads and form submissions.
            </p>
        </div>
    </div>

    <!-- CORE CHANNELS SECTION -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h3 class="text-primary mb-3">Refactored Navigation Nodes</h3>
            <div class="row g-4 mb-4">
                <?php 
                    $navNodes = [
                        ['Landing Page', 'landing_page.php', 'fa-house-chimney'],
                        ['Table View', 'table.php', 'fa-table-list'],
                        ['Information Center', 'info.php', 'fa-circle-info']
                    ];

                    foreach($navNodes as $node):
                ?>
                    <div class="col-md-6 col-lg-4">
                        <a href="<?= $node[1] ?>" class="card h-100 border transition-hover text-decoration-none bg-white">
                            <div class="card-body d-flex align-items-center">
                                <span class="bg-primary bg-opacity-10 text-primary p-3 rounded me-3 shadow-sm">
                                    <i class="fa-solid <?= $node[2] ?> fa-lg"></i>
                                </span>
                                <div class="text-dark">
                                    <h6 class="mb-0 fw-bold"><?= $node[0] ?></h6>
                                    <p class="small text-muted mb-0">System Node &mdash;</p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <p class="small text-muted mb-0">
                <i class="fa-solid fa-circle-check text-success me-1"></i>
                <strong>Session Persistence:</strong> Integrated <code>sessionStorage</code> to bookmark the active table tab (Vehicle, Vendor, Maintenance, etc.).
            </p>
        </div>
    </div>

    <!-- UI/UX FEATURES SECTION -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body">
            <h3 class="text-primary mb-3">UI/UX Implementation Focus</h3>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover small">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 25%;">Feature</th>
                            <th>Description</th>
                            <th style="width: 25%;">Methodology</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">Vertical Pill Navigation</td>
                            <td>Dynamic switching between Vehicles, Vendors, Maintenance, and Fuel tables.</td>
                            <td>Bootstrap 5.3 Pills</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Smart Search Engine</td>
                            <td>Context-aware search for dates, cost ranges, or keyword filtering across logs.</td>
                            <td>Filtered SQL Views</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Master-Detail Views</td>
                            <td>Drill-down reports for individual vehicles, detailing their specific log history.</td>
                            <td>Parameterized Routing</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- OVERLAY COMPONENTS -->
<?php addForms(); ?>
<?php addFeedback(); ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Restore active tab from sessionStorage for Lab 05 context
        var activeTabSelector = sessionStorage.getItem('activeTab_projectlab05');
        if (activeTabSelector) {
            var activeTabEl = document.querySelector(activeTabSelector);
            if (activeTabEl && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
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