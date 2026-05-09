<?php
/**
 * dashboard/admin.php — Admin overview: KPIs, pending tickets, activity, quick actions.
 */
$stats = $stats ?? [];
$pendingTickets = $pendingTickets ?? [];
$recentActivity = $recentActivity ?? [];
$recentEvents = $recentEvents ?? [];
$adminName = Auth::user()['name'] ?? 'Admin';
$totalEvents = (int) ($stats['total_events'] ?? 0);
$pendingCount = (int) ($stats['pending_tickets'] ?? 0);
$publishedAnnouncements = (int) ($stats['published_announcements'] ?? 0);
$aiSuggestions = (int) ($stats['ai_suggestions'] ?? 0);
?>
<section class="dash-welcome-card dash-card">
    <div>
        <h2>Welcome back, <?= e($adminName) ?></h2>
        <p>Manage campus events, review ticket requests, and monitor activity from one organised workspace.</p>
    </div>
    <div class="dash-actions">
        <a class="btn btn-primary" href="<?= url('/admin/events/create') ?>">Create event</a>
        <a class="btn btn-ghost" href="<?= url('/admin/tickets') ?>">View tickets</a>
    </div>
</section>

<div class="dash-kpi-grid">
    <article class="dash-kpi-card">
        <span class="dash-kpi-card__icon dash-kpi-card__icon--events" aria-hidden="true"><?php $dashIcon = 'events'; require APP_PATH . '/views/partials/dash-icon.php'; ?></span>
        <div>
            <span class="dash-kpi-card__value"><?= e((string) $totalEvents) ?></span>
            <span class="dash-kpi-card__label">Total events</span>
            <span class="dash-kpi-card__hint"><?= e((string) ($stats['published_events'] ?? 0)) ?> published</span>
        </div>
    </article>
    <article class="dash-kpi-card">
        <span class="dash-kpi-card__icon dash-kpi-card__icon--tickets" aria-hidden="true"><?php $dashIcon = 'ticket'; require APP_PATH . '/views/partials/dash-icon.php'; ?></span>
        <div>
            <span class="dash-kpi-card__value"><?= e((string) $pendingCount) ?></span>
            <span class="dash-kpi-card__label">Pending tickets</span>
            <span class="dash-kpi-card__hint">Awaiting review</span>
        </div>
    </article>
    <article class="dash-kpi-card">
        <span class="dash-kpi-card__icon dash-kpi-card__icon--news" aria-hidden="true"><?php $dashIcon = 'news'; require APP_PATH . '/views/partials/dash-icon.php'; ?></span>
        <div>
            <span class="dash-kpi-card__value"><?= e((string) $publishedAnnouncements) ?></span>
            <span class="dash-kpi-card__label">Published announcements</span>
            <span class="dash-kpi-card__hint">Live on public site</span>
        </div>
    </article>
    <article class="dash-kpi-card">
        <span class="dash-kpi-card__icon dash-kpi-card__icon--ai" aria-hidden="true"><?php $dashIcon = 'ai'; require APP_PATH . '/views/partials/dash-icon.php'; ?></span>
        <div>
            <span class="dash-kpi-card__value"><?= e((string) $aiSuggestions) ?></span>
            <span class="dash-kpi-card__label">AI suggestions</span>
            <span class="dash-kpi-card__hint">Logged interactions</span>
        </div>
    </article>
</div>

<div class="dash-grid dash-grid--main">
    <div>
        <div class="dash-card">
            <div class="dash-card-header">
                <h2>Pending ticket requests</h2>
                <a class="btn btn-sm" href="<?= url('/admin/tickets?status=pending') ?>">View all</a>
            </div>
            <?php if (empty($pendingTickets)): ?>
                <div class="dash-empty">
                    <p>No pending tickets right now.</p>
                </div>
            <?php else: ?>
                <div class="dash-table-wrap dash-desktop-only">
                    <table class="dash-table dash-table--responsive">
                        <thead>
                            <tr><th>Event</th><th>User</th><th>Qty</th><th></th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingTickets as $t): ?>
                                <tr>
                                    <td data-label="Event"><?= e($t['event_title']) ?></td>
                                    <td data-label="User"><?= e($t['user_name']) ?></td>
                                    <td data-label="Qty"><?= e((string) $t['quantity']) ?></td>
                                    <td class="actions" data-label="Action">
                                        <a class="btn btn-sm btn-primary" href="<?= url('/admin/tickets/' . $t['id']) ?>">Review</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="dash-card">
            <div class="dash-card-header">
                <h2>Recent events</h2>
                <a class="btn btn-sm" href="<?= url('/admin/events') ?>">Manage</a>
            </div>
            <?php if (empty($recentEvents)): ?>
                <div class="dash-empty">
                    <p>No events yet.</p>
                    <a class="btn btn-primary btn-sm" href="<?= url('/admin/events/create') ?>">Create event</a>
                </div>
            <?php else: ?>
                <?php foreach ($recentEvents as $ev): ?>
                    <div class="dash-event-mini-card">
                        <img src="<?= e(event_image($ev['image'] ?? null)) ?>" alt="" width="48" height="36" loading="lazy">
                        <div>
                            <a href="<?= url('/admin/events/' . $ev['id']) ?>"><?= e($ev['title']) ?></a>
                            <?php
                            $evSt = (string) ($ev['status'] ?? 'draft');
                            $evBadge = $evSt === 'published' ? 'success' : ($evSt === 'cancelled' ? 'danger' : 'warning');
                            ?>
                            <p class="meta"><?= e(format_date($ev['event_date'])) ?> · <span class="dash-badge dash-badge-<?= e($evBadge) ?>"><?= e($evSt) ?></span></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div>
        <div class="dash-card">
            <div class="dash-card-header">
                <h2>Recent activity</h2>
                <a class="btn btn-sm" href="<?= url('/admin/activity-logs') ?>">Full log</a>
            </div>
            <?php if (empty($recentActivity)): ?>
                <div class="dash-empty"><p>No activity logged yet.</p></div>
            <?php else: ?>
                <ul class="dash-timeline">
                    <?php foreach ($recentActivity as $log): ?>
                        <li>
                            <span class="dash-timeline__action"><?= e($log['action']) ?></span>
                            <span class="dash-timeline__meta">
                                <?= e($log['user_name'] ?? 'System') ?>
                                · <?= e(trim(($log['entity_type'] ?? '') . ($log['entity_id'] ? ' #' . $log['entity_id'] : ''))) ?>
                                · <?= e(format_datetime($log['created_at'])) ?>
                            </span>
                            <?php if (!empty($log['details'])): ?>
                                <span class="dash-timeline__meta"><?= e($log['details']) ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="dash-card">
            <div class="dash-card-header"><h2>Quick actions</h2></div>
            <div class="dash-quick-actions">
                <a class="btn btn-primary" href="<?= url('/admin/events/create') ?>">Create event</a>
                <a class="btn" href="<?= url('/admin/announcements/create') ?>">Add announcement</a>
                <a class="btn" href="<?= url('/admin/ai-draft') ?>">AI draft assistant</a>
                <a class="btn btn-ghost" href="<?= url('/') ?>">View public site</a>
            </div>
        </div>
    </div>
</div>
