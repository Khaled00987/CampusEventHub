<?php /** admin/events/edit.php */ ?>
<?php require APP_PATH . '/views/partials/form-errors.php'; ?>
<form method="post" action="<?= url('/admin/events/' . $event['id'] . '/update') ?>" data-validate="event" enctype="multipart/form-data" novalidate>
    <?= CSRF::field() ?>
    <div class="dash-card dash-form-card">
        <div class="dash-form-body">
            <?php $prefill = $event; include APP_PATH . '/views/admin/events/_form.php'; ?>
        </div>
        <div class="dash-form-footer">
            <button type="submit" class="btn btn-primary">Update event</button>
            <a class="btn btn-ghost" href="<?= url('/admin/events/' . $event['id']) ?>">Cancel</a>
        </div>
    </div>
</form>
