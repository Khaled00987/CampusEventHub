<?php
/** admin/events/show.php — Admin event detail */
$status = (string) ($event['status'] ?? 'draft');
$badgeClass = $status === 'published' ? 'success' : ($status === 'cancelled' ? 'danger' : 'warning');
?>

<div class="dash-card dash-detail-hero">
    <img src="<?= e(event_image($event['image'] ?? null)) ?>" alt="<?= e($event['title']) ?>" loading="lazy">
</div>

<div class="dash-meta-grid" style="margin-bottom:1.25rem;">
    <div class="dash-meta-item">
        <dt>Status</dt>
        <dd><span class="dash-badge dash-badge-<?= e($badgeClass) ?>"><?= e($status) ?></span></dd>
    </div>
    <div class="dash-meta-item">
        <dt>Date</dt>
        <dd><?= e(format_date($event['event_date'])) ?></dd>
    </div>
    <div class="dash-meta-item">
        <dt>Time</dt>
        <dd><?= e($event['start_time'] ?? '') ?> – <?= e($event['end_time'] ?? '') ?></dd>
    </div>
    <div class="dash-meta-item">
        <dt>Location</dt>
        <dd><?= e($event['location']) ?></dd>
    </div>
    <div class="dash-meta-item">
        <dt>Capacity</dt>
        <dd><?= e((string) ($event['capacity'] ?? '')) ?></dd>
    </div>
    <div class="dash-meta-item">
        <dt>Category</dt>
        <dd><?= e($event['category']) ?></dd>
    </div>
</div>

<div class="dash-grid dash-grid--2">
    <div class="dash-card">
        <div class="dash-card-header"><h2>Description</h2></div>
        <div class="body"><?= nl2br(e($event['description'])) ?></div>
    </div>
    <div class="dash-card">
        <div class="dash-card-header"><h2>Admin actions</h2></div>
        <dl class="dash-detail-list">
            <dt>Ticket requests</dt>
            <dd><?= e((string) $ticketCount) ?> linked</dd>
            <dt>Created</dt>
            <dd><?= e(format_datetime($event['created_at'])) ?></dd>
            <dt>Updated</dt>
            <dd><?= e(format_datetime($event['updated_at'])) ?></dd>
        </dl>
        <div class="dash-actions" style="margin-top:1rem;">
            <a class="btn btn-primary" href="<?= url('/admin/events/' . $event['id'] . '/edit') ?>">Edit</a>
            <?php if ($ticketCount === 0): ?>
                <form method="post" action="<?= url('/admin/events/' . $event['id'] . '/delete') ?>" data-confirm="Delete this event? This cannot be undone.">
                    <?= CSRF::field() ?>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
