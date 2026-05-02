<?php
/**
 * Auth.php — Authentication and role-based access control.
 *
 * Stores logged-in user in session. Provides requireLogin() and requireAdmin()
 * guards for controllers. Login rate limiting uses session counters per spec.
 */

declare(strict_types=1);

class Auth
{
    private const SESSION_USER = 'auth_user';
    private const LOGIN_ATTEMPTS_KEY = 'login_attempts';
    private const LOGIN_LOCK_UNTIL_KEY = 'login_locked_until';

    /**
     * @return array<string, mixed>|null Current user row from session
     */
    public static function user(): ?array
    {
        $user = Session::get(self::SESSION_USER);
        return is_array($user) ? $user : null;
    }

    /**
     * @return bool
     */
    public static function check(): bool
    {
        return self::user() !== null;
    }

    /**
     * @return bool
     */
    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user && ($user['role'] ?? '') === 'admin';
    }

    /**
     * Log in user: regenerate session ID and store safe user fields only.
     *
     * @param array<string, mixed> $user Database row (password must not be kept)
     */
    public static function login(array $user): void
    {
        Session::regenerate();
        unset($user['password']);
        Session::set(self::SESSION_USER, $user);
    }

    /**
     * Log out and destroy session.
     */
    public static function logout(): void
    {
        Session::destroy();
        Session::start();
    }

    /**
     * Redirect guests to login.
     */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            Session::flash('error', 'Please log in to continue.');
            redirect('/login');
        }
    }

    /**
     * Redirect non-admins to 403 page.
     */
    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            view('errors/403');
            exit;
        }
    }

    /**
     * Redirect authenticated users away from login/register.
     */
    public static function redirectIfAuthenticated(): void
    {
        if (self::check()) {
            redirect('/dashboard');
        }
    }

    /**
     * Check if login is temporarily locked due to failed attempts.
     *
     * @return bool
     */
    public static function isLoginLocked(): bool
    {
        $until = (int) Session::get(self::LOGIN_LOCK_UNTIL_KEY, 0);
        return $until > time();
    }

    /**
     * Minutes remaining on lockout.
     *
     * @return int
     */
    public static function loginLockMinutesRemaining(): int
    {
        $until = (int) Session::get(self::LOGIN_LOCK_UNTIL_KEY, 0);
        $diff = $until - time();
        return $diff > 0 ? (int) ceil($diff / 60) : 0;
    }

    /**
     * Record a failed login attempt; lock if threshold exceeded.
     */
    public static function recordFailedLogin(): void
    {
        $attempts = (int) Session::get(self::LOGIN_ATTEMPTS_KEY, 0) + 1;
        Session::set(self::LOGIN_ATTEMPTS_KEY, $attempts);

        if ($attempts >= LOGIN_MAX_ATTEMPTS) {
            Session::set(
                self::LOGIN_LOCK_UNTIL_KEY,
                time() + (LOGIN_LOCK_MINUTES * 60)
            );
            Session::set(self::LOGIN_ATTEMPTS_KEY, 0);
        }
    }

    /**
     * Clear login attempt counter after successful login.
     */
    public static function clearLoginAttempts(): void
    {
        Session::remove(self::LOGIN_ATTEMPTS_KEY);
        Session::remove(self::LOGIN_LOCK_UNTIL_KEY);
    }
}
