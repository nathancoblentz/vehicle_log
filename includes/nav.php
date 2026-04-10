<nav class="navbar navbar-expand-lg bg-primary sticky-top" data-bs-theme="dark">
    <div class="container">
        <a class="navbar-brand" href="<?php echo $baseURL; ?>">CPT283 Projects</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseURL; ?>#exercises">Murach Exercises</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseURL; ?>vehicle_log/landing_page.php">Fleet App</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseURL; ?>#project">Project Labs</a>
                </li>

                <?php 
                // Display the logged-in user's name and a logout link if authenticated
                if (session_status() === PHP_SESSION_NONE) { session_start(); }
                if (isset($_SESSION['user'])): ?>
                    <li class="nav-item ms-lg-3 border-start ps-lg-3 py-2 py-lg-0">
                        <span class="navbar-text text-white small me-2">
                            <i class="fa-solid fa-user-circle me-1"></i>
                            Welcome, <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong>
                        </span>
                        <a class="btn btn-outline-light btn-sm" href="<?= $baseURL ?>vehicle_log/logout.php">
                            <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>