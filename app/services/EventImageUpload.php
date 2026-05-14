<?php
/**
 * EventImageUpload.php — Secure event image upload handler.
 *
 * Saves files to public/assets/images/events/ with a unique generated name.
 * Validates extension, MIME type, and size before move_uploaded_file().
 */

declare(strict_types=1);

class EventImageUpload
{
    /** Maximum upload size: 2 megabytes */
    private const MAX_BYTES = 2_097_152;

    /** Allowed file extensions (lowercase) */
    private const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'webp'];

    /** Allowed MIME types from finfo */
    private const ALLOWED_MIME = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    /** Filenames we never delete when replacing an upload */
    private const PROTECTED_FILES = [
        'placeholder-event.jpg',
        'placeholder.jpg',
    ];

    private string $uploadDir;

    public function __construct()
    {
        $this->uploadDir = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'events';
    }

    /**
     * Process $_FILES['image'] upload.
     *
     * @param array<string, mixed>|null $file $_FILES entry
     * @param string|null $keepFilename Existing DB filename when no new file sent
     * @return array{filename?: string|null, error?: string}
     */
    public function process(?array $file, ?string $keepFilename = null): array
    {
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['filename' => $keepFilename];
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return ['error' => 'Image upload failed. Please try again.'];
        }

        if (!is_uploaded_file((string) ($file['tmp_name'] ?? ''))) {
            return ['error' => 'Invalid upload. Please select an image from your device.'];
        }

        $tmpName = (string) $file['tmp_name'];

        if (($file['size'] ?? 0) > self::MAX_BYTES) {
            return ['error' => 'Image is too large. Maximum size is 2MB.'];
        }

        // Extension check (do not trust original filename from browser)
        $ext = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXT, true)) {
            return ['error' => 'Invalid file type. Upload JPG, PNG, or WEBP only.'];
        }

        // MIME validation using file content (more reliable than extension alone)
        $mime = $this->detectMime($tmpName);
        if ($mime === null || !in_array($mime, self::ALLOWED_MIME, true)) {
            return ['error' => 'Invalid image file. Upload JPG, PNG, or WEBP only.'];
        }

        if (!$this->ensureUploadDirectory()) {
            return ['error' => 'Upload folder is not writable. Contact the administrator.'];
        }

        $newName = $this->generateFilename($ext);
        $destination = $this->uploadDir . DIRECTORY_SEPARATOR . $newName;

        if (!move_uploaded_file($tmpName, $destination)) {
            return ['error' => 'Could not save the uploaded image. Please try again.'];
        }

        return ['filename' => $newName];
    }

    /**
     * Remove old event image after successful replace (skip placeholders).
     *
     * @param string|null $filename
     */
    public function deleteOldFile(?string $filename): void
    {
        if ($filename === null || $filename === '') {
            return;
        }

        $safe = basename(str_replace('\\', '/', $filename));
        if (in_array($safe, self::PROTECTED_FILES, true)) {
            return;
        }

        $path = $this->uploadDir . DIRECTORY_SEPARATOR . $safe;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    /**
     * Create upload directory if missing (XAMPP first-run friendly).
     */
    private function ensureUploadDirectory(): bool
    {
        if (is_dir($this->uploadDir)) {
            return is_writable($this->uploadDir);
        }

        return @mkdir($this->uploadDir, 0755, true) && is_writable($this->uploadDir);
    }

    /**
     * Detect MIME type using finfo (preferred) or mime_content_type fallback.
     */
    private function detectMime(string $tmpPath): ?string
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo !== false) {
                $mime = finfo_file($finfo, $tmpPath);
                finfo_close($finfo);
                if (is_string($mime)) {
                    return $mime;
                }
            }
        }

        if (function_exists('mime_content_type')) {
            $mime = mime_content_type($tmpPath);
            return is_string($mime) ? $mime : null;
        }

        return null;
    }

    /**
     * Unique filename: event_YYYYMMDD_HHMMSS_random.ext
     */
    private function generateFilename(string $ext): string
    {
        $ext = $ext === 'jpeg' ? 'jpg' : $ext;
        $stamp = date('Ymd_His');
        $random = bin2hex(random_bytes(4));

        return 'event_' . $stamp . '_' . $random . '.' . $ext;
    }
}
