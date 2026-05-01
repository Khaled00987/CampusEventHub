<?php
/**
 * CSRF.php — Cross-Site Request Forgery protection.
 *
 * Every POST form must include a token from csrf_field(). On submit,
 * validateCsrf() compares the submitted token with the session value using
 * hash_equals() to prevent timing attacks.
 */

declare(strict_types=1);

class CSRF
{
    private const TOKEN_KEY = '_csrf_token';

    /**
     * Generate or return existing CSRF token for this session.
     *
     * @return string
     */
    public static function token(): string
    {
        if (empty($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::TOKEN_KEY];
    }

    /**
     * Validate submitted token from $_POST.
     *
     * @return bool
     */
    public static function validate(): bool
    {
        $submitted = $_POST['_csrf_token'] ?? '';
        $stored = $_SESSION[self::TOKEN_KEY] ?? '';
        if ($submitted === '' || $stored === '') {
            return false;
        }
        return hash_equals($stored, $submitted);
    }

    /**
     * HTML hidden input for forms.
     *
     * @return string
     */
    public static function field(): string
    {
        $token = self::token();
        return '<input type="hidden" name="_csrf_token" value="' . e($token) . '">';
    }
}
