<?php
/**
 * errors/_bootstrap.php — Minimal bootstrap for standalone error pages (403/404/500).
 */
if (!defined('APP_NAME')) {
    require_once dirname(__DIR__, 2) . '/config/config.php';
    require_once APP_PATH . '/core/Session.php';
    require_once APP_PATH . '/core/CSRF.php';
    require_once APP_PATH . '/core/Helpers.php';
    require_once APP_PATH . '/core/Auth.php';
    Session::start();
}
