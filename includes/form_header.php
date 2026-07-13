<?php
// Reusable Form Header
?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-success mb-1">
            <i class="fa-solid <?php echo $form_icon; ?> me-2"></i>
            <?php echo htmlspecialchars($form_title); ?>
        </h3>

        <?php if(!empty($form_subtitle)){ ?>
            <p class="text-muted mb-0">
                <?php echo htmlspecialchars($form_subtitle); ?>
            </p>
        <?php } ?>
    </div>

    <?php if(!empty($form_back_link)){ ?>
        <a href="<?php echo $form_back_link; ?>" class="btn btn-outline-success">
            <i class="fa-solid fa-arrow-left me-2"></i>
            <?php echo htmlspecialchars($form_back_label ?? "Back"); ?>
        </a>
    <?php } ?>
</div>