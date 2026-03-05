<?php
require_once '../config.php';
// $OPENAI_KEY was here, now use $OPENAI_KEY from config.php

?>

<ul class="nav nav-tabs">

    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#vehicles">Vehicles</a>
    </li>

    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#fuel">Fuel</a>
    </li>

    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#maintenance">Maintenance</a>
    </li>

    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#maintenance_types">Maintenance Types</a>
    </li>


</ul>

<div class="tab-content mt-3">

    <div class="tab-pane fade show active" id="vehicles">


        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
            Add Vehicle
        </button>
        <?php renderVehiclesTable($db); ?>

    </div>

    <div class="tab-pane fade" id="fuel">
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addFuelModal">
            Add Fuel
        </button>
        <?php renderFuelTable($db); ?>
    </div>

    <div class="tab-pane fade" id="maintenance">
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addMaintenanceModal">
            Add Maintenance Record
        </button>
        <?php renderMaintenanceTable($db); ?>
    </div>

    <div class="tab-pane fade" id="maintenance_types">
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addMaintenanceTypeModal">
            Add Maintenance Type
        </button>
        <?php renderMaintenanceTypeTable($db); ?>
    </div>

</div>