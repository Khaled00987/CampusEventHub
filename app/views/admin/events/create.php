<?php
/** admin/events/create.php */
$prefill = Session::get('ai_prefill_optional', []);
?>
<?php if (!empty($prefill)): ?>
    <div class="dash-ai-disclaimer">
        Optional prefill from your <strong>reviewed AI draft</strong>. Complete all fields and click Save — nothing is auto-published.
    </div>
<?php endif; ?>
<?php require APP_PATH . '/views/partials/form-errors.php'; ?>
<form method="post" action="<?= url('/admin/events/store') ?>" data-validate="event" enctype="multipart/form-data" novalidate>
    <?= CSRF::field() ?>
    <div class="dash-card dash-form-card">
        <div class="dash-form-body">
            <?php include APP_PATH . '/views/admin/events/_form.php'; ?>
        </div>
        <div class="dash-form-footer">
            <button type="submit" class="btn btn-primary">Save event</button>
            <a class="btn btn-ghost" href="<?= url('/admin/events') ?>">Cancel</a>
        </div>
    </div>
</form>
