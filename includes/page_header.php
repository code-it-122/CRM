<?php
// Reusable page header for listing and form pages.
// Optional vars before include:
// $ph_icon, $ph_title, $ph_subtitle,
// $ph_back_link, $ph_back_label,
// $ph_action_link, $ph_action_label, $ph_action_icon
?>
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-success mb-1">
            <?php if (!empty($ph_icon)): ?><i class="fa-solid <?php echo $ph_icon; ?> me-2"></i><?php endif; ?>
            <?php echo htmlspecialchars($ph_title ?? ''); ?>
        </h3>
        <?php if (!empty($ph_subtitle)): ?>
            <p class="text-muted mb-0"><?php echo htmlspecialchars($ph_subtitle); ?></p>
        <?php endif; ?>
    </div>
    <div class="d-flex gap-2">
        <?php if (!empty($ph_back_link)): ?>
            <a href="<?php echo $ph_back_link; ?>" class="btn btn-outline-success d-flex align-items-center">
                <i class="fa-solid fa-arrow-left me-2"></i> <?php echo htmlspecialchars($ph_back_label ?? 'Back'); ?>
            </a>
        <?php endif; ?>
        <?php if (!empty($ph_action_link)): ?>
            <a href="<?php echo $ph_action_link; ?>" class="btn btn-success d-flex align-items-center shadow-sm">
                <i class="fa-solid <?php echo $ph_action_icon ?? 'fa-plus'; ?> me-2"></i> <?php echo htmlspecialchars($ph_action_label ?? 'Add New'); ?>
            </a>
        <?php endif; ?>
    </div>
</div>
<?php
// Reset so a second include later on the same page doesn't reuse old values
$ph_icon = $ph_title = $ph_subtitle = null;
$ph_back_link = $ph_back_label = null;
$ph_action_link = $ph_action_label = $ph_action_icon = null;