<?php
/**
 * Reusable Breadcrumbs Component
 * Expected Variables:
 *  - $parentUrl : The URL to the parent dashboard tab (e.g., 'table.php#v-pills-vehicles')
 *  - $parentLabel : The display name of the parent section (e.g., 'Vehicles')
 *  - $currentItem : The display name of the current item being viewed
 */
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mt-4">
        <li class="breadcrumb-item"><a href="table.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars($parentUrl ?? 'table.php') ?>">
                <?= htmlspecialchars($parentLabel ?? 'List') ?>
            </a></li>
        <li class="breadcrumb-item active" aria-current="page">
            <?= htmlspecialchars($currentItem ?? 'Details') ?>
        </li>
    </ol>
</nav>