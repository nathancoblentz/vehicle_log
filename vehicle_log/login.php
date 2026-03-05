<?php
session_start(); // start session
require_once 'config.php'; // your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // form submitted
    $email = $_POST['email']; // get email
    $password = $_POST['password']; // get password

    // fetch user from DB
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && $password === $user['user_password']) {
        // store in session
        $_SESSION['user'] = [
            'email' => $user['email'],
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'role' => $user['user_role'] // "admin" or "user"
        ];
        header('Location: vehicles.php'); // redirect to main page
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>

<form method="POST">
    <input name="email" placeholder="Email">
    <input name="password" type="password" placeholder="Password">
    <button type="submit">Login</button>
</form>
<?php if(!empty($error)) echo "<p>$error</p>"; ?>
