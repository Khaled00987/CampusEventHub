<?php /** admin/events/index.php — Event management */ ?>
<div class="dash-card dash-filter-card">
    <form method="get" class="filter-bar" role="search">
        <input type="search" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Search title or location" aria-label="Search">
        <select name="category" aria-label="Category">
            <option value="">All categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= e($cat) ?>" <?= ($filters['category'] ?? '') === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" aria-label="Status">
            <option value="">All statuses</option>
            <?php foreach (['draft', 'published', 'cancelled'] as $s): ?>
                <option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= e($s) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Apply</button>
        <a class="btn btn-ghost" href="<?= url('/admin/events') ?>">Reset</a>
    </form>
</div>

<?php if (empty($events['items'])): ?>
    <div class="dash-card dash-empty">
        <p>No events found.</p>
        <a class="btn btn-primary" href="<?= url('/admin/events/create') ?>">Create the first event</a>
    </div>
<?php else: ?>
    <div class="dash-card dash-table-card">
        <div class="dash-table-wrap">
            <table class="dash-table dash-table--responsive">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events['items'] as $e): ?>
                        <?php
                        $status = (string) ($e['status'] ?? 'draft');
                        $badgeClass = $status === 'published' ? 'success' : ($status === 'cancelled' ? 'danger' : 'warning');
                        ?>
                        <tr>
                            <td data-label="Image">
                                <img class="dash-table-thumb" src="<?= e(event_image($e['image'] ?? null)) ?>" alt="" width="56" height="40" loading="lazy">
                            </td>
                            <td data-label="Title"><strong><?= e($e['title']) ?></strong></td>
                            <td data-label="Category"><?= e($e['category']) ?></td>
                            <td data-label="Date"><?= e(format_date($e['event_date'])) ?></td>
                            <td data-label="Status"><span class="dash-badge dash-badge-<?= e($badgeClass) ?>"><?= e($status) ?></span></td>
                            <td class="actions" data-label="Actions">
                                <a class="btn btn-sm" href="<?= url('/admin/events/' . $e['id']) ?>">View</a>
                                <a class="btn btn-sm btn-primary" href="<?= url('/admin/events/' . $e['id'] . '/edit') ?>">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php $pagination = $events; $baseUrl = '/admin/events'; require APP_PATH . '/views/partials/pagination.php'; ?>
    </div>
<?php endif; ?>
