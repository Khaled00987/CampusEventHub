<?php
/**
 * help-assistant/index.php — Smart Help Assistant (public layout).
 */
$bannerTitle = 'Smart Help Assistant';
$bannerLead = 'Answers about using ' . APP_NAME . ' — events, tickets, login, and announcements.';
$bannerImageKey = 'organizer_team';
require APP_PATH . '/views/partials/page-banner.php';
?>

<div class="page-content-below-banner">
<div class="pub-help-layout">
    <aside class="pub-help-info">
        <div class="pub-help-info__card">
            <h2>How this assistant works</h2>
            <p>Ask practical questions about browsing events, requesting tickets, using your dashboard, or reading announcements.</p>
            <ul class="pub-help-list">
                <li>FAQ answers are checked first for accuracy.</li>
                <li>Optional AI polish may refine matched answers when configured.</li>
                <li>It does not access ticket personal data or unrelated topics.</li>
            </ul>
            <p class="pub-help-disclaimer" role="note">
                AI may assist with system guidance, but important decisions should be reviewed by a human.
            </p>
        </div>
    </aside>
    <div class="pub-help-main">
        <div class="pub-help-panel">
            <form id="help-form" data-help-url="<?= url('/help-assistant/ask') ?>" novalidate>
                <?= CSRF::field() ?>
                <div class="form-group">
                    <label for="question">Your question</label>
                    <input type="text" id="question" name="question" required maxlength="500"
                           placeholder="e.g. How do I request tickets?"
                           aria-describedby="help-response">
                </div>
                <button type="submit" class="btn btn-primary">Ask assistant</button>
            </form>
            <div id="help-response" class="help-response pub-help-answer" aria-live="polite"></div>
        </div>
    </div>
</div>

<?php if (!empty($sampleFaqs)): ?>
    <section class="pub-faq-samples">
        <h2>Example questions</h2>
        <ul>
            <?php foreach ($sampleFaqs as $faq): ?>
                <?php $qText = is_array($faq) ? (string) ($faq['question'] ?? '') : (string) $faq; ?>
                <?php if ($qText === '') {
                    continue;
                } ?>
                <li><button type="button" class="faq-chip" data-question="<?= e($qText) ?>"><?= e($qText) ?></button></li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>
</div>
