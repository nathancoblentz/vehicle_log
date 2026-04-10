<?php
/**
 * login.php — Security Gateway
 * 
 * Authenticates users and establishes the persistent session for role-based
 * access across the application.
 */

require_once 'includes/init.php';

// Initialize error message variable
$error = null;

// Handle form submission securely
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Sanitize and validate inputs
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Securely fetch user matching the unique email
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify password (supports both modern BCrypt hashes and legacy plain text)
    $isHashedMatch = password_verify($password, $user['user_password']);
    $isPlainMatch  = ($password === $user['user_password']);

    // Authenticate user and establish session
    if ($user && ($isHashedMatch || $isPlainMatch)) {
        
        // Populate standard session identity
        $_SESSION['user_id']  = (int) $user['user_id'];
        $_SESSION['is_admin'] = ($user['user_role'] === 'admin');
        $_SESSION['user'] = [
            'id'    => $user['user_id'],
            'email' => $user['email'],
            'name'  => trim($user['first_name'] . ' ' . $user['last_name']),
            'role'  => $user['user_role']
        ];
        
        // Context-aware redirection logic
        $target = ($user['user_role'] === 'admin') ? 'admin/index.php' : 'table.php';
        header("Location: $target");
        exit();

    } else {
        $error = "The credentials provided do not match our active records.";
    }
}

$title = "Login | Vehicle Maintenance Log";
include_once '../includes/head.php'; 
?>

<!-- Custom CSS from Landing Page -->


<div class="container d-flex align-items-center justify-content-center min-vh-100 py-5">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="max-width: 450px; width: 100%;">
        
        <!-- CARD HEADER / BRANDING -->
        <div class="card-header text-white text-center py-5 border-0" style="background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-info) 100%);">
            <div class="mb-4">
                <div class="icon-circle rounded-circle bg-white d-flex align-items-center justify-content-center mx-auto text-primary shadow-sm hover-lift">
                    <i class="fa-solid fa-car-side"></i>
                </div>
            </div>
            <h2 class="h3 mb-0 fw-bold" style="text-shadow: 1px 1px 3px rgba(0,0,0,0.2);">Fleet Command</h2>
            <p class="small text-white-50 mb-0 opacity-75">Secure Access Gateway</p>
        </div>

        <div class="card-body p-4 p-md-5">
            
            <?php if ($error): ?>
                <div class="alert alert-danger border-0 small shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php elseif (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] === 'logout'): ?>
                    <div class="alert alert-success border-0 small shadow-sm mb-4" role="alert">
                        <i class="fa-solid fa-check-circle me-2"></i>You have been successfully logged out.
                    </div>
                <?php elseif ($_GET['msg'] === 'auth'): ?>
                    <div class="alert alert-warning border-0 small shadow-sm mb-4" role="alert">
                        <i class="fa-solid fa-lock me-2"></i>Please log in to access that page.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- logins for testing -->
            <div class="alert alert-info border-0 small shadow-sm mb-4">
                <p class="mb-1 fw-bold"><i class="fa-solid fa-info-circle me-1"></i> Sample Logins:</p>
                <ul class="mb-0 ps-3">
                    <li><strong>Admin:</strong> admin@test.com / admin</li>
                    <li><strong>User:</strong> cpt283@test.com / webapps</li>
                </ul>
            </div>

            <form method="POST" class="needs-validation">
                
                <!-- EMAIL FIELD -->
                <div class="mb-3">
                    <label for="email" class="form-label small fw-bold text-muted">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-envelope text-muted"></i></span>
                        <input type="email" name="email" id="email" 
                               class="form-control border-start-0" 
                               placeholder="name@example.com" 
                               required autofocus>
                    </div>
                </div>

                <!-- PASSWORD FIELD -->
                <div class="mb-4">
                    <label for="password" class="form-label small fw-bold text-muted">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                        <input type="password" name="password" id="password" 
                               class="form-control border-start-0" 
                               placeholder="Enter your password" 
                               required>
                    </div>
                </div>

                <!-- SUBMIT ACTION -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                        Sign In <i class="fa-solid fa-arrow-right-to-bracket ms-1"></i>
                    </button>
                    <a href="index.php" class="btn btn-link link-secondary text-decoration-none small mt-2">
                        <i class="fa-solid fa-chevron-left me-1"></i> Back to Home
                    </a>
                </div>

            </form>
        </div>


    </div>
</div>


<?php
// Include the standard footer
include_once '../includes/footer.php';
?>
