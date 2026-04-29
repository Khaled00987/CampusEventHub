<?php
/**
 * env.php — Loads environment variables from the project root .env file.
 *
 * This file is included early in the bootstrap process. It reads key=value
 * pairs safely and exposes them via env() helper. If .env is missing, sensible
 * defaults are used so the app still runs on XAMPP without extra setup.
 */

declare(strict_types=1);

/** @var array<string, string> Cached environment values */
$GLOBALS['_env_cache'] = [];

/**
 * Parse the .env file from the project root (one level above /public).
 *
 * @return array<string, string>
 */
function loadEnvFile(): array
{
    $root = dirname(__DIR__, 2);
    $envPath = $root . DIRECTORY_SEPARATOR . '.env';

    $defaults = [
        'APP_NAME' => 'Campus EventHub',
        'APP_ENV' => 'local',
        'APP_DEBUG' => 'true',
        'APP_URL' => 'http://localhost',
        'DB_HOST' => '127.0.0.1',
        'DB_PORT' => '3306',
        'DB_NAME' => 'campus_eventhub',
        'DB_USER' => 'root',
        'DB_PASS' => '',
        'SESSION_LIFETIME' => '120',
        'LOGIN_MAX_ATTEMPTS' => '5',
        'LOGIN_LOCK_MINUTES' => '10',
        'LONGCAT_API_KEY' => '',
        'LONGCAT_API_URL' => '',
        'LONGCAT_MODEL' => 'longcat-chat',
        'AI_PROVIDER' => 'longcat',
        'AI_FALLBACK' => 'true',
    ];

    if (!is_readable($envPath)) {
        return $defaults;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return $defaults;
    }

    $parsed = $defaults;

    foreach ($lines as $line) {
        $line = trim($line);
        // Skip comments and invalid lines
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        // Strip optional surrounding quotes
        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }
        $parsed[$key] = $value;
    }

    return $parsed;
}

/**
 * Get an environment variable by key with optional default.
 *
 * @param string $key
 * @param string|null $default
 * @return string
 */
function env(string $key, ?string $default = null): string
{
    if ($GLOBALS['_env_cache'] === []) {
        $GLOBALS['_env_cache'] = loadEnvFile();
    }
    return $GLOBALS['_env_cache'][$key] ?? ($default ?? '');
}

// Load immediately when this file is required
$GLOBALS['_env_cache'] = loadEnvFile();
