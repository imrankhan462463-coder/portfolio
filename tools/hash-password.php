<?php
/**
 * CLI helper: generate an admin password hash.
 *
 * Usage: php tools/hash-password.php "your-strong-password"
 *
 * @package ImranPortfolio
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This tool can only be run from the command line.');
}

$password = $argv[1] ?? '';

if (strlen($password) < 12) {
    fwrite(STDERR, "Please choose a password with at least 12 characters.\n");
    exit(1);
}

echo password_hash($password, PASSWORD_DEFAULT) . PHP_EOL;
