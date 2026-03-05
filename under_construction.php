<?php
$title = 'Under Construction';

$labName = $_GET['lab'] ?? 'This lab';
$dueRaw  = $_GET['due'] ?? null;

$dueFormatted = $dueRaw
    ? date('F j, Y', strtotime($dueRaw))
    : 'TBD';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <link href="https://bootswatch.com/5/darkly/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

<div class="container text-center mt-5">
    <h1 class="display-4">🚧 Under Construction</h1>
    <p class="fs-4 mt-3">
        <?= htmlspecialchars($labName) ?> is currently under construction.  Please check back after the due date for this lab.
<p class="text-warning fs-5 mt-3">
    Due date: <strong><?= $dueFormatted ?></strong>
</p>

    <a href="../index.php" class="btn btn-primary mt-4">
        🏠      
    </a>
</div>

</body>
</html>
