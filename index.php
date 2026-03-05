<?php
// Include functions first
include_once __DIR__ . '/includes/functions.php';

// Load exercises JSON
$exercisesJson = file_get_contents(__DIR__ . '/exercises/exercises.json');
$exercises = json_decode($exercisesJson, true) ?? [];

// Load project labs JSON
$projectLabsJson = file_get_contents(__DIR__ . '/vehicle_log/project_labs.json');
$projectLabs = json_decode($projectLabsJson, true) ?? [];

// Include page sections
include 'includes/head.php';
include 'includes/nav.php';
include 'includes/hero.php';
?>

<div class="container mt-5 mb-5" id="exercises">
    <h2 class="display-6">Murach Exercises</h2>
    <p class="lead">Exercises from the Murach PHP Programming book</p>

    <div class="row g-2">
        <?php renderList($exercises, $baseURL); ?>
    </div>
</div>

<div class="container mt-5 mb-5" id="project">
    <h2 class="display-6">Project Labs</h2>
    <p class="lead">Documenting progress on the final project in CPT283</p>

    <div class="row g-2">
        <?php renderList($projectLabs, $baseURL); ?>
    </div>
</div>

<?php include_once('includes/footer.php'); ?>