<?php

if (!isset($title)) {
    $title = 'Jonathan Coblentz | CPT283: PHP Programming';
}

// Automatically detect if we are on XAMPP (localhost) or the live production server
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    $baseURL = '/cpt283coblentz/'; // base URL for local development
} else {
    $baseURL = 'https://www.26sp-cpt283-coblentz.beausanders.net/'; // base URL to generate link
}


// bootstrap themes taken from https://bootswatch.com/
// card and jumbotron elements taken from Bootstrap's example docs found here: 
// https://getbootstrap.com/docs/5.3/examples/


// theme choices: 
// default brite cerulean cosmo cyborg darkly flatly journal litera lumen lux materia minty morph
// pulse quartz sandstone simplex sketchy slate solar spacelab superhero united vapor yeti zephyr 

$bootstrap_sheet = 'https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/darkly/bootstrap.min.css';
$local_styles = $baseURL . 'css/styles.css';
$local_scripts = $baseURL . 'js/scripts.js';

$exercisesJson = file_get_contents($baseURL . 'exercises/exercises.json'); // read the JSON file and decode it for PHP
$exercises = json_decode($exercisesJson, true); // true means we are using an associative array



// I used ChatGPT to get instructions for working with JSON and for building the loop to generate links to each project.


?>

<!doctype html>

<body data-bs-spy="scroll" data-bs-target="#navbarNav" data-bs-offset="80" tabindex="0">

    <html lang="en" data-bx-theme="dark">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap CSS -->
        <link href="<?= $bootstrap_sheet ?>" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
        <!-- Custom styles -->
        <link href="<?= $local_styles ?>" rel="stylesheet">

        <title><?php echo $title ?></title>

        <!-- bootstrap JS in case we use any interactive elements -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"
            defer></script>
        <script src="<?= $local_scripts ?>"></script>
    </head>

    <body data-bs-spy="scroll" data-bs-target="#navbarNav" data-bs-offset="80" tabindex="0">