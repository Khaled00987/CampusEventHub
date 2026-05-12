<?php /** tickets/my-tickets.php — User ticket history */ ?>
<?php if (empty($tickets)): ?>
    <div class="dash-card dash-empty">
        <p>You have not requested any tickets yet.</p>
        <a class="btn btn-primary" href="<?= url('/events') ?>">Browse events</a>
    </div>
<?php else: ?>
    <div class="dash-card dash-table-card dash-desktop-only">
        <div class="dash-table-wrap">
            <table class="dash-table dash-table--responsive">
                <thead>
                    <tr>
                        <th>Event</th><th>Date</th><th>Qty</th><th>Attendee</th>
                        <th>Status</th><th>Admin note</th><th>Requested</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $t): ?>
                        <?php
                        $status = (string) ($t['status'] ?? 'pending');
                        $badgeClass = $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning');
                        ?>
                        <tr>
                            <td data-label="Event"><a href="<?= url('/events/' . ($t['event_slug'] ?? '')) ?>"><?= e($t['event_title']) ?></a></td>
                            <td data-label="Date"><?= e(format_date($t['event_date'])) ?></td>
                            <td data-label="Qty"><?= e((string) $t['quantity']) ?></td>
                            <td data-label="Attendee"><?= e($t['attendee_name']) ?></td>
                            <td data-label="Status"><span class="dash-badge dash-badge-<?= e($badgeClass) ?>"><?= e($status) ?></span></td>
                            <td data-label="Admin note"><?= e($t['admin_note'] ?? '—') ?></td>
                            <td data-label="Requested"><?= e(format_datetime($t['created_at'])) ?></td>
                            <td class="actions" data-label="Actions">
                                <?php if ($status === 'approved'): ?>
                                    <a class="btn btn-sm btn-primary" href="<?= url('/tickets/' . $t['id'] . '/download') ?>">Download PDF</a>
                                <?php elseif ($status === 'pending'): ?>
                                    <span class="meta">PDF after approval</span>
                                <?php else: ?>
                                    <span class="meta">Not approved</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="dash-mobile-only">
        <?php foreach ($tickets as $t): ?>
            <?php
            $status = (string) ($t['status'] ?? 'pending');
            $badgeClass = $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning');
            ?>
            <article class="dash-ticket-card">
                <div class="dash-ticket-card__head">
                    <h3 class="dash-ticket-card__title"><a href="<?= url('/events/' . ($t['event_slug'] ?? '')) ?>"><?= e($t['event_title']) ?></a></h3>
                    <span class="dash-badge dash-badge-<?= e($badgeClass) ?>"><?= e($status) ?></span>
                </div>
                <dl>
                    <dt>Event date</dt><dd><?= e(format_date($t['event_date'])) ?></dd>
                    <dt>Quantity</dt><dd><?= e((string) $t['quantity']) ?></dd>
                    <dt>Attendee</dt><dd><?= e($t['attendee_name']) ?></dd>
                    <dt>Requested</dt><dd><?= e(format_datetime($t['created_at'])) ?></dd>
                    <?php if (!empty($t['admin_note'])): ?>
                        <dt>Admin note</dt><dd><?= e($t['admin_note']) ?></dd>
                    <?php endif; ?>
                </dl>
                <div class="dash-actions">
                    <?php if ($status === 'approved'): ?>
                        <a class="btn btn-primary btn-sm" href="<?= url('/tickets/' . $t['id'] . '/download') ?>">Download Ticket PDF</a>
                    <?php elseif ($status === 'pending'): ?>
                        <p class="meta">PDF available after approval.</p>
                    <?php elseif ($status === 'rejected' && !empty($t['admin_note'])): ?>
                        <p class="meta"><?= e($t['admin_note']) ?></p>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
