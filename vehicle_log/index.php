<?php

$title = 'Vehicle Log | Fleet Management Dashboard';
$showHero = true;
require_once 'includes/header_bundle.php';
?>

<div class="container mt-4">
    <h2>Fleet Management Dashboard Working Draft and Progress Tracker</h2>
    <!-- DATA FLOW SUMMARY -->
    <div class="card my-4">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Weekly labs</h3>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item">
                    <h4>Final Project Lab 1.1</h4>
                    <p><a href="projectlab0101.php">Project overview, specifications and proposed timeline.</a></p>
                </li>
                <li class="list-group-item">
                    <h4>Final Project Lab 1.2</h4>
                    <a href="projectlab0102.php"><p>Creating a database design for the final project.</p></a>
                </li>
                <li class="list-group-item">
                    <h4>Final Project Lab 2</h4>
                    <a href="projectlab02.php"><p>Connecting a database to a PHP program.</p></a>
                </li>
                <li class="list-group-item">
                    <h4>Final Project Lab 3</h4>
                    <a href="projectlab03.php"><p>Creating an interface to add records to the database.</p></a>
                </li>
                <li class="list-group-item">
                    <h4>Final Project Lab 4</h4>
                    <a href="projectlab04.php"><p>Creating an interface to search, edit, delete or deactivate records in the database</p></a>
                    <ul class="list-group">
                        <li class="list-group-item"><a href="edit_vehicle.php">Edit Vehicle</a></li>
                        <li class="list-group-item"><a href="edit_maintenance.php">Edit Maintenance</a></li>
                        <li class="list-group-item"><a href="edit_Fuel.php">Edit Fuel Log</a></li>
                        <li class="list-group-item"><a href="edit_service.php">Edit Service</a></li>
                    </ul>
                </li>
                <li class="list-group-item">
                    <h4>Final Project Lab 5</h4>
                    <a href="projectlab05.php"><p>Navigation and Inteface Elements</p></a>
                    <li class="list-group-item"><a href="landing_page.php"></a></li>
                    <li class="list-group-item"><a href="table.php">Table View</a></li>
                    <li class="list-group-item"><a href="info.php">Consolidated Info Center</a></li>
                </li>
                <li class="list-group-item">
                    <h4>Final Project Lab 6</h4>
                    <a href="projectlab06.php">Interfaces to All Database Tables</a>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <a href="table.php">Table View</a>
                        </li>
                    </ul>
                </li>
                <li class="list-group-item">
                    <h4>Final Project Lab 7</h4>
                    <p>Progress Report Sumbitted</p>
                </li>
                <li class="list-group-item">
                    <h4>Final Project Lab 8</h4>
                    <a href="projectlab08.php"><p>Admin Password Protected Directory</p></a>
                </li>
                <li class="list-group-item">
                    <h4>Final Project Lab 9</h4>
                    <a href="projectlab09.php"><p>Form Validation and Modal Recovery System</p></a>
                </li>
                <li class="list-group-item">
                    <h4>Final Project Lab 10</h4>
                    <a href="projectlab10.php"><p>User Roles, Privileges, and Data Integrity</p></a>
                </li>

            </ul>
                

        </div>
    </div>



    



</div>






<?php include_once('../includes/footer.php'); ?>