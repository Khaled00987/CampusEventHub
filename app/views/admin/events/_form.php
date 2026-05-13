<?php
/**
 * admin/events/_form.php — Shared event form fields (grouped sections).
 */
$data = $prefill ?? $event ?? [];
$currentImage = $data['image'] ?? null;
?>
<div class="dash-form-section">
    <h3>Basic information</h3>
    <div class="dash-form-grid">
        <div class="form-group form-group--full">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required minlength="5" maxlength="150"
                   value="<?= e(old('title', $data['title'] ?? '')) ?>">
        </div>
        <div class="form-group form-group--full">
            <label for="description">Description</label>
            <textarea id="description" name="description" required minlength="20" rows="6"><?= e(old('description', $data['description'] ?? '')) ?></textarea>
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <input type="text" id="category" name="category" required value="<?= e(old('category', $data['category'] ?? 'General')) ?>">
        </div>
    </div>
</div>

<div class="dash-form-section">
    <h3>Schedule &amp; location</h3>
    <div class="dash-form-grid">
        <div class="form-group form-group--full">
            <label for="location">Location</label>
            <input type="text" id="location" name="location" required value="<?= e(old('location', $data['location'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label for="event_date">Event date</label>
            <input type="date" id="event_date" name="event_date" required value="<?= e(old('event_date', $data['event_date'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label for="start_time">Start time</label>
            <input type="time" id="start_time" name="start_time" required value="<?= e(old('start_time', $data['start_time'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label for="end_time">End time</label>
            <input type="time" id="end_time" name="end_time" required value="<?= e(old('end_time', $data['end_time'] ?? '')) ?>">
        </div>
    </div>
</div>

<div class="dash-form-section">
    <h3>Capacity &amp; status</h3>
    <div class="dash-form-grid">
        <div class="form-group">
            <label for="capacity">Capacity (1–5000)</label>
            <input type="number" id="capacity" name="capacity" min="1" max="5000" required value="<?= e(old('capacity', (string) ($data['capacity'] ?? '100'))) ?>">
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                <?php foreach (['draft', 'published', 'cancelled'] as $s): ?>
                    <option value="<?= $s ?>" <?= (old('status', $data['status'] ?? 'draft')) === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<div class="dash-form-section">
    <h3>Event image</h3>
    <?php if ($currentImage): ?>
        <div class="dash-upload-preview">
            <img src="<?= e(event_image($currentImage)) ?>" alt="Current event image" width="240" height="140" loading="lazy">
            <p class="meta">Current file: <?= e($currentImage) ?></p>
        </div>
    <?php endif; ?>
    <div class="form-group">
        <label for="image">Upload image</label>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
        <p class="form-hint">JPG, PNG, or WEBP. Maximum 2MB.</p>
    </div>
</div>
