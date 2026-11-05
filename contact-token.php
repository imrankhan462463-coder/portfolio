<?php
/**
 * Issues a short-lived CSRF token for the contact form.
 *
 * @package ImranPortfolio
 */

declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(array(
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ));
    session_start();
}

$token = bin2hex(random_bytes(32));
$_SESSION['portfolio_contact_token'] = $token;
$_SESSION['portfolio_contact_token_time'] = time();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
header('X-Content-Type-Options: nosniff');

echo json_encode(array('token' => $token), JSON_UNESCAPED_SLASHES);
