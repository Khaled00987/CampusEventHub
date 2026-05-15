<?php /** admin/announcements/index.php */ ?>
<div class="dash-card dash-filter-card">
    <form method="get" class="filter-bar">
        <input type="search" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Search title" aria-label="Search">
        <select name="status" aria-label="Status">
            <option value="">All</option>
            <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
            <option value="published" <?= ($filters['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
        </select>
        <button type="submit" class="btn btn-primary">Apply</button>
        <a class="btn btn-ghost" href="<?= url('/admin/announcements') ?>">Reset</a>
    </form>
</div>

<?php if (empty($announcements['items'])): ?>
    <div class="dash-card dash-empty">
        <p>No announcements yet.</p>
        <a class="btn btn-primary" href="<?= url('/admin/announcements/create') ?>">Create one</a>
    </div>
<?php else: ?>
    <div class="dash-card dash-table-card">
        <div class="dash-table-wrap">
            <table class="dash-table dash-table--responsive">
                <thead>
                    <tr><th>Title</th><th>Status</th><th>Updated</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements['items'] as $a): ?>
                        <?php
                        $st = (string) ($a['status'] ?? 'draft');
                        $badgeClass = $st === 'published' ? 'success' : 'warning';
                        ?>
                        <tr>
                            <td data-label="Title"><strong><?= e($a['title']) ?></strong></td>
                            <td data-label="Status"><span class="dash-badge dash-badge-<?= e($badgeClass) ?>"><?= e($st) ?></span></td>
                            <td data-label="Updated"><?= e(format_datetime($a['updated_at'])) ?></td>
                            <td class="actions" data-label="Actions">
                                <a class="btn btn-sm btn-primary" href="<?= url('/admin/announcements/' . $a['id'] . '/edit') ?>">Edit</a>
                                <form method="post" action="<?= url('/admin/announcements/' . $a['id'] . '/delete') ?>" class="inline-form" data-confirm="Delete this announcement?">
                                    <?= CSRF::field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php $pagination = $announcements; $baseUrl = '/admin/announcements'; require APP_PATH . '/views/partials/pagination.php'; ?>
    </div>
<?php endif; ?>
