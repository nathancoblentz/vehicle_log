<?php
include 'includes/functions.php';

$mockItems = [
    [
        'folder' => 'ch02_ex1',
        'status' => 'Completed',
        'header' => 'Ex 1'
    ],
    [
        'filename' => 'projectlab0101.php',
        'status' => 'Completed',
        'header' => 'Proj 1'
    ],
    [
        'header' => 'Pending Item',
        'status' => 'In Progress'
    ]
];

ob_start();
renderList($mockItems, $baseURL);
$html = ob_get_clean();

preg_match_all('/href="([^"]+)"/', $html, $matches);
$urls = $matches[1];

foreach ($urls as $url) {
    if (strpos($url, 'exercises/ch02_ex1') !== false) {
        echo "PASS: Exercise URL correct ($url)\n";
    } elseif (strpos($url, 'vehicle_log/projectlab0101.php') !== false) {
        echo "PASS: Project URL correct ($url)\n";
    } elseif (strpos($url, 'under_construction.php') !== false) {
        echo "PASS: Under construction correct ($url)\n";
    } else {
        echo "FAIL: Unexpected URL ($url)\n";
    }
}
