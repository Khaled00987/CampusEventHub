<?php
/**
 * partials/dash-page-header.php — In-page dashboard header (title, subtitle, actions).
 *
 * Expects: $headerTitle, optional $headerSubtitle, $headerActionsHtml (raw safe HTML from view)
 */
$headerTitle = $headerTitle ?? ($pageTitle ?? '');
$headerSubtitle = $headerSubtitle ?? '';
$headerActionsHtml = $headerActionsHtml ?? '';
?>
<header class="dash-page-header">
    <div class="dash-page-header__text">
        <h2 class="dash-page-header__title"><?= e($headerTitle) ?></h2>
        <?php if ($headerSubtitle !== ''): ?>
            <p class="dash-page-header__subtitle"><?= e($headerSubtitle) ?></p>
        <?php endif; ?>
    </div>
    <?php if ($headerActionsHtml !== ''): ?>
        <div class="dash-page-header__actions"><?= $headerActionsHtml ?></div>
    <?php endif; ?>
</header>
