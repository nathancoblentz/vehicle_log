<?php
// Include configuration and functions
require 'config.php';
include_once 'includes/functions.php';

// Set page title and include header
$title = 'Fleet Management Dashboard';
include_once '../includes/head.php';
include_once '../includes/nav.php';
?>

<!-- Minimal Custom CSS -->
<style>
/* Only include things Bootstrap doesn't supply out of the box */
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-lift:hover {
    transform: translateY(-10px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
}
.icon-circle {
    width: 80px;
    height: 80px;
    font-size: 2.5rem;
}
</style>

<div class="container mb-5 mt-4">
    
    <!-- Hero Section -->
    <div class="p-5 mb-5 bg-primary text-white text-center rounded-4 shadow" style="background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-info) 100%);">
        <h1 class="display-4 fw-bold mb-3" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">Welcome to Fleet Command</h1>
        <p class="lead fs-4 mb-4 opacity-75 mx-auto" style="max-width: 700px;">Your centralized hub for managing vehicles, maintenance, vendors, and fuel records with elegance and efficiency.</p>
        <a href="#modules" class="btn btn-light btn-lg rounded-pill px-5 fw-bold mt-2 text-primary shadow">
            <i class="fa-solid fa-rocket me-2"></i> Get Started
        </a>
    </div>

    <!-- Modules Grid -->
    <div id="modules" class="row g-4 mt-2">
        
        <!-- Vehicles Module -->
        <div class="col-md-6 col-lg-3">
            <a href="edit_vehicle.php" class="text-decoration-none">
                <div class="card h-100 bg-secondary border-0 shadow-sm hover-lift text-center">
                    <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                        <div class="icon-circle rounded-circle bg-dark d-flex align-items-center justify-content-center mb-4 text-info shadow">
                            <i class="fa-solid fa-car-side"></i>
                        </div>
                        <h3 class="h4 fw-bold text-white mb-2">Vehicles</h3>
                        <p class="text-white-50 mb-0">Manage your entire fleet inventory and details.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Maintenance Module -->
        <div class="col-md-6 col-lg-3">
            <a href="edit_maintenance.php" class="text-decoration-none">
                <div class="card h-100 bg-secondary border-0 shadow-sm hover-lift text-center">
                    <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                        <div class="icon-circle rounded-circle bg-dark d-flex align-items-center justify-content-center mb-4 text-warning shadow">
                            <i class="fa-solid fa-wrench"></i>
                        </div>
                        <h3 class="h4 fw-bold text-white mb-2">Maintenance</h3>
                        <p class="text-white-50 mb-0">Track repairs, schedules, and service history.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Vendors Module -->
        <div class="col-md-6 col-lg-3">
            <a href="edit_vendors.php" class="text-decoration-none">
                <div class="card h-100 bg-secondary border-0 shadow-sm hover-lift text-center">
                    <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                        <div class="icon-circle rounded-circle bg-dark d-flex align-items-center justify-content-center mb-4 text-success shadow">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <h3 class="h4 fw-bold text-white mb-2">Vendors</h3>
                        <p class="text-white-50 mb-0">Manage service providers and vendor relationships.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Fuel Module -->
        <div class="col-md-6 col-lg-3">
            <a href="edit_fuel.php" class="text-decoration-none">
                <div class="card h-100 bg-secondary border-0 shadow-sm hover-lift text-center">
                    <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                        <div class="icon-circle rounded-circle bg-dark d-flex align-items-center justify-content-center mb-4 text-danger shadow">
                            <i class="fa-solid fa-gas-pump"></i>
                        </div>
                        <h3 class="h4 fw-bold text-white mb-2">Fueling</h3>
                        <p class="text-white-50 mb-0">Log fuel entries, costs, and track efficiency.</p>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <!-- Optional Quick Stats or Recent Activity Section could go here in the future -->
    <div class="row mt-5 mb-4">
        <div class="col-12 text-center text-muted">
            <small><i class="fa-solid fa-shield-halved me-1"></i> Fleet Management System v2.0 &bull; Secure Access</small>
        </div>
    </div>

</div>

<!-- Auto-scroll script for the "Get Started" button -->
<script>
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});
</script>

<?php include_once('../includes/footer.php'); ?>