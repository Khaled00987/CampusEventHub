<?php
/**
 * events/show.php — Event detail, hero image, ticket panel or login CTA.
 * Admins see a notice instead of the ticket request form.
 */
?>
<article class="event-detail">
    <img class="event-hero" src="<?= e(event_image($event['image'] ?? null)) ?>" alt="<?= e($event['title']) ?>">

    <header class="event-header">
        <span class="badge"><?= e($event['category']) ?></span>
        <h1><?= e($event['title']) ?></h1>
        <p class="meta">
            <?= e(format_date($event['event_date'])) ?>
            · <?= e($event['start_time']) ?>–<?= e($event['end_time']) ?>
            · <?= e($event['location']) ?>
        </p>
    </header>

    <div class="event-body">
        <?= nl2br(e($event['description'])) ?>
    </div>

    <p><strong>Capacity:</strong> <?= e((string) $event['capacity']) ?></p>

    <section class="ticket-panel">
        <?php if (!empty($isAdmin)): ?>
            <div class="alert alert-info" role="note">
                You are logged in as an <strong>administrator</strong>. Manage tickets from
                <a href="<?= url('/admin/tickets') ?>">Admin → Tickets</a>. Ticket requests are for standard users only.
            </div>
        <?php elseif (Auth::check()): ?>
            <?php if ($hasActiveRequest): ?>
                <div class="alert alert-info">
                    You already have a pending or approved request for this event.
                    <a href="<?= url('/my-tickets') ?>">View My Tickets</a>
                </div>
            <?php else: ?>
                <h2>Request tickets</h2>
                <?php require APP_PATH . '/views/partials/form-errors.php'; ?>
                <form method="post" action="<?= url('/events/' . $event['id'] . '/request-ticket') ?>"
                      class="form-card" data-validate="ticket" novalidate>
                    <?= CSRF::field() ?>
                    <div class="form-group">
                        <label for="quantity">Quantity (1–5)</label>
                        <input type="number" id="quantity" name="quantity" min="1" max="5" required
                               value="<?= e(old('quantity', '1')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="attendee_name">Attendee name</label>
                        <input type="text" id="attendee_name" name="attendee_name" required
                               value="<?= e(old('attendee_name', Auth::user()['name'] ?? '')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="attendee_email">Attendee email</label>
                        <input type="email" id="attendee_email" name="attendee_email" required
                               value="<?= e(old('attendee_email', Auth::user()['email'] ?? '')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="note">Note (optional, max 500)</label>
                        <textarea id="note" name="note" maxlength="500"><?= e(old('note')) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit request</button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <a class="btn btn-primary" href="<?= url('/login') ?>">Log in</a> to request tickets for this event.
                <a href="<?= url('/register') ?>">Register</a> if you do not have an account.
            </div>
        <?php endif; ?>
    </section>
</article>
