<?php
/**
 * partials/brand-logo.php — Logo image only (logo file includes wordmark).
 */
$logoHref = $logoHref ?? url('/');
$logoClass = $logoClass ?? '';
$logoAlt = $logoAlt ?? 'Home';
?>
<a class="brand <?= e($logoClass) ?>" href="<?= e($logoHref) ?>" aria-label="<?= e($logoAlt) ?>">
    <img src="<?= e(logo_url()) ?>" alt="" class="logo" width="200" height="48" decoding="async">
</a>
