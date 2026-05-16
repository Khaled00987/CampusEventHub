<?php
/** admin/tickets/show.php */
$status = (string) ($ticket['status'] ?? 'pending');
$badgeClass = $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning');
?>

<div class="dash-grid dash-grid--2">
    <div class="dash-card">
        <div class="dash-card-header"><h2>Request details</h2></div>
        <dl class="dash-detail-list">
            <dt>Event</dt>
            <dd><?= e($ticket['event_title']) ?></dd>
            <dt>Account</dt>
            <dd><?= e($ticket['user_name']) ?> (<?= e($ticket['user_email']) ?>)</dd>
            <dt>Quantity</dt>
            <dd><?= e((string) $ticket['quantity']) ?></dd>
            <dt>Attendee</dt>
            <dd><?= e($ticket['attendee_name']) ?> — <?= e($ticket['attendee_email']) ?></dd>
            <dt>Request note</dt>
            <dd><?= e($ticket['note'] ?: '—') ?></dd>
            <dt>Status</dt>
            <dd><span class="dash-badge dash-badge-<?= e($badgeClass) ?>"><?= e($status) ?></span></dd>
        </dl>
        <?php if ($status === 'approved'): ?>
            <p style="margin-top:1rem;">
                <a class="btn btn-primary" href="<?= url('/tickets/' . $ticket['id'] . '/download') ?>">Download Ticket PDF</a>
            </p>
        <?php endif; ?>
    </div>

    <div class="dash-card">
        <div class="dash-card-header"><h2>Update status</h2></div>
        <?php require APP_PATH . '/views/partials/form-errors.php'; ?>
        <form method="post" action="<?= url('/admin/tickets/' . $ticket['id'] . '/status') ?>">
            <?= CSRF::field() ?>
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" required>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="form-group">
                <label for="admin_note">Admin note</label>
                <textarea name="admin_note" id="admin_note" rows="4"><?= e(old('admin_note', $ticket['admin_note'] ?? '')) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save decision</button>
        </form>
        <?php if (!empty($ticket['admin_note'])): ?>
            <div class="dash-ai-disclaimer" style="margin-top:1rem;">
                <strong>Current admin note:</strong> <?= e($ticket['admin_note']) ?>
            </div>
        <?php endif; ?>
    </div>
</div>
