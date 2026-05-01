<?php
/**
 * Helpers.php — Global utility functions used across views and controllers.
 *
 * url() uses WEB_ROOT for application routes (no hardcoded folder names).
 * asset() uses PUBLIC_URL for static files under /public/assets/.
 * event_image() returns safe event image URLs with automatic placeholders.
 */

declare(strict_types=1);

/**
 * Build an internal application URL (routes are relative to WEB_ROOT).
 *
 * @param string $path e.g. '/events' or '/admin/events'
 * @return string
 */
function url(string $path = ''): string
{
    $path = $path === '' ? '/' : $path;
    if (!str_starts_with($path, '/')) {
        $path = '/' . $path;
    }
    return WEB_ROOT . $path;
}

/**
 * Build URL for static assets under /public/assets/.
 *
 * Works in XAMPP subfolders (e.g. /Campus-EventHub/public/assets/...)
 * and on live hosting with or without /public in the web path.
 *
 * @param string $path e.g. 'css/style.css' or 'assets/images/logo.png'
 * @return string
 */
function asset(string $path): string
{
    $path = ltrim(str_replace('\\', '/', $path), '/');
    $base = rtrim(defined('PUBLIC_URL') ? PUBLIC_URL : BASE_URL, '/');

    // Encode filename only so names like "2.1-events_&_news-body-1.jpg" work in URLs
    if (str_contains($path, '/')) {
        $dir = dirname($path);
        $file = rawurlencode(basename($path));
        $path = ($dir === '.' ? $file : $dir . '/' . $file);
    } else {
        $path = rawurlencode($path);
    }

    if (str_starts_with($path, 'assets/')) {
        return $base . '/' . $path;
    }

    if (str_starts_with($path, 'images/')) {
        return $base . '/assets/' . $path;
    }

    return $base . '/assets/' . $path;
}

/**
 * Marketing / static page images in public/assets/images/ (user-provided files).
 *
 * @param string $key e.g. hero, campus_convocation, events_news
 * @return string URL via asset()
 */
function campus_static_image(string $key): string
{
    // Each key maps to one file — use each image at most once on the landing page.
    $files = [
        'hero' => 'EVENT-MANAGEMENT.jpg',
        'hero_right' => 'hero-right.jpg',
        'event_management' => 'EVENT-MANAGEMENT.jpg',
        'convocation' => 'convocation-pl.jpg',
        'reunion' => 'reunion-weekend.jpg',
        'residence_halls' => '1.3.2.1.1-residence_halls-body-3-0723.jpg',
        'organizer_team' => 'what-is-an-event-organiser-team-943x630.webp',
        'organizer_jakarta' => 'event-organizer-in-jakarta.jpg',
        'events_news' => '2.1-events_&_news-body-1.jpg',
        'campus_union' => '220821-maucker-union-live027-resized.jpg',
        'campus_55149' => '55149-.jpg',
        'campus_71942' => '71942.jpg',
        'campus_71939' => '71939.jpg',
        'campus_67315' => '67315.jpg',
    ];

    $filename = $files[$key] ?? $files['hero'];
    $path = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $filename;

    if (!is_readable($path)) {
        return asset('assets/images/placeholder.jpg');
    }

    return asset('assets/images/' . $filename);
}

/**
 * Escape HTML for safe output in views (XSS prevention).
 *
 * @param mixed $value
 * @return string
 */
function e(mixed $value): string
{
    if ($value === null) {
        return '';
    }
    if (is_bool($value)) {
        $value = $value ? '1' : '0';
    } elseif (is_array($value) || is_object($value)) {
        return '';
    } else {
        $value = (string) $value;
    }

    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Redirect to an internal URL and stop execution.
 *
 * @param string $path
 */
function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

/**
 * Check if current request path matches (for nav active states).
 *
 * @param string $path
 * @return bool
 */
function is_active_path(string $path): bool
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $base = defined('WEB_ROOT') ? WEB_ROOT : BASE_URL;
    if ($base !== '' && str_starts_with($uri, $base)) {
        $uri = substr($uri, strlen($base)) ?: '/';
    }
    if (str_starts_with($uri, '/public')) {
        $uri = substr($uri, 7) ?: '/';
    }
    if (!str_starts_with($uri, '/')) {
        $uri = '/' . $uri;
    }
    return $path === '/' ? ($uri === '/' || $uri === '') : str_starts_with($uri, $path);
}

/**
 * Create URL-friendly slug from title.
 *
 * @param string $title
 * @return string
 */
function slugify(string $title): string
{
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
    return trim($slug, '-') ?: 'event';
}

/**
 * Format datetime for display.
 *
 * @param string|null $datetime
 * @return string
 */
function format_datetime(?string $datetime): string
{
    if (!$datetime) {
        return '—';
    }
    $ts = strtotime($datetime);
    return $ts ? date('d M Y H:i', $ts) : e($datetime);
}

/**
 * Format date only.
 *
 * @param string|null $date
 * @return string
 */
function format_date(?string $date): string
{
    if (!$date) {
        return '—';
    }
    $ts = strtotime($date);
    return $ts ? date('d M Y', $ts) : e($date);
}

/**
 * Get client IP for activity logs (best effort).
 *
 * @return string
 */
function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Render a view file with optional data array extracted as variables.
 *
 * @param string $view Path relative to app/views/ without .php
 * @param array<string, mixed> $data
 */
function view(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $file = APP_PATH . '/views/' . str_replace('.', '/', $view) . '.php';
    if (!is_readable($file)) {
        http_response_code(500);
        if (APP_DEBUG) {
            echo 'View not found: ' . e($view);
        } else {
            require APP_PATH . '/views/errors/500.php';
        }
        exit;
    }
    require $file;
}

/**
 * Return old form input after validation failure.
 *
 * @param string $key
 * @param string $default
 * @return string
 */
function old(string $key, string $default = ''): string
{
    $old = $_SESSION['_old_input'][$key] ?? $default;
    return is_string($old) ? $old : $default;
}

/**
 * Store old input in session before redirect back.
 *
 * @param array<string, mixed> $input
 */
function flash_old_input(array $input): void
{
    $_SESSION['_old_input'] = $input;
}

/**
 * Clear old input after successful form submit.
 */
function clear_old_input(): void
{
    unset($_SESSION['_old_input']);
}

/**
 * Event image URL with filesystem check and safe fallbacks.
 *
 * Stored filenames live in public/assets/images/events/ only.
 * Never pass a full path from the database — basename() is applied for safety.
 *
 * @param string|null $filename Database filename only
 * @return string Browser-ready URL via asset()
 */
function event_image(?string $filename): string
{
    $eventsDir = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'events';
    $placeholderEvent = $eventsDir . DIRECTORY_SEPARATOR . 'placeholder-event.jpg';
    $placeholderGlobal = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'placeholder.jpg';

    if ($filename !== null && $filename !== '') {
        $safeName = basename(str_replace('\\', '/', $filename));
        $fullPath = $eventsDir . DIRECTORY_SEPARATOR . $safeName;
        if (is_readable($fullPath)) {
            return asset('assets/images/events/' . $safeName);
        }
    }

    if (is_readable($placeholderEvent)) {
        return asset('assets/images/events/placeholder-event.jpg');
    }

    return asset('assets/images/placeholder.jpg');
}

/**
 * Backward-compatible alias for event_image().
 *
 * @param string|null $filename
 * @return string
 */
function event_image_url(?string $filename): string
{
    return event_image($filename);
}

/**
 * Logo URL used on every page (navbar, dashboard, footer, PDF).
 *
 * @return string Always returns a valid asset URL (logo or global placeholder).
 */
function logo_url(): string
{
    $path = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'logo.png';

    if (is_readable($path)) {
        return asset('assets/images/logo.png');
    }

    return asset('assets/images/placeholder.jpg');
}

/**
 * Filesystem path to logo.png for PDF embedding (FPDF-compatible).
 */
function logo_path_for_pdf(): ?string
{
    $path = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'logo.png';

    if (!is_readable($path)) {
        return null;
    }

    return pdf_compatible_image_path($path);
}

/**
 * Absolute filesystem path for an image used by FPDF (not a browser URL).
 *
 * @param string|null $filename Event image filename from database
 * @return string|null Path to JPG/PNG file FPDF can embed, or null to skip image
 */
function event_image_path_for_pdf(?string $filename): ?string
{
    $eventsDir = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'events';
    $candidates = [];

    if ($filename !== null && $filename !== '') {
        $candidates[] = $eventsDir . DIRECTORY_SEPARATOR . basename(str_replace('\\', '/', $filename));
    }

    $candidates[] = $eventsDir . DIRECTORY_SEPARATOR . 'placeholder-event.jpg';
    $candidates[] = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'placeholder.jpg';

    foreach ($candidates as $path) {
        if (!is_readable($path)) {
            continue;
        }
        $normalized = pdf_compatible_image_path($path);
        if ($normalized !== null) {
            return $normalized;
        }
    }

    return null;
}

/**
 * Convert WEBP to temporary JPEG for FPDF when GD is available.
 *
 * @param string $path
 * @return string|null
 */
function pdf_compatible_image_path(string $path): ?string
{
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    if (in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
        return $path;
    }

    if ($ext === 'webp' && function_exists('imagecreatefromwebp') && function_exists('imagejpeg')) {
        $im = @imagecreatefromwebp($path);
        if ($im === false) {
            return null;
        }
        $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ceh_pdf_' . uniqid('', true) . '.jpg';
        if (@imagejpeg($im, $tmp, 90)) {
            imagedestroy($im);
            return $tmp;
        }
        imagedestroy($im);
    }

    return null;
}

/**
 * Build ticket code shown on PDF and download filename.
 *
 * @param int $ticketId
 * @param int $eventId
 * @param int $userId
 * @return string e.g. CEH-12-3-2
 */
function ticket_code(int $ticketId, int $eventId, int $userId): string
{
    return sprintf('CEH-%d-%d-%d', $ticketId, $eventId, $userId);
}

/**
 * Numeric gate / entry code for PDF tickets (stable per request).
 */
function entry_gate_code(int $ticketId, int $eventId, int $userId): string
{
    $seed = sprintf('%d:%d:%d', $ticketId, $eventId, $userId);
    $n = abs(crc32($seed)) % 100000000;

    return str_pad((string) $n, 8, '0', STR_PAD_LEFT);
}
