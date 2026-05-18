<?php
/**
 * longcat.php — AI provider configuration (Longcat optional).
 *
 * Reads API settings from .env. When LONGCAT_API_KEY or LONGCAT_API_URL is empty,
 * LongcatService uses local fallback responses so XAMPP works without any API key.
 */

declare(strict_types=1);

require_once __DIR__ . '/env.php';

/** Provider name shown in logs and documentation */
define('AI_PROVIDER', env('AI_PROVIDER', 'longcat'));

/** When true, use safe local templates if the API is unavailable */
define('AI_FALLBACK', strtolower(env('AI_FALLBACK', 'true')) === 'true');

/** Longcat HTTP endpoint (empty = offline mode) */
define('LONGCAT_API_URL', env('LONGCAT_API_URL', ''));

/** API secret — never echo in HTML */
define('LONGCAT_API_KEY', env('LONGCAT_API_KEY', ''));

/** Model identifier sent in JSON payload */
define('LONGCAT_MODEL', env('LONGCAT_MODEL', 'longcat-chat'));

/**
 * Exact disclaimer required on every AI output (assessment specification).
 * Shown in views and appended to JSON responses.
 */
define(
    'AI_DISCLAIMER',
    'AI-generated content requires human review before saving or publishing.'
);

/** cURL timeout in seconds for Longcat requests */
define('LONGCAT_TIMEOUT', 25);
