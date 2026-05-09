<?php
/**
 * layouts/dashboard_layout.php — Premium dashboard app shell (sidebar + topbar + content).
 */
$pageTitle = $pageTitle ?? 'Dashboard';
$pageSubtitle = $pageSubtitle ?? '';
$pageActionUrl = $pageActionUrl ?? '';
$pageActionLabel = $pageActionLabel ?? '';
$pageActionsHtml = $pageActionsHtml ?? '';
$isAdmin = Auth::isAdmin();
$user = Auth::user() ?? [];
$userName = (string) ($user['name'] ?? 'User');
$userRole = $isAdmin ? 'Admin' : 'User';

$nameParts = preg_split('/\s+/', trim($userName), -1, PREG_SPLIT_NO_EMPTY) ?: [];
$initials = '';
if (count($nameParts) >= 2) {
    $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
} elseif ($nameParts !== []) {
    $initials = strtoupper(substr($nameParts[0], 0, 2));
} else {
    $initials = 'U';
}

$dashNavActive = static function (string $path) use ($isAdmin): string {
    if ($path === '/' && is_active_path('/')) {
        return ' is-active';
    }
    if ($path !== '/' && is_active_path($path)) {
        return ' is-active';
    }
    return '';
};

$dashNavCurrent = static function (string $path) use ($dashNavActive): string {
    return $dashNavActive($path) !== '' ? ' aria-current="page"' : '';
};

if ($isAdmin) {
    $navGroups = [
        [
            'label' => 'Main',
            'items' => [
                ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => 'grid'],
                ['label' => 'Public Site', 'url' => '/', 'icon' => 'globe'],
            ],
        ],
        [
            'label' => 'Management',
            'items' => [
                ['label' => 'Events', 'url' => '/admin/events', 'icon' => 'calendar'],
                ['label' => 'Ticket Requests', 'url' => '/admin/tickets', 'icon' => 'ticket'],
                ['label' => 'Announcements', 'url' => '/admin/announcements', 'icon' => 'megaphone'],
            ],
        ],
        [
            'label' => 'AI & Monitoring',
            'items' => [
                ['label' => 'AI Draft Assistant', 'url' => '/admin/ai-draft', 'icon' => 'spark'],
                ['label' => 'AI Logs', 'url' => '/admin/ai-logs', 'icon' => 'list'],
                ['label' => 'Activity Logs', 'url' => '/admin/activity-logs', 'icon' => 'activity'],
            ],
        ],
        [
            'label' => 'Account',
            'items' => [
                ['label' => 'Logout', 'url' => '/logout', 'icon' => 'logout'],
            ],
        ],
    ];
} else {
    $navGroups = [
        [
            'label' => 'Main',
            'items' => [
                ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => 'grid'],
                ['label' => 'Browse Events', 'url' => '/events', 'icon' => 'calendar'],
                ['label' => 'My Tickets', 'url' => '/my-tickets', 'icon' => 'ticket'],
                ['label' => 'Announcements', 'url' => '/announcements', 'icon' => 'megaphone'],
                ['label' => 'Help Assistant', 'url' => '/help-assistant', 'icon' => 'help'],
                ['label' => 'Public Site', 'url' => '/', 'icon' => 'globe'],
                ['label' => 'Logout', 'url' => '/logout', 'icon' => 'logout'],
            ],
        ],
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>">
</head>
<body class="dashboard-body">
<a class="skip-link" href="#dash-content">Skip to content</a>

<div class="dash-shell">
    <aside id="dash-sidebar" class="dash-sidebar" aria-label="Dashboard navigation">
        <div class="dash-sidebar__head">
            <?php $logoHref = url('/dashboard'); $logoClass = 'dash-brand'; require APP_PATH . '/views/partials/brand-logo.php'; ?>
        </div>

        <div class="dash-user-card">
            <span class="dash-user-card__avatar" aria-hidden="true"><?= e($initials) ?></span>
            <div class="dash-user-card__info">
                <span class="dash-user-card__name"><?= e($userName) ?></span>
                <span class="dash-badge dash-badge-role"><?= e($userRole) ?></span>
            </div>
        </div>

        <nav class="dash-nav">
            <?php foreach ($navGroups as $group): ?>
                <div class="dash-nav-group">
                    <span class="dash-nav-group__label"><?= e($group['label']) ?></span>
                    <ul>
                        <?php foreach ($group['items'] as $item): ?>
                            <?php
                            $activeClass = $dashNavActive($item['url']);
                            $isLogout = ($item['url'] ?? '') === '/logout';
                            ?>
                            <li>
                                <a href="<?= url($item['url']) ?>"
                                   class="dash-nav-item<?= e($activeClass) ?><?= $isLogout ? ' dash-nav-item--logout' : '' ?>"
                                   <?= $dashNavCurrent($item['url']) ?>>
                                    <span class="dash-nav-item__icon" aria-hidden="true"><?php $dashIcon = $item['icon'] ?? 'dot'; require APP_PATH . '/views/partials/dash-icon.php'; ?></span>
                                    <span class="dash-nav-item__text"><?= e($item['label']) ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div class="dash-overlay" id="dash-overlay" hidden aria-hidden="true"></div>

    <div class="dash-main">
        <header class="dash-topbar">
            <button type="button" class="dash-menu-btn" id="dash-menu-btn" aria-expanded="false" aria-controls="dash-sidebar" aria-label="Open dashboard menu">
                <span class="dash-menu-btn__bar" aria-hidden="true"></span>
                <span class="dash-menu-btn__bar" aria-hidden="true"></span>
                <span class="dash-menu-btn__bar" aria-hidden="true"></span>
            </button>
            <div class="dash-topbar__titles">
                <h1 class="dash-topbar__title"><?= e($pageTitle) ?></h1>
                <?php if ($pageSubtitle !== ''): ?>
                    <p class="dash-topbar__subtitle"><?= e($pageSubtitle) ?></p>
                <?php endif; ?>
            </div>
            <div class="dash-topbar__actions">
                <span class="dash-badge dash-badge-role dash-topbar__role"><?= e($userRole) ?></span>
                <?php if ($pageActionsHtml !== ''): ?>
                    <?= $pageActionsHtml ?>
                <?php elseif ($pageActionUrl !== '' && $pageActionLabel !== ''): ?>
                    <a class="btn btn-primary btn-sm" href="<?= url($pageActionUrl) ?>"><?= e($pageActionLabel) ?></a>
                <?php endif; ?>
            </div>
        </header>

        <main id="dash-content" class="dash-content">
            <?php require APP_PATH . '/views/partials/flash.php'; ?>
            <?php
            if (!empty($_inner_view)) {
                require APP_PATH . '/views/' . str_replace('.', '/', $_inner_view) . '.php';
            }
            ?>
        </main>
    </div>
</div>

<script src="<?= asset('js/main.js') ?>"></script>
<script src="<?= asset('js/validation.js') ?>"></script>
<?php if (str_contains($_inner_view ?? '', 'ai') || str_contains($_inner_view ?? '', 'help')): ?>
<script src="<?= asset('js/ai.js') ?>"></script>
<?php endif; ?>
</body>
</html>
