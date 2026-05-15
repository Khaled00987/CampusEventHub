<?php $item = $item ?? []; ?>
<div class="form-group">
    <label for="title">Title</label>
    <input type="text" name="title" id="title" required minlength="5" maxlength="150" value="<?= e(old('title', $item['title'] ?? '')) ?>">
</div>
<div class="form-group">
    <label for="body">Body</label>
    <textarea name="body" id="body" required minlength="20" rows="8"><?= e(old('body', $item['body'] ?? '')) ?></textarea>
</div>
<div class="form-group">
    <label for="status">Status</label>
    <select name="status" id="status">
        <option value="draft" <?= (old('status', $item['status'] ?? '') === 'draft') ? 'selected' : '' ?>>draft</option>
        <option value="published" <?= (old('status', $item['status'] ?? '') === 'published') ? 'selected' : '' ?>>published</option>
    </select>
</div>
