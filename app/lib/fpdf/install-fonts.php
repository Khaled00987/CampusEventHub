<?php
/**
 * One-time installer for FPDF core font metrics (Helvetica family).
 * Run: php app/lib/fpdf/install-fonts.php
 */
declare(strict_types=1);

$fontDir = __DIR__ . DIRECTORY_SEPARATOR . 'font' . DIRECTORY_SEPARATOR;
if (!is_dir($fontDir)) {
    mkdir($fontDir, 0775, true);
}

$files = ['helvetica', 'helveticab', 'helveticai', 'helveticabi'];
$base = 'https://raw.githubusercontent.com/Setasign/FPDF/master/font/';

foreach ($files as $name) {
    $url = $base . $name . '.php';
    $dest = $fontDir . $name . '.php';
    if (is_readable($dest) && filesize($dest) > 500) {
        echo "SKIP {$name}.php\n";
        continue;
    }
    $data = @file_get_contents($url);
    if ($data === false || strlen($data) < 500) {
        fwrite(STDERR, "Failed to download {$url}\n");
        exit(1);
    }
    file_put_contents($dest, $data);
    echo "OK {$name}.php (" . strlen($data) . " bytes)\n";
}

echo "FPDF fonts installed.\n";
