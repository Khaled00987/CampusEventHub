<?php
/**
 * dashboard/index.php — Standard user dashboard.
 */
$tickets = $tickets ?? [];
$ticketSummary = $ticketSummary ?? ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
$upcomingEvents = $upcomingEvents ?? [];
$announcements = $announcements ?? [];
$userName = Auth::user()['name'] ?? 'User';
$upcomingCount = (int) ($upcomingEventsCount ?? 0);
$annCount = (int) ($announcementsCount ?? 0);
$recentTickets = array_slice($tickets, 0, 5);
?>
<section class="dash-welcome-card dash-card">
    <div>
        <h2>Welcome, <?= e($userName) ?></h2>
        <p>Discover upcoming campus events, request tickets, and track approvals from your personal dashboard.</p>
    </div>
    <div class="dash-actions">
        <a class="btn btn-primary" href="<?= url('/events') ?>">Browse events</a>
        <a class="btn btn-ghost" href="<?= url('/my-tickets') ?>">My tickets</a>
    </div>
</section>

<div class="dash-kpi-grid">
    <article class="dash-kpi-card">
        <span class="dash-kpi-card__icon dash-kpi-card__icon--events" aria-hidden="true"><?php $dashIcon = 'events'; require APP_PATH . '/views/partials/dash-icon.php'; ?></span>
        <div>
            <span class="dash-kpi-card__value"><?= e((string) $upcomingCount) ?></span>
            <span class="dash-kpi-card__label">Upcoming events</span>
            <span class="dash-kpi-card__hint">Published on campus</span>
        </div>
    </article>
    <article class="dash-kpi-card">
        <span class="dash-kpi-card__icon dash-kpi-card__icon--tickets" aria-hidden="true"><?php $dashIcon = 'ticket'; require APP_PATH . '/views/partials/dash-icon.php'; ?></span>
        <div>
            <span class="dash-kpi-card__value"><?= e((string) ($ticketSummary['total'] ?? 0)) ?></span>
            <span class="dash-kpi-card__label">My ticket requests</span>
            <span class="dash-kpi-card__hint">All time</span>
        </div>
    </article>
    <article class="dash-kpi-card">
        <span class="dash-kpi-card__icon dash-kpi-card__icon--tickets" aria-hidden="true"><?php $dashIcon = 'ticket'; require APP_PATH . '/views/partials/dash-icon.php'; ?></span>
        <div>
            <span class="dash-kpi-card__value"><?= e((string) ($ticketSummary['approved'] ?? 0)) ?></span>
            <span class="dash-kpi-card__label">Approved tickets</span>
            <span class="dash-kpi-card__hint">PDF available when approved</span>
        </div>
    </article>
    <article class="dash-kpi-card">
        <span class="dash-kpi-card__icon dash-kpi-card__icon--news" aria-hidden="true"><?php $dashIcon = 'news'; require APP_PATH . '/views/partials/dash-icon.php'; ?></span>
        <div>
            <span class="dash-kpi-card__value"><?= e((string) $annCount) ?></span>
            <span class="dash-kpi-card__label">Announcements</span>
            <span class="dash-kpi-card__hint">Campus updates</span>
        </div>
    </article>
</div>

<div class="dash-status-row">
    <div class="dash-status-chip">
        <strong><?= e((string) ($ticketSummary['pending'] ?? 0)) ?></strong>
        <span>Pending</span>
    </div>
    <div class="dash-status-chip">
        <strong><?= e((string) ($ticketSummary['approved'] ?? 0)) ?></strong>
        <span>Approved</span>
    </div>
    <div class="dash-status-chip">
        <strong><?= e((string) ($ticketSummary['rejected'] ?? 0)) ?></strong>
        <span>Rejected</span>
    </div>
</div>

<div class="dash-grid dash-grid--2">
    <div class="dash-card">
        <div class="dash-card-header">
            <h2>Upcoming events</h2>
            <a class="btn btn-sm" href="<?= url('/events') ?>">Browse all</a>
        </div>
        <?php if (empty($upcomingEvents)): ?>
            <div class="dash-empty"><p>No upcoming published events.</p></div>
        <?php else: ?>
            <ul class="dash-link-list">
                <?php foreach ($upcomingEvents as $ev): ?>
                    <li>
                        <a href="<?= url('/events/' . $ev['slug']) ?>"><?= e($ev['title']) ?></a>
                        <span class="meta"> — <?= e(format_date($ev['event_date'])) ?> · <?= e($ev['location']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="dash-card">
        <div class="dash-card-header">
            <h2>Recent ticket requests</h2>
            <a class="btn btn-sm" href="<?= url('/my-tickets') ?>">View all</a>
        </div>
        <?php if (empty($recentTickets)): ?>
            <div class="dash-empty">
                <p>No ticket requests yet.</p>
                <a class="btn btn-primary btn-sm" href="<?= url('/events') ?>">Find an event</a>
            </div>
        <?php else: ?>
            <ul class="dash-link-list">
                <?php foreach ($recentTickets as $t): ?>
                    <?php $st = (string) ($t['status'] ?? 'pending'); ?>
                    <li>
                        <a href="<?= url('/events/' . ($t['event_slug'] ?? '')) ?>"><?= e($t['event_title']) ?></a>
                        <span class="dash-badge dash-badge-<?= $st === 'approved' ? 'success' : ($st === 'rejected' ? 'danger' : 'warning') ?>"><?= e($st) ?></span>
                        <span class="meta"> · <?= e(format_datetime($t['created_at'])) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<div class="dash-grid dash-grid--2">
    <div class="dash-card">
        <div class="dash-card-header">
            <h2>Latest announcements</h2>
            <a class="btn btn-sm" href="<?= url('/announcements') ?>">All news</a>
        </div>
        <?php if (empty($announcements)): ?>
            <div class="dash-empty"><p>No announcements published.</p></div>
        <?php else: ?>
            <?php foreach ($announcements as $a): ?>
                <article style="margin-bottom:1rem;">
                    <h3 style="margin:0 0 0.35rem;font-size:1rem;"><?= e($a['title']) ?></h3>
                    <p class="meta" style="margin:0 0 0.35rem;"><?= e(format_datetime($a['created_at'])) ?></p>
                    <p style="margin:0;font-size:0.875rem;color:var(--dash-muted);"><?= e(substr($a['body'], 0, 120)) ?>…</p>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="dash-help-cta">
        <div>
            <h3 style="margin:0 0 0.35rem;font-size:1.0625rem;">Need help using <?= e(APP_NAME) ?>?</h3>
            <p class="meta" style="margin:0;">Ask about events, tickets, login, and announcements.</p>
        </div>
        <a class="btn btn-primary" href="<?= url('/help-assistant') ?>">Open Help Assistant</a>
    </div>
</div>
