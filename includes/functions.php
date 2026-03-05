<?php
// functions.php

$baseURL = 'https://www.26sp-cpt283-coblentz.beausanders.net/';

/**
 * Render a single card for exercises or project labs
 *
 * @param string $header
 * @param string $title
 * @param string $description
 * @param string $status
 * @param string $href
 */
function renderCard(string $header, string $title, string $description, string $status, string $href)
{
    $isCompleted = ($status === 'Completed');
    $badgeClass = $isCompleted ? 'bg-success text-white' : 'bg-warning text-white';
    ?>
    <div class="col-12">
        <div class="card shadow-sm p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-1 d-flex align-items-center fs-5 mb-2">
                        <?= htmlspecialchars($header) ?>
                        <span class="badge <?= $badgeClass ?> small ms-2 py-1 px-2"><?= htmlspecialchars($status) ?></span>
                    </h3>
                    <p class="lead card-subtitle mb-1 text-muted fs-6"><?= htmlspecialchars($title) ?></p>
                    <p class="card-text mb-0 fs-6"><?= htmlspecialchars($description) ?></p>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-primary btn-lg rounded-2"
                            onclick="window.location.href='<?= htmlspecialchars($href) ?>'">
                        View
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Render a list of exercises or project labs.
 * Detects type automatically: exercises have 'folder', projects have 'filename'
 *
 * @param array $items Array of exercises or project labs
 * @param string $baseURL Base URL of site
 */
function renderList(array $items, string $baseURL)
{
    foreach ($items as $item) {
        $isProject = isset($item['filename']);
        $isExercise = isset($item['folder']);

        if ($item['status'] === 'Completed') {
            if ($isProject) {
                // Projects live in vehicle_log/, sibling to exercises/
                $href = $baseURL . '../vehicle_log/' . $item['filename'];
            } elseif ($isExercise) {
                // Exercises live inside exercises/
                $href = $baseURL . '../exercises/' . $item['folder'] . '/index.php';
            } else {
                $href = '#';
            }
        } else {
            // under construction
            $href = $baseURL . '../under_construction.php?lab=' .
                urlencode($item['header'] ?? '') .
                '&due=' . urlencode($item['due date'] ?? '');
        }

        renderCard(
            $item['header'] ?? 'No Header',
            $item['title'] ?? 'No Title',
            $item['description'] ?? '',
            $item['status'] ?? 'Unknown',
            $href
        );
    }
}
