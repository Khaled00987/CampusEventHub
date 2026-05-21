<?php
http_response_code(500);
require __DIR__ . '/_bootstrap.php';
$layoutWide = false;
require APP_PATH . '/views/layouts/header.php';
?>
<div class="error-page reveal">
    <h1>500 — Something went wrong</h1>
    <p>We could not complete your request. Please try again later.</p>
    <p><a class="btn btn-primary" href="<?= url('/') ?>">Return home</a></p>
</div>
<?php require APP_PATH . '/views/layouts/footer.php'; ?>
