<?php
/**
 * Session.php — Secure session wrapper.
 *
 * Starts sessions with httponly cookies, tracks last activity for timeout,
 * and stores flash messages for one-time display after redirects.
 */

declare(strict_types=1);

class Session
{
    /**
     * Start the session if not already active.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Lax');

        session_start();

        // Initialise last activity timestamp for idle timeout
        if (!isset($_SESSION['_last_activity'])) {
            $_SESSION['_last_activity'] = time();
        }
    }

    /**
     * Check idle timeout; destroy session if expired.
     *
     * @return bool True if session is still valid
     */
    public static function checkTimeout(): bool
    {
        $lifetimeMinutes = SESSION_LIFETIME;
        $maxIdle = $lifetimeMinutes * 60;

        if (isset($_SESSION['_last_activity']) && (time() - $_SESSION['_last_activity']) > $maxIdle) {
            self::destroy();
            self::start(); // Fresh session so flash message can be stored
            return false;
        }

        $_SESSION['_last_activity'] = time();
        return true;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $key
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Store a one-time flash message (success, error, info).
     *
     * @param string $type
     * @param string $message
     */
    public static function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][$type][] = $message;
    }

    /**
     * Retrieve and clear all flash messages.
     *
     * @return array<string, array<int, string>>
     */
    public static function getFlashes(): array
    {
        $flashes = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flashes;
    }

    /**
     * Destroy session completely (used on logout).
     */
    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    /**
     * Regenerate session ID after login to prevent session fixation.
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }
}
