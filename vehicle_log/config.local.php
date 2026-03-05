<?php
$host = 'localhost';
$dbname = 'cpt283coblentz_vehicle_log';
$user ='root'; // ← Change this to your REAL DB username
$username = 'cpt283coblentz';  // ← Change this to your REAL DB username
$password = 'Pinkyp!321'; // ← Change this to your REAL DB password
$charset = 'utf8mb4';


try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>

<!-- AIzaSyCzbAywlFJcaU_qcZQE7bppI9YYuQcBeAk - google api key for antigravity IDE -->

<!-- OpenAI API key for Antigravity IDE -->
<!-- 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 -->


