<?php
// Reusable empty-state block for listing pages.
// Optional vars before include:
// $es_icon, $es_title, $es_desc, $es_action_link, $es_action_label
?>
<div class="text-center py-5">
    <div class="mb-3">
        <i class="fa-solid <?php echo $es_icon ?? 'fa-inbox'; ?> text-success opacity-25" style="font-size: 4rem;"></i>
    </div>
    <h5 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($es_title ?? 'Nothing here yet'); ?></h5>
    <p class="text-muted mb-3 mx-auto" style="max-width: 420px;"><?php echo htmlspecialchars($es_desc ?? ''); ?></p>
    <?php if (!empty($es_action_link)): ?>
        <a href="<?php echo $es_action_link; ?>" class="btn btn-success shadow-sm">
            <i class="fa-solid fa-plus me-2"></i><?php echo htmlspecialchars($es_action_label ?? 'Add New'); ?>
        </a>
    <?php endif; ?>
</div>
<?php
$es_icon = $es_title = $es_desc = $es_action_link = $es_action_label = null;