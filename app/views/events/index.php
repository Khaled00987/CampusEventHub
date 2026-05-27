<?php /** events/index.php — Public events catalogue with photo banner */ ?>
<?php
$bannerTitle = 'Campus Events';
$bannerLead = 'Search, filter, and request tickets for published events.';
$bannerImageKey = 'event_management';
require APP_PATH . '/views/partials/page-banner.php';
?>

<div class="page-content-below-banner">
<form method="get" action="<?= url('/events') ?>" class="filter-bar reveal" role="search">
    <input type="search" name="q" placeholder="Search title or location" value="<?= e($filters['q'] ?? '') ?>" aria-label="Search events">
    <select name="category" aria-label="Category">
        <option value="">All categories</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= e($cat) ?>" <?= ($filters['category'] ?? '') === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="date_filter" aria-label="Date">
        <option value="upcoming" <?= ($filters['date_filter'] ?? '') === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
        <option value="past" <?= ($filters['date_filter'] ?? '') === 'past' ? 'selected' : '' ?>>Past</option>
        <option value="all" <?= ($filters['date_filter'] ?? 'all') === 'all' ? 'selected' : '' ?>>All dates</option>
    </select>
    <button type="submit" class="btn btn-primary">Apply filters</button>
</form>

<?php if (empty($events['items'])): ?>
    <div class="empty-state reveal">
        <p>No events match your search.</p>
        <a class="btn" href="<?= url('/events') ?>">Clear filters</a>
    </div>
<?php else: ?>
    <div class="card-grid">
        <?php foreach ($events['items'] as $event): ?>
            <article class="card reveal">
                <img src="<?= e(event_image($event['image'] ?? null)) ?>" alt="<?= e($event['title']) ?>">
                <div class="card-body">
                    <span class="badge"><?= e($event['category']) ?></span>
                    <h2><a href="<?= url('/events/' . $event['slug']) ?>"><?= e($event['title']) ?></a></h2>
                    <p class="meta">
                        <?= e(format_date($event['event_date'])) ?>
                        · <?= e($event['start_time']) ?>–<?= e($event['end_time']) ?>
                        · <?= e($event['location']) ?>
                    </p>
                    <a class="btn btn-sm" href="<?= url('/events/' . $event['slug']) ?>">View details</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <?php $pagination = $events; $baseUrl = '/events'; require APP_PATH . '/views/partials/pagination.php'; ?>
<?php endif; ?>
</div>
