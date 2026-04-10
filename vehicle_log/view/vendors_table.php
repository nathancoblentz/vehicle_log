<!-- HEADER CARD -->
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="d-inline mb-0"><span class="fas fa-store me-3"></span>Vendors</h3>
        </div>
        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addVendorModal"
            title="Add Vendor">
            <i class="fa-solid fa-plus text-white"></i>
        </button>
    </div>
    <div class="card-body">
        <p class="lead mb-3">
            Search for a vendor to edit or delete. Enter any keyword (name, city, state, phone, etc.).
        </p>

        <!-- Search Form -->
        <form method="POST" class="row g-3">

            <div class="col-12">
                <div class="input-group input-group-lg">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-search me-1"></i>
                    </button>
                    <input class="form-control" id="search_string" type="text" name="search_string"
                        placeholder="Name, city, state, phone, email..."
                        value="<?= htmlspecialchars($_POST['search_string'] ?? '') ?>">
                    <button type="submit" name="show_all" value="1" class="btn btn-outline-primary px-3">
                        <i class="fa-solid fa-list me-1"></i> Show All
                    </button>
                    <a href="?" class="btn btn-outline-primary px-3">
                        <i class="fa-solid fa-xmark me-1"></i> Clear Filters
                    </a>
                </div>
            </div>
            <div class="col-12 d-flex align-items-center pt-2">
                <div class="form-check form-switch">
                    <!-- Show inactive vendors toggle -->
                    <input class="form-check-input" type="checkbox" onclick="toggle_active(this, 'resultCount_vendors')"
                        id="show_inactive_vendors" name="show_inactive" value="1" <?= isset($_POST['show_inactive']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="show_inactive_vendors">Show Inactive
                        Vendors</label>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
// Determine if we're doing a search or showing all (default on page load)
$is_show_all_get = (isset($_GET['show_all']) && $_GET['show_all'] == '1');
$is_search = ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['search_string']) || isset($_POST['show_all'])));
$is_default = ($_SERVER['REQUEST_METHOD'] !== 'POST') || ($is_search) || $is_show_all_get;

if ($is_default) {
    if ($is_search) {
        $show_all = isset($_POST['show_all']);
        $search_string = trim($_POST['search_string'] ?? '');
    } elseif ($is_show_all_get) {
        $show_all = true;
        $search_string = '';
    } else {
        // Default on first load (GET without show_all)
        $show_all = true;
        $search_string = '';
    }


    if (!$show_all && empty($search_string)) {
        echo '<div class="alert alert-warning"><strong>Error!</strong> Please enter something to search for.</div>';
    } else {
        require_once __DIR__ . '/../models/VendorModel.php';
        $vendors = VendorModel::getFilteredVendors($db, $search_string);

        $display_label = $show_all ? 'All Vendors' : htmlspecialchars($search_string);

        if (empty($vendors)) {
            echo '<div class="alert alert-info">No vendors found matching "<strong>' . $display_label . '</strong>".</div>';
        } else {
            $active_count = count(array_filter($vendors, fn($v) => ($v['is_active'] ?? 1) == 1));
            $total_count = count($vendors);
            $initial_count = isset($_POST['show_inactive']) ? $total_count : $active_count;
            echo '<h5 class="mb-3" id="resultCount_vendors" data-active-count="' . $active_count . '" data-total-count="' . $total_count . '" data-label="' . $display_label . '">' . $initial_count . ' result(s) for "' . $display_label . '"</h5>';
            ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Zip</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Records</th>
                            <th class="text-center">Info</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vendors as $v):
                            $is_inactive = ($v['is_active'] ?? 1) == 0;
                            ?>
                            <tr
                                class="<?= $is_inactive ? 'table-secondary text-decoration-line-through opacity-50' : '' ?> <?= ($is_inactive && !isset($_POST['show_inactive'])) ? 'd-none' : '' ?>">
                                <td>
                                    <?= htmlspecialchars($v['vendor_name'] ?? '') ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($v['vendor_address'] ?? '') ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($v['vendor_city'] ?? '') ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($v['vendor_state'] ?? '') ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($v['vendor_zip'] ?? '') ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars(formatPhone($v['vendor_phone'] ?? '')) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($v['vendor_email'] ?? '') ?>
                                </td>
                                <td>
                                    <?php if ($v['usage_count'] > 0): ?>
                                        <span class="badge bg-info">
                                            <?= $v['usage_count'] ?> record(s)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">None</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="info.php?tab=vendors&vendor_id=<?= $v['vendor_id'] ?>" class="btn btn-sm btn-info"
                                        title="Vendor Info"><i class="fa-solid fa-circle-info"></i></a>
                                </td>
                                <td class="text-center text-nowrap">
                                    <!-- Edit Button -->
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editVendorModal"
                                        data-vendor-id="<?= $v['vendor_id'] ?>"
                                        data-vendor-name="<?= htmlspecialchars($v['vendor_name'] ?? '') ?>"
                                        data-vendor-address="<?= htmlspecialchars($v['vendor_address'] ?? '') ?>"
                                        data-vendor-city="<?= htmlspecialchars($v['vendor_city'] ?? '') ?>"
                                        data-vendor-state="<?= htmlspecialchars($v['vendor_state'] ?? '') ?>"
                                        data-vendor-zip="<?= htmlspecialchars($v['vendor_zip'] ?? '') ?>"
                                        data-vendor-phone="<?= htmlspecialchars(formatPhone($v['vendor_phone'] ?? '')) ?>"
                                        data-vendor-email="<?= htmlspecialchars($v['vendor_email'] ?? '') ?>"
                                        data-is-active="<?= $v['is_active'] ?? 1 ?>" title="Edit Vendor">
                                        <span class="fas fa-edit"></span>
                                    </button>

                                    <!-- Delete Button -->
                                    <?php if (isset($allow_delete) && $allow_delete && $_SESSION['is_admin']): ?>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#cannotDeleteVendorModal" data-vendor-id="<?= $v['vendor_id'] ?>"
                                            data-vendor-name="<?= htmlspecialchars($v['vendor_name'] ?? '') ?>"
                                            data-usage-count="<?= $v['usage_count'] ?>" title="Delete Vendor">
                                            <span class="fa-regular fa-trash-can"></span>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php
        } // end results else
    } // end not-empty else
} // end POST
?>

