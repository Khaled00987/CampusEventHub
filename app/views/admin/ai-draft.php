<?php
/**
 * admin/ai-draft.php — AI Event Draft Assistant (human-in-the-loop).
 */
$draft = $draft ?? null;
?>

<div class="dash-ai-disclaimer" role="note">
    <strong>Responsible AI:</strong> <?= e($disclaimer ?? AI_DISCLAIMER) ?>
</div>

<p class="meta" style="margin-bottom:1.25rem;">
    Longcat API: <?= !empty($apiConfigured) ? 'configured (optional polish)' : 'not configured — safe local fallback' ?>.
    No ticket or personal data is sent to AI.
</p>

<div class="dash-ai-split">
    <div class="dash-card">
        <div class="dash-card-header"><h2>Step 1 — Describe your event</h2></div>
        <form method="post" action="<?= url('/admin/ai-draft/generate') ?>" data-validate="ai-draft" novalidate>
            <?= CSRF::field() ?>
            <div class="form-group">
                <label for="event_idea">Event idea <span class="required">*</span></label>
                <textarea id="event_idea" name="event_idea" rows="3" required
                          placeholder="e.g. Sustainability workshop with recycling demos"><?= e(old('event_idea')) ?></textarea>
            </div>
            <div class="form-group">
                <label for="target_audience">Target audience <span class="required">*</span></label>
                <input type="text" id="target_audience" name="target_audience" required
                       placeholder="e.g. First-year students" value="<?= e(old('target_audience')) ?>">
            </div>
            <div class="form-group">
                <label for="tone">Tone</label>
                <select id="tone" name="tone">
                    <?php foreach (['friendly', 'formal', 'energetic', 'informative'] as $t): ?>
                        <option value="<?= e($t) ?>"><?= e(ucfirst($t)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="date_location">Date / location notes (optional)</label>
                <input type="text" id="date_location" name="date_location"
                       placeholder="e.g. March, Main Hall"
                       value="<?= e(old('date_location')) ?>">
            </div>
            <button type="submit" id="ai-draft-generate-btn" class="btn btn-primary">Generate draft suggestion</button>
        </form>
    </div>

    <div class="dash-card">
        <div class="dash-card-header"><h2>Step 2 — Review &amp; accept</h2></div>
        <?php if (empty($draft)): ?>
            <div class="dash-empty">
                <p>Generated suggestions will appear here after Step 1.</p>
            </div>
        <?php else: ?>
            <p class="dash-badge dash-badge-muted" style="margin-bottom:1rem;">
                <?= !empty($draft['from_api']) ? 'Source: Longcat API' : 'Source: local fallback' ?>
            </p>
            <form method="post" action="<?= url('/admin/ai-draft/accept') ?>">
                <?= CSRF::field() ?>
                <input type="hidden" name="log_id" value="<?= e((string) ($draft['log_id'] ?? '')) ?>">
                <div class="form-group">
                    <label for="title">Suggested title</label>
                    <input type="text" id="title" name="title" required maxlength="150" value="<?= e($draft['title'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="short_description">Short description</label>
                    <textarea id="short_description" name="short_description" rows="2" maxlength="300"><?= e($draft['short_description'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="detailed_description">Detailed description</label>
                    <textarea id="detailed_description" name="detailed_description" rows="6" required minlength="20"><?= e($draft['detailed_description'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" required value="<?= e($draft['category'] ?? 'General') ?>">
                </div>
                <p class="meta" style="margin-bottom:1rem;"><?= e($disclaimer ?? AI_DISCLAIMER) ?></p>
                <p class="meta" style="margin-bottom:1rem;">
                    Accepting creates an event with status <strong>draft</strong> in Manage Events. You can edit schedule and publish when ready.
                </p>
                <div class="dash-actions">
                    <button type="submit" class="btn btn-primary">Accept reviewed draft</button>
                    <a class="btn btn-ghost" href="<?= url('/admin/events/create') ?>">Create event manually</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
