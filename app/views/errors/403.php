<?php
http_response_code(403);
require __DIR__ . '/_bootstrap.php';
$layoutWide = false;
require APP_PATH . '/views/layouts/header.php';
?>
<div class="error-page reveal">
    <h1>403 — Access denied</h1>
    <p>You do not have permission to view this page.</p>
    <p><a class="btn btn-primary" href="<?= url('/') ?>">Return home</a></p>
</div>
<?php require APP_PATH . '/views/layouts/footer.php'; ?>
