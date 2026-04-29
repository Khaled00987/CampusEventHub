<?php
/**
 * database.php — PDO database connection singleton.
 *
 * Uses prepared statements for all queries (enforced in Model layer).
 * Credentials come from .env via env() — never commit real passwords.
 */

declare(strict_types=1);

require_once __DIR__ . '/env.php';

/**
 * Return a shared PDO instance (created once per request).
 *
 * @return PDO
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = env('DB_HOST', '127.0.0.1');
    $port = env('DB_PORT', '3306');
    $name = env('DB_NAME', 'campus_eventhub');
    $user = env('DB_USER', 'root');
    $pass = env('DB_PASS', '');

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $host,
        $port,
        $name
    );

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false, // Real prepared statements for security
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        if (APP_DEBUG) {
            throw $e;
        }
        http_response_code(500);
        require APP_PATH . '/views/errors/500.php';
        exit;
    }

    return $pdo;
}
