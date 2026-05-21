<?php
/**
 * partials/flash.php — Dismissible flash messages.
 */
$flashes = Session::getFlashes();
if ($flashes === [] && empty($errors)) {
    return;
}
?>
<div class="flash-stack" aria-live="polite">
<?php foreach ($flashes as $type => $messages):
    foreach ($messages as $message):
        $class = match ($type) {
            'success' => 'alert-success',
            'error' => 'alert-error',
            default => 'alert-info',
        };
        ?>
        <div class="alert <?= e($class) ?>" role="alert">
            <span><?= e($message) ?></span>
            <button type="button" class="alert-close" aria-label="Dismiss">&times;</button>
        </div>
    <?php endforeach;
endforeach; ?>
</div>
