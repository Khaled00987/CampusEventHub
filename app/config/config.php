<?php
/**
 * config.php — Application-wide constants and path definitions.
 *
 * PUBLIC_URL = web path to /public (for CSS, JS, images).
 * WEB_ROOT   = project URL base without /public (for routes like /events).
 * Works in XAMPP subfolders and when opening /public/ directly.
 */

declare(strict_types=1);

require_once __DIR__ . '/env.php';

define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'app');
define('PUBLIC_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'public');

define('APP_NAME', env('APP_NAME', 'Campus EventHub'));
define('APP_ENV', env('APP_ENV', 'local'));
define('APP_DEBUG', strtolower(env('APP_DEBUG', 'true')) === 'true');
define('SESSION_LIFETIME', (int) env('SESSION_LIFETIME', '120'));
define('LOGIN_MAX_ATTEMPTS', (int) env('LOGIN_MAX_ATTEMPTS', '5'));
define('LOGIN_LOCK_MINUTES', (int) env('LOGIN_LOCK_MINUTES', '10'));

/**
 * Compute PUBLIC_URL (assets) and WEB_ROOT (application routes) from SCRIPT_NAME.
 *
 * @return array{public: string, web: string}
 */
function computeUrlBases(): array
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $scriptName = str_replace('\\', '/', $scriptName);
    $dir = rtrim(dirname($scriptName), '/');

    if ($dir === '' || $dir === '.') {
        return ['public' => '', 'web' => ''];
    }

    // Entry via /Folder/index.php or /Folder/public/index.php
    $publicUrl = str_ends_with($dir, '/public') ? $dir : $dir . '/public';
    $webRoot = str_ends_with($dir, '/public') ? substr($dir, 0, -7) : $dir;

    return ['public' => $publicUrl, 'web' => $webRoot];
}

$bases = computeUrlBases();
define('PUBLIC_URL', $bases['public']);
define('WEB_ROOT', $bases['web']);
/** @deprecated Use PUBLIC_URL for assets, WEB_ROOT for routes */
define('BASE_URL', PUBLIC_URL);

define('PER_PAGE', 10);
