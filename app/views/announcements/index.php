<?php /** announcements/index.php — Public published announcements */ ?>
<?php
$bannerTitle = 'Announcements';
$bannerLead = 'Official campus news and updates from the events team.';
$bannerImageKey = 'events_news';
require APP_PATH . '/views/partials/page-banner.php';
?>

<div class="page-content-below-banner">
<?php if (empty($announcements)): ?>
    <p class="empty-state reveal">No announcements have been published yet.</p>
<?php else: ?>
    <div class="announcements-list">
        <?php foreach ($announcements as $a): ?>
            <article class="announcement-card reveal">
                <div class="announcement-card__icon" aria-hidden="true">
                    <?php require APP_PATH . '/views/partials/announcement-icon.php'; ?>
                </div>
                <div class="announcement-card__body">
                    <h2><?= e($a['title']) ?></h2>
                    <p class="meta">Published <?= e(format_datetime($a['created_at'])) ?></p>
                    <div class="body"><?= nl2br(e($a['body'])) ?></div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
</div>
