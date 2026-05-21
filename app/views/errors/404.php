<?php
http_response_code(404);
require __DIR__ . '/_bootstrap.php';
$layoutWide = false;
require APP_PATH . '/views/layouts/header.php';
?>
<div class="error-page reveal">
    <h1>404 — Page not found</h1>
    <p>The page you requested could not be found.</p>
    <p><a class="btn btn-primary" href="<?= url('/') ?>">Return home</a></p>
</div>
<?php require APP_PATH . '/views/layouts/footer.php'; ?>
