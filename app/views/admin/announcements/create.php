<?php /** admin/announcements/create.php */ ?>
<?php require APP_PATH . '/views/partials/form-errors.php'; ?>
<form method="post" action="<?= url('/admin/announcements/store') ?>" data-validate="announcement" novalidate>
    <?= CSRF::field() ?>
    <div class="dash-card dash-form-card">
        <div class="dash-form-body">
            <div class="dash-form-section">
                <h3>Announcement content</h3>
                <?php $item = []; include APP_PATH . '/views/admin/announcements/_form.php'; ?>
            </div>
        </div>
        <div class="dash-form-footer">
            <button type="submit" class="btn btn-primary">Save announcement</button>
            <a class="btn btn-ghost" href="<?= url('/admin/announcements') ?>">Cancel</a>
        </div>
    </div>
</form>
