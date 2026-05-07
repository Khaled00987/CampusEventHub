<?php
/**
 * partials/page-banner.php — Reusable hero banner for static public pages.
 *
 * Background image sits on the in-container frame (not full-bleed section).
 * Expects: $bannerTitle, $bannerLead, optional $bannerImageKey (campus_static_image key)
 */
$bannerTitle = $bannerTitle ?? '';
$bannerLead = $bannerLead ?? '';
$bannerImageKey = $bannerImageKey ?? 'hero';
$bannerImageUrl = campus_static_image($bannerImageKey);
?>
<section class="page-banner reveal" aria-labelledby="page-banner-title">
    <div class="page-banner__frame" style="background-image: url('<?= e($bannerImageUrl) ?>')">
        <div class="page-banner__overlay" aria-hidden="true"></div>
        <div class="page-banner__content">
            <h1 id="page-banner-title"><?= e($bannerTitle) ?></h1>
            <?php if ($bannerLead !== ''): ?>
                <p class="page-banner__lead"><?= e($bannerLead) ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>
