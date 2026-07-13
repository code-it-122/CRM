<?php
include "../includes/header.php";
include "../database/db.php";

$sql = "SELECT * FROM leads ORDER BY lead_id DESC";
$result = mysqli_query($conn, $sql);
$total_leads = mysqli_num_rows($result);

$status_map = [
    'new'         => ['label' => 'New',         'badge' => 'bg-info bg-opacity-10 text-info border border-info-subtle',           'icon' => 'fa-circle-plus'],
    'contacted'   => ['label' => 'Contacted',    'badge' => 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle', 'icon' => 'fa-phone'],
    'interested'  => ['label' => 'Interested',   'badge' => 'bg-primary bg-opacity-10 text-primary border border-primary-subtle',  'icon' => 'fa-star'],
    'converted'   => ['label' => 'Converted',    'badge' => 'bg-success bg-opacity-10 text-success border border-success-subtle',  'icon' => 'fa-circle-check'],
    'lost'        => ['label' => 'Lost',         'badge' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle', 'icon' => 'fa-circle-xmark'],
];
?>

<div class="admin-container">
    <?php
    if ($_SESSION['role'] == 'admin') {
        include "../includes/admin_sidebar.php";
    } elseif ($_SESSION['role'] == 'sales') {
        include "../includes/sales_sidebar.php";
    }
    ?>

    <div class="view py-4 px-4">
        <?php
        $ph_icon = 'fa-bullseye';
        $ph_title = 'Leads';
        $ph_subtitle = $total_leads . ' lead' . ($total_leads == 1 ? '' : 's') . ' in pipeline';
        $ph_action_link = 'add_lead.php';
        $ph_action_label = 'Add Lead';
        $ph_action_icon = 'fa-plus';
        include "../includes/page_header.php";
        ?>

        <?php if ($total_leads > 0): ?>
            <!-- Search -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body py-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" id="leadSearch" class="form-control bg-light border-start-0 ps-0"
                               placeholder="Search leads by name, source or requirement...">
                    </div>
                </div>
            </div>

            <!-- Lead Cards -->
            <div class="row g-4" id="leadGrid">
                <?php while ($l = mysqli_fetch_assoc($result)):
                    $initial = strtoupper(substr(trim($l['name']), 0, 1));
                    $searchBlob = strtolower($l['name'] . ' ' . $l['source'] . ' ' . $l['requirement']);
                    $status_info = $status_map[$l['status']] ?? $status_map['new'];
                    $is_overdue = !empty($l['follow_up_date']) && strtotime($l['follow_up_date']) < strtotime(date('Y-m-d')) && !in_array($l['status'], ['converted', 'lost']);
                ?>
                <div class="col-md-6 col-lg-4 lead-card-wrap" data-search="<?php echo htmlspecialchars($searchBlob); ?>">
                    <div class="card border-0 shadow-sm rounded-3 h-100 card-animate <?php echo $is_overdue ? 'border-start border-danger border-4' : ''; ?>">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center overflow-hidden">
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold me-3"
                                         style="width: 46px; height: 46px; flex-shrink: 0;">
                                        <?php echo $initial ?: '?'; ?>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="fw-bold text-dark mb-0 text-truncate"><?php echo htmlspecialchars($l['name']); ?></h6>
                                        <small class="text-muted text-truncate d-block"><?php echo htmlspecialchars($l['source'] ?: 'Unknown source'); ?></small>
                                    </div>
                                </div>
                                <span class="badge <?php echo $status_info['badge']; ?> rounded-pill px-2 py-1 flex-shrink-0">
                                    <i class="fa-solid <?php echo $status_info['icon']; ?> me-1"></i><?php echo $status_info['label']; ?>
                                </span>
                            </div>

                            <?php if (!empty($l['phone'])): ?>
                            <div class="mb-2 text-truncate">
                                <i class="fa-solid fa-phone text-success me-2" style="width: 16px;"></i>
                                <span class="text-dark small"><?php echo htmlspecialchars($l['phone']); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($l['email'])): ?>
                            <div class="mb-2 text-truncate">
                                <i class="fa-solid fa-envelope text-success me-2" style="width: 16px;"></i>
                                <span class="text-dark small"><?php echo htmlspecialchars($l['email']); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($l['requirement'])): ?>
                            <div class="mb-3">
                                <i class="fa-solid fa-clipboard-list text-success me-2" style="width: 16px;"></i>
                                <span class="text-muted small"><?php echo htmlspecialchars(mb_strimwidth($l['requirement'], 0, 70, '...')); ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($l['follow_up_date'])): ?>
                            <div class="d-flex align-items-center justify-content-between border-top pt-3 mb-2">
                                <small class="text-muted">Follow-up</small>
                                <span class="fw-semibold small <?php echo $is_overdue ? 'text-danger' : 'text-dark'; ?>">
                                    <?php if ($is_overdue): ?><i class="fa-solid fa-triangle-exclamation me-1"></i><?php endif; ?>
                                    <?php echo date('d M Y', strtotime($l['follow_up_date'])); ?>
                                </span>
                            </div>
                            <?php endif; ?>

                            <div class="d-flex gap-2 pt-2 border-top">
                                <a href="edit_lead.php?id=<?php echo $l['lead_id']; ?>" class="btn btn-sm btn-outline-success flex-fill">
                                    <i class="fa-solid fa-pen me-1"></i> Edit
                                </a>
                                <a href="delete_lead.php?id=<?php echo $l['lead_id']; ?>" class="btn btn-sm btn-outline-danger flex-fill"
                                   onclick="return confirm('Are you sure you want to delete this lead?');">
                                    <i class="fa-solid fa-trash me-1"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <div id="noResults" class="text-center py-5 d-none">
                <i class="fa-solid fa-magnifying-glass text-muted opacity-25 mb-3" style="font-size: 3rem;"></i>
                <p class="text-muted mb-0">No leads match your search.</p>
            </div>

        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-3">
                <?php
                $es_icon = 'fa-bullseye';
                $es_title = 'No leads yet';
                $es_desc = 'Start tracking prospects by adding your first lead.';
                $es_action_link = 'add_lead.php';
                $es_action_label = 'Add Lead';
                include "../includes/empty_state.php";
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('leadSearch');
    if (!searchInput) return;
    const cards = document.querySelectorAll('.lead-card-wrap');
    const noResults = document.getElementById('noResults');

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        let visibleCount = 0;
        cards.forEach(card => {
            const match = card.getAttribute('data-search').includes(query);
            card.classList.toggle('d-none', !match);
            if (match) visibleCount++;
        });
        noResults.classList.toggle('d-none', visibleCount !== 0);
    });
});
</script>

<?php include "../includes/footer.php"; ?>