<?php
/**
 * layouts/header.php — Sticky navbar, semantic structure, wide layout option for landing.
 */
$pageTitle = $pageTitle ?? APP_NAME;
$layoutWide = $layoutWide ?? false;
$loadLandingCss = $loadLandingCss ?? false;
$bodyClass = trim(($bodyClass ?? '') . ($layoutWide ? ' page-wide' : ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e(APP_NAME) ?> — campus events, tickets, and announcements">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
    <?php if ($loadLandingCss): ?>
    <link rel="stylesheet" href="<?= asset('css/landing.css') ?>">
    <?php endif; ?>
</head>
<body class="<?= e($bodyClass) ?>">
<a class="skip-link" href="#main-content">Skip to main content</a>

<header class="site-header" id="site-header">
    <div class="container header-inner">
        <?php $logoClass = 'brand--header'; $logoAlt = 'Home'; require APP_PATH . '/views/partials/brand-logo.php'; ?>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="main-nav" aria-label="Open menu">
            <span class="nav-toggle-bar"></span>
            <span class="nav-toggle-bar"></span>
            <span class="nav-toggle-bar"></span>
        </button>
        <nav id="main-nav" class="main-nav" aria-label="Main navigation">
            <ul class="main-nav-list">
                <li><a href="<?= url('/') ?>" class="<?= is_active_path('/') && !is_active_path('/events') && !is_active_path('/about') ? 'active' : '' ?>">Home</a></li>
                <li><a href="<?= url('/events') ?>" class="<?= is_active_path('/events') ? 'active' : '' ?>">Events</a></li>
                <li><a href="<?= url('/announcements') ?>" class="<?= is_active_path('/announcements') ? 'active' : '' ?>">Announcements</a></li>
                <li><a href="<?= url('/about') ?>" class="<?= is_active_path('/about') ? 'active' : '' ?>">About</a></li>
                <li><a href="<?= url('/help-assistant') ?>" class="<?= is_active_path('/help-assistant') ? 'active' : '' ?>">Help</a></li>
                <?php if (Auth::check()): ?>
                    <li><a href="<?= url('/dashboard') ?>" class="<?= is_active_path('/dashboard') ? 'active' : '' ?>">Dashboard</a></li>
                    <?php if (!Auth::isAdmin()): ?>
                        <li><a href="<?= url('/my-tickets') ?>" class="<?= is_active_path('/my-tickets') ? 'active' : '' ?>">My Tickets</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <div class="main-nav-actions">
                <?php if (Auth::check()): ?>
                    <a href="<?= url('/logout') ?>" class="main-nav-link-muted">Logout</a>
                <?php else: ?>
                    <a href="<?= url('/login') ?>" class="main-nav-login">Login</a>
                    <a class="btn btn-primary btn-nav-register" href="<?= url('/register') ?>">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>

<main id="main-content" class="site-main<?= $layoutWide ? ' site-main--wide' : '' ?>">
<?php if (!$layoutWide): ?><div class="container page-container"><?php endif; ?>
<?php require APP_PATH . '/views/partials/flash.php'; ?>
