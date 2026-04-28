<?php
/**
 * migrate-local.php — Apply missing schema to an existing local database (safe, non-destructive).
 *
 * Run from project root:
 *   php database/migrate-local.php
 *
 * Or open in browser (local only):
 *   http://localhost/Campus-EventHub/database/migrate-local.php
 *
 * PDF tickets do NOT need new columns — ticket codes are generated in PHP.
 * This script adds any tables/columns your DB may be missing vs schema.sql.
 */

declare(strict_types=1);

$isCli = PHP_SAPI === 'cli';
if (!$isCli) {
    $remote = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!in_array($remote, ['127.0.0.1', '::1'], true)) {
        http_response_code(403);
        exit('Local access only.');
    }
    header('Content-Type: text/plain; charset=utf-8');
}

define('APP_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app');
require_once APP_PATH . '/config/env.php';
require_once APP_PATH . '/config/database.php';

/** @return bool */
function tableExists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.tables
         WHERE table_schema = DATABASE() AND table_name = ?'
    );
    $stmt->execute([$table]);

    return (int) $stmt->fetchColumn() > 0;
}

/** @return bool */
function columnExists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.columns
         WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?'
    );
    $stmt->execute([$table, $column]);

    return (int) $stmt->fetchColumn() > 0;
}

function run(PDO $pdo, string $sql, array &$log): void
{
    $pdo->exec($sql);
    $log[] = 'OK: ' . preg_replace('/\s+/', ' ', trim($sql));
}

$log = [];

try {
    $pdo = db();
    $log[] = 'Connected to database: ' . env('DB_NAME', 'campus_eventhub');

    if (!columnExists($pdo, 'events', 'image')) {
        run($pdo, "ALTER TABLE events ADD COLUMN image VARCHAR(120) NULL COMMENT 'Filename under public/assets/images/events/' AFTER capacity", $log);
    } else {
        $log[] = 'SKIP: events.image already exists';
    }

    if (!tableExists($pdo, 'ai_logs')) {
        run($pdo, "CREATE TABLE ai_logs (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            feature VARCHAR(50) NOT NULL,
            input_text TEXT NOT NULL,
            ai_suggestion TEXT NOT NULL,
            final_text TEXT NULL,
            accepted TINYINT(1) NOT NULL DEFAULT 0,
            disclaimer_shown TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ai_feature (feature),
            INDEX idx_ai_accepted (accepted),
            CONSTRAINT fk_ai_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $log);
    } else {
        $log[] = 'SKIP: ai_logs table exists';
    }

    if (!tableExists($pdo, 'faq_items')) {
        run($pdo, "CREATE TABLE faq_items (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            question VARCHAR(255) NOT NULL,
            answer TEXT NOT NULL,
            keywords VARCHAR(255) NOT NULL,
            status ENUM('active','inactive') NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_faq_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $log);
    } else {
        $log[] = 'SKIP: faq_items table exists';
    }

    if (!tableExists($pdo, 'activity_logs')) {
        run($pdo, "CREATE TABLE activity_logs (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NULL,
            action VARCHAR(80) NOT NULL,
            entity_type VARCHAR(50) NULL,
            entity_id INT UNSIGNED NULL,
            details TEXT NULL,
            ip_address VARCHAR(45) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_activity_action (action),
            INDEX idx_activity_created (created_at),
            CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $log);
    } else {
        $log[] = 'SKIP: activity_logs table exists';
    }

    $log[] = '';
    $log[] = 'Note: Ticket PDF download uses existing ticket_requests columns — no PDF-specific migration required.';
    $log[] = 'Migration finished successfully.';
} catch (Throwable $e) {
    $log[] = 'ERROR: ' . $e->getMessage();
    if ($isCli) {
        fwrite(STDERR, implode(PHP_EOL, $log) . PHP_EOL);
        exit(1);
    }
    http_response_code(500);
    echo implode("\n", $log);
    exit;
}

echo implode($isCli ? PHP_EOL : "\n", $log);
