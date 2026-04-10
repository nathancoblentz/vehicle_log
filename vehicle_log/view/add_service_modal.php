<?php // ADD SERVICE FORM 
?>

<?php
renderModalStart('addMaintenanceTypeModal', 'Add Service', 'addMaintenanceTypeForm', 'add_service');
$mode = 'add';
$prefix = 'add_mtype_';
include __DIR__ . '/partials/_service_form.php';
renderModalEnd('addMaintenanceTypeForm', 'Add Service'); ?>