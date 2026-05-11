<?php
/**
 * partials/pagination.php — Reusable pagination controls.
 *
 * Expects: $pagination array with page, total, per_page, and $baseUrl (path without query).
 */
if (empty($pagination) || ($pagination['total'] ?? 0) <= ($pagination['per_page'] ?? 10)) {
    return;
}
$page = (int) ($pagination['page'] ?? 1);
$perPage = (int) ($pagination['per_page'] ?? PER_PAGE);
$total = (int) ($pagination['total'] ?? 0);
$totalPages = (int) ceil($total / $perPage);
$baseUrl = $baseUrl ?? '/';
$query = $_GET;
unset($query['page']);
$qs = http_build_query($query);
$prefix = url($baseUrl) . ($qs ? '?' . $qs . '&' : '?');
?>
<nav class="pagination" aria-label="Pagination">
    <ul>
        <?php if ($page > 1): ?>
            <li><a href="<?= e($prefix . 'page=' . ($page - 1)) ?>">Previous</a></li>
        <?php endif; ?>
        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <li>
                <?php if ($i === $page): ?>
                    <span class="current" aria-current="page"><?= e((string) $i) ?></span>
                <?php else: ?>
                    <a href="<?= e($prefix . 'page=' . $i) ?>"><?= e((string) $i) ?></a>
                <?php endif; ?>
            </li>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
            <li><a href="<?= e($prefix . 'page=' . ($page + 1)) ?>">Next</a></li>
        <?php endif; ?>
    </ul>
</nav>
