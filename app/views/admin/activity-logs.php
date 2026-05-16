<?php /** admin/activity-logs.php */ ?>
<?php if (empty($logs['items'])): ?>
    <div class="dash-card dash-empty"><p>No activity recorded yet.</p></div>
<?php else: ?>
    <div class="dash-card dash-table-card dash-desktop-only">
        <div class="dash-table-wrap">
            <table class="dash-table dash-table--responsive">
                <thead>
                    <tr>
                        <th>When</th><th>User</th><th>Action</th><th>Entity</th><th>Details</th><th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs['items'] as $log): ?>
                        <tr>
                            <td data-label="When"><?= e(format_datetime($log['created_at'])) ?></td>
                            <td data-label="User"><?= e($log['user_name'] ?? 'Guest') ?></td>
                            <td data-label="Action"><span class="dash-badge dash-badge-muted"><?= e($log['action']) ?></span></td>
                            <td data-label="Entity"><?= e(trim(($log['entity_type'] ?? '') . ' #' . ($log['entity_id'] ?? ''))) ?></td>
                            <td data-label="Details"><?= e($log['details']) ?></td>
                            <td data-label="IP"><?= e($log['ip_address']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="dash-mobile-only">
        <?php foreach ($logs['items'] as $log): ?>
            <article class="dash-ticket-card">
                <div class="dash-ticket-card__head">
                    <strong><?= e($log['action']) ?></strong>
                    <span class="meta"><?= e(format_datetime($log['created_at'])) ?></span>
                </div>
                <dl>
                    <dt>User</dt><dd><?= e($log['user_name'] ?? 'Guest') ?></dd>
                    <dt>Entity</dt><dd><?= e(trim(($log['entity_type'] ?? '') . ' #' . ($log['entity_id'] ?? ''))) ?></dd>
                    <dt>Details</dt><dd><?= e($log['details']) ?></dd>
                    <dt>IP</dt><dd><?= e($log['ip_address']) ?></dd>
                </dl>
            </article>
        <?php endforeach; ?>
    </div>
    <div class="dash-card" style="padding:0;">
        <?php $pagination = $logs; $baseUrl = '/admin/activity-logs'; require APP_PATH . '/views/partials/pagination.php'; ?>
    </div>
<?php endif; ?>
