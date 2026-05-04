<?php
/**
 * index.php — Project root entry (XAMPP fallback when mod_rewrite is off).
 *
 * Forwards all requests to public/index.php so visiting
 * http://localhost/YourFolder/ runs the app instead of a directory listing.
 */
declare(strict_types=1);

require __DIR__ . '/public/index.php';
