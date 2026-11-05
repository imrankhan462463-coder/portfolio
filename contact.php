<?php
/**
 * Standalone contact endpoint.
 *
 * - Validates every field on the server.
 * - Uses a honeypot, same-origin check, CSRF token and rate limiting.
 * - Sends mail through SMTP configured with environment variables.
 * - Never exposes SMTP credentials to the browser.
 *
 * @package ImranPortfolio
 */

declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/smtp.php';

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

/**
 * Whether the request expects JSON.
 *
 * @return bool
 */
function portfolio_contact_is_ajax(): bool
{
    $requestedWith = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''));
    $accept = strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? ''));

    return $requestedWith === 'xmlhttprequest' || strpos($accept, 'application/json') !== false;
}

/**
 * Send a response in JSON or redirect back to the page.
 *
 * @param bool   $success Success.
 * @param string $message Human-readable message.
 * @param int    $status  HTTP status.
 * @param string $code    Short redirect code.
 * @return never
 */
function portfolio_contact_respond(bool $success, string $message, int $status = 200, string $code = ''): void
{
    if (portfolio_contact_is_ajax()) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: no-store');
        echo json_encode(
            array('success' => $success, 'message' => $message),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        exit;
    }

    $target = 'index.html';
    $referer = (string) ($_SERVER['HTTP_REFERER'] ?? '');

    if ($referer !== '') {
        $parts = parse_url($referer);
        $host = (string) ($_SERVER['HTTP_HOST'] ?? '');

        if (is_array($parts) && isset($parts['host']) && strcasecmp($parts['host'], (string) parse_url('http://' . $host, PHP_URL_HOST)) === 0) {
            $path = (string) ($parts['path'] ?? '/index.html');
            $target = $path !== '' ? $path : '/index.html';
        }
    }

    $query = $success ? 'sent=1' : 'error=' . rawurlencode($code !== '' ? $code : 'validation');
    $separator = strpos($target, '?') === false ? '?' : '&';
    header('Location: ' . $target . $separator . $query . '#contact', true, 303);
    exit;
}

/**
 * Strip control characters and normalise whitespace.
 *
 * @param string $value Raw value.
 * @return string
 */
function portfolio_contact_clean(string $value): string
{
    $value = str_replace(array("\r", "\0"), '', $value);
    $value = preg_replace('/[ \t]+/', ' ', $value) ?? $value;

    return trim($value);
}

/**
 * Verify the request originates from this site when the browser supplies a
 * source header.
 *
 * @return bool
 */
function portfolio_contact_same_origin(): bool
{
    $host = (string) ($_SERVER['HTTP_HOST'] ?? '');
    $host = strtolower((string) preg_replace('/:\d+$/', '', $host));
    $source = (string) ($_SERVER['HTTP_ORIGIN'] ?? '');

    if ($source === '') {
        $source = (string) ($_SERVER['HTTP_REFERER'] ?? '');
    }

    if ($source === '' || $host === '') {
        return true;
    }

    $sourceHost = strtolower((string) parse_url($source, PHP_URL_HOST));

    return $sourceHost === '' || $sourceHost === $host;
}

/**
 * Check and record rate limiting for an IP address.
 *
 * @param string $ip Client IP.
 * @return bool True when the request is allowed.
 */
function portfolio_contact_rate_limit(string $ip): bool
{
    $now = time();
    $window = 600;
    $max = 3;
    $allowed = true;

    // Session limit.
    $sessionStore = $_SESSION['portfolio_contact_attempts'] ?? array();
    $sessionStore = is_array($sessionStore) ? $sessionStore : array();
    $sessionStore = array_values(array_filter($sessionStore, static function ($time) use ($now, $window): bool {
        return is_int($time) && ($now - $time) < $window;
    }));

    if (count($sessionStore) >= $max) {
        $allowed = false;
    }

    // Additional file-based IP limit (works without cookies).
    $dir = portfolio_storage_dir() . DIRECTORY_SEPARATOR . 'ratelimit';
    if (!is_dir($dir)) {
        @mkdir($dir, 0750, true);
    }

    $file = $dir . DIRECTORY_SEPARATOR . hash('sha256', $ip) . '.json';
    $ipStore = array();

    if (is_file($file)) {
        $decoded = json_decode((string) @file_get_contents($file), true);
        $ipStore = is_array($decoded) ? $decoded : array();
    }

    $ipStore = array_values(array_filter($ipStore, static function ($time) use ($now, $window): bool {
        return is_int($time) && ($now - $time) < $window;
    }));

    if (count($ipStore) >= $max) {
        $allowed = false;
    }

    if ($allowed) {
        $sessionStore[] = $now;
        $ipStore[] = $now;
        $_SESSION['portfolio_contact_attempts'] = $sessionStore;
        @file_put_contents($file, json_encode($ipStore), LOCK_EX);
    }

    return $allowed;
}

// -----------------------------------------------------------------------------
// Request guards
// -----------------------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    portfolio_contact_respond(false, 'Method not allowed. Please submit the contact form.', 405, 'method');
}

if (!portfolio_contact_same_origin()) {
    portfolio_contact_respond(false, 'This request could not be verified. Please refresh the page and try again.', 403, 'security');
}

$honeypot = trim((string) ($_POST['website'] ?? ''));
if ($honeypot !== '') {
    // Pretend success so bots do not learn anything.
    portfolio_contact_respond(true, 'Thanks — your message has been sent.', 200);
}

$postedToken = (string) ($_POST['csrf_token'] ?? '');
$sessionToken = (string) ($_SESSION['portfolio_contact_token'] ?? '');
$tokenTime = (int) ($_SESSION['portfolio_contact_token_time'] ?? 0);

if ($sessionToken !== '' && $tokenTime > 0 && (time() - $tokenTime) < 7200) {
    if ($postedToken === '' || !hash_equals($sessionToken, $postedToken)) {
        portfolio_contact_respond(false, 'Your form session expired. Please refresh the page and try again.', 419, 'security');
    }
}

// -----------------------------------------------------------------------------
// Read and validate
// -----------------------------------------------------------------------------
$profile = portfolio_profile();
$name = portfolio_contact_clean((string) ($_POST['name'] ?? ''));
$email = portfolio_contact_clean((string) ($_POST['email'] ?? ''));
$phone = portfolio_contact_clean((string) ($_POST['phone'] ?? ''));
$location = portfolio_contact_clean((string) ($_POST['location'] ?? ''));
$subject = portfolio_contact_clean((string) ($_POST['subject'] ?? ''));
$projectType = portfolio_contact_clean((string) ($_POST['project_type'] ?? ''));
$budget = portfolio_contact_clean((string) ($_POST['budget'] ?? ''));
$message = trim(str_replace(array("\r\n", "\r"), "\n", (string) ($_POST['message'] ?? '')));

$projectTypes = array(
    'Website Design',
    'WordPress Website Development',
    'Custom WordPress Theme',
    'Custom WordPress Plugin',
    'Business Website',
    'E-commerce Website',
    'Responsive Website Design',
    'Website Redesign & Maintenance',
    'Other',
);

$budgets = array('', 'Under $500', '$500 – $1,500', '$1,500 – $3,000', '$3,000+', 'Not sure yet');

if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
    portfolio_contact_respond(false, 'Please enter your name (2–100 characters).', 422, 'validation');
}

if (mb_strlen($email) > 190 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    portfolio_contact_respond(false, 'Please enter a valid email address.', 422, 'validation');
}

if (mb_strlen($phone) < 7 || mb_strlen($phone) > 24 || !preg_match('/^\+?[0-9\s().-]{7,24}$/', $phone)) {
    portfolio_contact_respond(false, 'Please enter a valid phone number.', 422, 'validation');
}

if (mb_strlen($location) > 120) {
    portfolio_contact_respond(false, 'Please keep the location under 120 characters.', 422, 'validation');
}

if (mb_strlen($subject) > 180) {
    portfolio_contact_respond(false, 'Please keep the subject under 180 characters.', 422, 'validation');
}

if (!in_array($projectType, $projectTypes, true)) {
    portfolio_contact_respond(false, 'Please choose a valid project type.', 422, 'validation');
}

if (!in_array($budget, $budgets, true)) {
    portfolio_contact_respond(false, 'Please choose a valid budget range.', 422, 'validation');
}

if (mb_strlen($message) < 10 || mb_strlen($message) > 6000) {
    portfolio_contact_respond(false, 'Please write a message between 10 and 6,000 characters.', 422, 'validation');
}

if (strpos($email . $name . $subject . $phone, "\n") !== false) {
    portfolio_contact_respond(false, 'Please remove line breaks from the header fields.', 422, 'validation');
}

if (preg_match_all('~https?://~i', $message) > 6) {
    portfolio_contact_respond(false, 'That message contains too many links. Please shorten it and try again.', 422, 'validation');
}

$ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
if (!portfolio_contact_rate_limit($ip)) {
    portfolio_contact_respond(
        false,
        'Too many messages in a short time. Please wait a few minutes or email ' . $profile['email'] . ' directly.',
        429,
        'rate'
    );
}

// -----------------------------------------------------------------------------
// Compose and send
// -----------------------------------------------------------------------------
$subjectLine = $subject !== '' ? $subject : 'New project enquiry';
$mailSubject = sprintf('[Portfolio] %s — %s', $projectType, $name);

$body = "New message from your portfolio contact form\n";
$body .= str_repeat('-', 58) . "\n\n";
$body .= 'Name:         ' . $name . "\n";
$body .= 'Email:        ' . $email . "\n";
$body .= 'Phone:        ' . $phone . "\n";
$body .= 'Location:     ' . ($location !== '' ? $location : 'Not provided') . "\n";
$body .= 'Subject:      ' . $subjectLine . "\n";
$body .= 'Project type: ' . $projectType . "\n";
$body .= 'Budget:       ' . ($budget !== '' ? $budget : 'Not provided') . "\n\n";
$body .= "Message:\n" . $message . "\n\n";
$body .= str_repeat('-', 58) . "\n";
$body .= 'Sent: ' . date('Y-m-d H:i:s T') . "\n";
$body .= 'IP:   ' . $ip . "\n";

$recipient = trim((string) portfolio_env('CONTACT_TO', (string) $profile['email']));
if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
    $recipient = (string) $profile['email'];
}

[$sent, $error] = portfolio_smtp_send($recipient, $mailSubject, $body, $email, $name);

if (!$sent) {
    portfolio_contact_respond(
        false,
        'The message could not be sent right now. ' . ($error !== '' ? $error . ' ' : '') . 'Please email ' . $profile['email'] . ' directly.',
        500,
        'server'
    );
}

portfolio_contact_respond(
    true,
    'Thanks for reaching out — your message has been sent. I will reply within one business day.',
    200
);
