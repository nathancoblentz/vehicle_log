<?php
/**
 * _page_header.php — Unified page header bar
 * 
 * Expected variables (set before including):
 * @var string $headerTitle     Page title text (e.g., "List View", "Information Center")
 * @var string $headerIcon      FontAwesome icon class (e.g., "fa-list", "fa-circle-info")
 * @var string $headerActive    Which toggle is active: 'list' or 'info'
 * @var string $headerListUrl   URL for the List View button (e.g., "table.php", "index.php")
 * @var string $headerInfoUrl   URL for the Info View button (e.g., "info.php")
 * @var string $headerBadge     Optional badge text (e.g., "ADMIN"). Leave empty for none.
 */

$headerBadge = $headerBadge ?? '';
?>

    <div class="d-flex align-items-center py-3 border-bottom border-primary mb-4 flex-wrap gap-2">
        <!-- Page Title -->
        <h2 class="mb-0 fw-bold text-primary me-3">
            <i class="fa-solid <?= htmlspecialchars($headerIcon) ?> me-2"></i><?= htmlspecialchars($headerTitle) ?>
            <?php if ($headerBadge): ?>
                <span class="badge bg-primary ms-2 fs-6 align-middle"><?= htmlspecialchars($headerBadge) ?></span>
            <?php endif; ?>
        </h2>

        <!-- View Toggle -->
        <div class="btn-group shadow-sm" role="group" aria-label="View Toggle">
            <?php if ($headerActive === 'list'): ?>
                <button type="button" class="btn btn-primary px-3" disabled>
                    <i class="fa-solid fa-list me-1"></i> List View
                </button>
            <?php else: ?>
                <a href="<?= htmlspecialchars($headerListUrl) ?>" class="btn btn-outline-primary px-3">
                    <i class="fa-solid fa-list me-1"></i> List View
                </a>
            <?php endif; ?>

            <?php if ($headerActive === 'info'): ?>
                <button type="button" class="btn btn-primary px-3" disabled>
                    <i class="fa-solid fa-circle-info me-1"></i> Record View
                </button>
            <?php else: ?>
                <a href="<?= htmlspecialchars($headerInfoUrl) ?>" class="btn btn-outline-primary px-3">
                    <i class="fa-solid fa-circle-info me-1"></i> Record View
                </a>
            <?php endif; ?>
        </div>

        <!-- User Info (pushed to right) -->
        <div class="ms-auto d-flex align-items-center">
            <span class="text-muted me-2">
                <i class="fa-solid fa-user-circle me-1"></i>
                Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name'] ?? 'System User') ?></strong>
            </span>
            <a class="btn btn-outline-secondary btn-sm" href="<?= $baseURL ?>vehicle_log/logout.php">
                <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
            </a>
        </div>
    </div>
