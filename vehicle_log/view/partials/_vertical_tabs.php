<div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
    <?php foreach ($tabs as $id => $tab): 
        $active = ($id === $active_tab_id) ? "active" : "";
        $selected = ($id === $active_tab_id) ? "true" : "false";
    ?>
        <button
            class="nav-link <?= $active ?>"
            id="v-pills-<?= $id ?>-tab"
            data-bs-toggle="pill"
            data-bs-target="#v-pills-<?= $id ?>"
            type="button"
            role="tab"
            aria-controls="v-pills-<?= $id ?>"
            aria-selected="<?= $selected ?>"
        >
            <span class="fas <?= $tab['icon'] ?> fa-2xl mb-1"></span>
            <span class="tab-label"><?= $tab['label'] ?></span>
        </button>
    <?php endforeach; ?>
</div>
