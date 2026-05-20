<?php
/**
 * FpdfFonts.php — Ensures FPDF core Helvetica font metrics exist locally.
 */
declare(strict_types=1);

final class FpdfFonts
{
    private const FONT_NAMES = ['helvetica', 'helveticab', 'helveticai', 'helveticabi'];

    private const REMOTE_BASE = 'https://raw.githubusercontent.com/Setasign/FPDF/master/font/';

    public static function ensureInstalled(): void
    {
        $dir = APP_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'fpdf' . DIRECTORY_SEPARATOR . 'font' . DIRECTORY_SEPARATOR;

        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Cannot create FPDF font directory.');
        }

        $missing = [];
        foreach (self::FONT_NAMES as $name) {
            $file = $dir . $name . '.php';
            if (!is_readable($file) || filesize($file) < 500) {
                $missing[] = $name;
            }
        }

        if ($missing === []) {
            return;
        }

        $ctx = stream_context_create([
            'http' => ['timeout' => 20, 'user_agent' => 'Campus-EventHub/1.0'],
            'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
        ]);

        foreach ($missing as $name) {
            $url = self::REMOTE_BASE . $name . '.php';
            $data = @file_get_contents($url, false, $ctx);
            if ($data === false || strlen($data) < 500) {
                throw new RuntimeException(
                    'FPDF fonts missing. Run from project root: php app/lib/fpdf/install-fonts.php'
                );
            }
            file_put_contents($dir . $name . '.php', $data);
        }
    }
}
