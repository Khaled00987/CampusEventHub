<?php /** admin/tickets/index.php */ ?>
<div class="dash-card dash-filter-card">
    <form method="get" class="filter-bar" role="search">
        <input type="search" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Search attendee or event" aria-label="Search">
        <select name="status" aria-label="Status">
            <option value="">All statuses</option>
            <?php foreach (['pending', 'approved', 'rejected', 'cancelled'] as $s): ?>
                <option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= e($s) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Apply</button>
        <a class="btn btn-ghost" href="<?= url('/admin/tickets') ?>">Reset</a>
    </form>
</div>

<?php if (empty($tickets['items'])): ?>
    <div class="dash-card dash-empty"><p>No ticket requests found.</p></div>
<?php else: ?>
    <div class="dash-card dash-table-card">
        <div class="dash-table-wrap">
            <table class="dash-table dash-table--responsive">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>User</th>
                        <th>Attendee</th>
                        <th>Qty</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets['items'] as $t): ?>
                        <?php
                        $st = (string) ($t['status'] ?? 'pending');
                        $badgeClass = $st === 'approved' ? 'success' : ($st === 'rejected' ? 'danger' : 'warning');
                        ?>
                        <tr>
                            <td data-label="Event"><?= e($t['event_title']) ?></td>
                            <td data-label="User"><?= e($t['user_name']) ?><br><span class="meta"><?= e($t['user_email'] ?? '') ?></span></td>
                            <td data-label="Attendee"><?= e($t['attendee_name'] ?? '—') ?></td>
                            <td data-label="Qty"><?= e((string) $t['quantity']) ?></td>
                            <td data-label="Status"><span class="dash-badge dash-badge-<?= e($badgeClass) ?>"><?= e($st) ?></span></td>
                            <td data-label="Requested"><?= e(format_datetime($t['created_at'])) ?></td>
                            <td class="actions" data-label="Action">
                                <a class="btn btn-sm btn-primary" href="<?= url('/admin/tickets/' . $t['id']) ?>">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php $pagination = $tickets; $baseUrl = '/admin/tickets'; require APP_PATH . '/views/partials/pagination.php'; ?>
    </div>
<?php endif; ?>
