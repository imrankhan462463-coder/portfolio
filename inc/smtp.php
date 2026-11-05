<?php
/**
 * Minimal dependency-free SMTP client.
 *
 * Configure delivery with environment variables (see .env.example). Credentials
 * are never exposed to the browser. When CONTACT_DEV_MODE=true the message is
 * written to storage/contact.log instead of being sent.
 *
 * @package ImranPortfolio
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * Read a complete SMTP response.
 *
 * @param resource $socket Socket.
 * @return array{0:int,1:string}
 */
function portfolio_smtp_read($socket): array
{
    $lines = array();
    $code = 0;

    while (!feof($socket)) {
        $line = fgets($socket, 1024);

        if ($line === false) {
            break;
        }

        $lines[] = rtrim($line, "\r\n");
        $code = (int) substr($line, 0, 3);

        // A space in the fourth position marks the final line of a reply.
        if (strlen($line) < 4 || $line[3] !== '-') {
            break;
        }
    }

    return array($code, implode("\n", $lines));
}

/**
 * Send one SMTP command and assert the response code.
 *
 * @param resource  $socket  Socket.
 * @param string    $command Command text.
 * @param int|array $expected Expected code(s).
 * @return array{0:bool,1:string}
 */
function portfolio_smtp_command($socket, string $command, $expected): array
{
    if (fwrite($socket, $command . "\r\n") === false) {
        return array(false, 'Could not write to the SMTP connection.');
    }

    [$code, $text] = portfolio_smtp_read($socket);
    $allowed = is_array($expected) ? $expected : array($expected);

    if (!in_array($code, $allowed, true)) {
        return array(false, 'SMTP server rejected the request (' . $code . ').');
    }

    return array(true, $text);
}

/**
 * Encode a header value for UTF-8 safety.
 *
 * @param string $value Value.
 * @return string
 */
function portfolio_smtp_header(string $value): string
{
    $value = str_replace(array("\r", "\n"), '', $value);

    if (preg_match('/[^\x20-\x7E]/', $value)) {
        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    return $value;
}

/**
 * Send an email using SMTP or the development log.
 *
 * @param string $to        Recipient email.
 * @param string $subject   Subject.
 * @param string $body      Plain-text body.
 * @param string $replyTo   Reply-to email.
 * @param string $replyName Reply-to name.
 * @return array{0:bool,1:string} Success and a safe error message.
 */
function portfolio_smtp_send(string $to, string $subject, string $body, string $replyTo = '', string $replyName = ''): array
{
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return array(false, 'The recipient email address is not valid.');
    }

    $devMode = portfolio_env_bool(portfolio_env('CONTACT_DEV_MODE', 'false'));

    if ($devMode) {
        $dir = portfolio_storage_dir();
        $log = $dir . DIRECTORY_SEPARATOR . 'contact.log';
        $entry = sprintf(
            "[%s]\nTo: %s\nSubject: %s\nReply-To: %s\n\n%s\n%s\n",
            date('c'),
            $to,
            $subject,
            $replyTo,
            $body,
            str_repeat('-', 72)
        );

        return @file_put_contents($log, $entry, FILE_APPEND | LOCK_EX) !== false
            ? array(true, '')
            : array(false, 'Development mail log could not be written.');
    }

    $host = trim((string) portfolio_env('SMTP_HOST', ''));
    $port = (int) portfolio_env('SMTP_PORT', '587');
    $secure = strtolower(trim((string) portfolio_env('SMTP_SECURE', 'tls')));
    $user = (string) portfolio_env('SMTP_USER', '');
    $pass = (string) portfolio_env('SMTP_PASS', '');
    $from = trim((string) portfolio_env('SMTP_FROM', $user !== '' ? $user : 'no-reply@' . $host));
    $fromName = trim((string) portfolio_env('SMTP_FROM_NAME', 'IMRAN.DEV'));
    $timeout = max(5, (int) portfolio_env('SMTP_TIMEOUT', '20'));

    if ($host === '') {
        return array(false, 'SMTP is not configured. Add SMTP_HOST and credentials to .env.');
    }

    $transport = $secure === 'ssl' ? 'ssl://' : 'tcp://';
    $context = stream_context_create(array(
        'ssl' => array(
            'verify_peer'      => true,
            'verify_peer_name' => true,
        ),
    ));

    $errno = 0;
    $errstr = '';
    $socket = @stream_socket_client($transport . $host . ':' . $port, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context);

    if (!is_resource($socket)) {
        return array(false, 'Could not connect to the mail server. Check the SMTP settings.');
    }

    stream_set_timeout($socket, $timeout);

    [$code] = portfolio_smtp_read($socket);
    if ($code !== 220) {
        fclose($socket);
        return array(false, 'The mail server did not accept the connection.');
    }

    $hostname = 'localhost';
    $ehlo = portfolio_smtp_command($socket, 'EHLO ' . $hostname, 250);
    if (!$ehlo[0]) {
        fclose($socket);
        return array(false, 'The mail server did not respond to EHLO.');
    }

    if ($secure === 'tls') {
        $start = portfolio_smtp_command($socket, 'STARTTLS', 220);
        if (!$start[0]) {
            fclose($socket);
            return array(false, 'The mail server could not start TLS.');
        }

        $crypto = @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        if ($crypto !== true) {
            fclose($socket);
            return array(false, 'Could not establish a secure TLS connection.');
        }

        $ehlo = portfolio_smtp_command($socket, 'EHLO ' . $hostname, 250);
        if (!$ehlo[0]) {
            fclose($socket);
            return array(false, 'The mail server did not respond after TLS negotiation.');
        }
    }

    if ($user !== '') {
        $auth = portfolio_smtp_command($socket, 'AUTH LOGIN', 334);
        if (!$auth[0]) {
            fclose($socket);
            return array(false, 'The mail server does not support the configured authentication method.');
        }

        $authUser = portfolio_smtp_command($socket, base64_encode($user), 334);
        if (!$authUser[0]) {
            fclose($socket);
            return array(false, 'SMTP authentication failed.');
        }

        $authPass = portfolio_smtp_command($socket, base64_encode($pass), 235);
        if (!$authPass[0]) {
            fclose($socket);
            return array(false, 'SMTP authentication failed. Check SMTP_USER and SMTP_PASS.');
        }
    }

    $mailFrom = portfolio_smtp_command($socket, 'MAIL FROM:<' . $from . '>', 250);
    if (!$mailFrom[0]) {
        fclose($socket);
        return array(false, 'The mail server rejected the sender address.');
    }

    $rcpt = portfolio_smtp_command($socket, 'RCPT TO:<' . $to . '>', array(250, 251));
    if (!$rcpt[0]) {
        fclose($socket);
        return array(false, 'The mail server rejected the recipient address.');
    }

    $data = portfolio_smtp_command($socket, 'DATA', 354);
    if (!$data[0]) {
        fclose($socket);
        return array(false, 'The mail server rejected the message data request.');
    }

    $replyTo = filter_var($replyTo, FILTER_VALIDATE_EMAIL) ? $replyTo : $from;
    $domain = substr(strrchr($from, '@') ?: '@localhost', 1);
    $messageId = bin2hex(random_bytes(10)) . '@' . $domain;
    $body = str_replace(array("\r\n", "\r"), "\n", $body);
    $body = str_replace("\n", "\r\n", $body);
    $body = preg_replace('/^\./m', '..', $body) ?? $body;

    $headers = array(
        'Date: ' . date(DATE_RFC2822),
        'From: ' . portfolio_smtp_header($fromName) . ' <' . $from . '>',
        'To: <' . $to . '>',
        'Subject: ' . portfolio_smtp_header($subject),
        'Reply-To: ' . portfolio_smtp_header($replyName !== '' ? $replyName : $replyTo) . ' <' . $replyTo . '>',
        'Message-ID: <' . $messageId . '>',
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        'X-Mailer: IMRAN.DEV Portfolio',
    );

    $payload = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.";

    if (fwrite($socket, $payload . "\r\n") === false) {
        fclose($socket);
        return array(false, 'The message could not be transmitted.');
    }

    [$sendCode] = portfolio_smtp_read($socket);
    portfolio_smtp_command($socket, 'QUIT', 221);
    fclose($socket);

    if ($sendCode !== 250) {
        return array(false, 'The mail server did not confirm delivery.');
    }

    return array(true, '');
}
