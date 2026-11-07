<?php
/**
 * GitHub profile + repository endpoint.
 *
 * Fetches the public profile and repositories for the configured GitHub
 * account, caches the result inside storage/ and serves stale data when the
 * GitHub API is temporarily unavailable or rate limited.
 *
 * The front-end (assets/js/live.js) falls back to api.github.com directly when
 * this endpoint is not available, so the portfolio still works on static
 * hosting.
 *
 * @package ImranPortfolio
 */

declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=300, stale-while-revalidate=600');
header('X-Content-Type-Options: nosniff');

/**
 * Send a JSON response and stop.
 *
 * @param array<string, mixed> $payload Response data.
 * @param int                  $status  HTTP status code.
 * @return void
 */
function github_respond(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Extract a useful error message from a GitHub API response.
 *
 * @param int    $status HTTP status.
 * @param string $body   Raw body.
 * @return string
 */
function github_error_message(int $status, string $body): string
{
    $decoded = json_decode($body, true);

    if (is_array($decoded) && isset($decoded['message']) && is_string($decoded['message'])) {
        return $decoded['message'];
    }

    return $status > 0 ? 'GitHub returned HTTP ' . $status . '.' : 'GitHub could not be reached.';
}

/**
 * Perform an HTTP GET request without requiring a specific extension.
 *
 * @param string        $url     Request URL.
 * @param array<string> $headers Request headers.
 * @return array{0:int,1:string} Status code and body.
 */
function github_request(string $url, array $headers): array
{
    if (function_exists('curl_init')) {
        $handle = curl_init($url);

        if ($handle !== false) {
            curl_setopt_array(
                $handle,
                array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => false,
                    CURLOPT_CONNECTTIMEOUT => 6,
                    CURLOPT_TIMEOUT        => 14,
                    CURLOPT_ENCODING       => '',
                    CURLOPT_HTTPHEADER     => $headers,
                    CURLOPT_USERAGENT      => 'IMRAN.DEV-Portfolio/1.0',
                    CURLOPT_SSL_VERIFYPEER => true,
                    CURLOPT_SSL_VERIFYHOST => 2,
                )
            );

            $body = curl_exec($handle);
            $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
            $error = curl_error($handle);
            curl_close($handle);

            if (is_string($body)) {
                return array($status, $body);
            }

            return array(0, $error !== '' ? $error : 'GitHub could not be reached.');
        }
    }

    if (!filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) {
        return array(0, 'No HTTP transport is available on this server.');
    }

    $context = stream_context_create(
        array(
            'http' => array(
                'method'        => 'GET',
                'header'        => implode("\r\n", $headers),
                'timeout'       => 14,
                'ignore_errors' => true,
            ),
            'ssl'  => array(
                'verify_peer'      => true,
                'verify_peer_name' => true,
            ),
        )
    );

    $body = @file_get_contents($url, false, $context);
    $status = 0;

    if (isset($http_response_header) && is_array($http_response_header) && isset($http_response_header[0])) {
        if (preg_match('#\s(\d{3})\s#', (string) $http_response_header[0], $matches)) {
            $status = (int) $matches[1];
        }
    }

    if (is_string($body)) {
        return array($status, $body);
    }

    return array(0, 'GitHub could not be reached.');
}

$profile = portfolio_profile();

// Resolve the GitHub username: query string → environment → profile settings.
$username = isset($_GET['user']) && is_string($_GET['user']) ? trim($_GET['user']) : '';

if ($username === '') {
    $username = trim((string) portfolio_env('GITHUB_USERNAME', ''));
}

if ($username === '') {
    $username = trim((string) ($profile['github_username'] ?? ''));
}

if ($username === '' && !empty($profile['github'])) {
    $path = trim((string) parse_url((string) $profile['github'], PHP_URL_PATH), '/');
    $segments = explode('/', $path);
    $username = trim((string) ($segments[0] ?? ''));
}

if (!preg_match('/^[A-Za-z0-9](?:[A-Za-z0-9-]{0,37}[A-Za-z0-9])?$/', $username)) {
    github_respond(
        array(
            'ok'      => false,
            'error'   => 'A valid GitHub username is required.',
            'message' => 'Set a GitHub username in the admin panel or in data/profile.json.',
        ),
        400
    );
}

$cacheTtl = (int) portfolio_env('GITHUB_CACHE_TTL', '900');
$cacheTtl = max(60, min($cacheTtl, 86400));
$cacheFile = portfolio_storage_dir() . DIRECTORY_SEPARATOR . 'github-' . sha1(strtolower($username)) . '.json';
$cached = array();

if (is_file($cacheFile) && is_readable($cacheFile)) {
    $decodedCache = json_decode((string) file_get_contents($cacheFile), true);
    if (is_array($decodedCache)) {
        $cached = $decodedCache;
    }
}

$hasCache = isset($cached['profile'], $cached['repos'])
    && is_array($cached['profile'])
    && is_array($cached['repos']);

if ($hasCache && isset($cached['fetched_at']) && (time() - (int) $cached['fetched_at']) < $cacheTtl) {
    github_respond(
        array(
            'ok'         => true,
            'source'     => 'cache',
            'username'   => $username,
            'fetched_at' => (int) $cached['fetched_at'],
            'profile'    => $cached['profile'],
            'repos'      => $cached['repos'],
        )
    );
}

$headers = array(
    'Accept: application/vnd.github+json',
    'X-GitHub-Api-Version: 2022-11-28',
    'User-Agent: IMRAN.DEV-Portfolio/1.0',
);

$token = trim((string) portfolio_env('GITHUB_TOKEN', ''));
if ($token !== '') {
    $headers[] = 'Authorization: Bearer ' . $token;
}

$userUrl = 'https://api.github.com/users/' . rawurlencode($username);
$reposUrl = 'https://api.github.com/users/' . rawurlencode($username)
    . '/repos?per_page=100&sort=updated&type=owner';

list($userStatus, $userBody) = github_request($userUrl, $headers);
list($reposStatus, $reposBody) = github_request($reposUrl, $headers);

if ($userStatus !== 200 || $reposStatus !== 200) {
    if ($hasCache) {
        github_respond(
            array(
                'ok'         => true,
                'source'     => 'stale',
                'username'   => $username,
                'fetched_at' => (int) ($cached['fetched_at'] ?? 0),
                'profile'    => $cached['profile'],
                'repos'      => $cached['repos'],
                'notice'     => 'Showing the most recent cached GitHub data.',
            )
        );
    }

    $failedStatus = $userStatus !== 200 ? $userStatus : $reposStatus;
    $failedBody = $userStatus !== 200 ? $userBody : $reposBody;

    github_respond(
        array(
            'ok'      => false,
            'error'   => github_error_message($failedStatus, $failedBody),
            'message' => 'GitHub data could not be loaded right now.',
        ),
        502
    );
}

$user = json_decode($userBody, true);
$repoList = json_decode($reposBody, true);

if (!is_array($user) || !is_array($repoList)) {
    github_respond(
        array(
            'ok'      => false,
            'error'   => 'GitHub returned an unexpected response.',
            'message' => 'GitHub data could not be read right now.',
        ),
        502
    );
}

$normalisedProfile = array(
    'login'        => (string) ($user['login'] ?? $username),
    'name'         => isset($user['name']) && is_string($user['name']) ? $user['name'] : '',
    'avatar_url'   => (string) ($user['avatar_url'] ?? ''),
    'html_url'     => (string) ($user['html_url'] ?? ('https://github.com/' . $username)),
    'bio'          => isset($user['bio']) && is_string($user['bio']) ? $user['bio'] : '',
    'public_repos' => (int) ($user['public_repos'] ?? 0),
    'followers'    => (int) ($user['followers'] ?? 0),
    'following'    => (int) ($user['following'] ?? 0),
    'created_at'   => (string) ($user['created_at'] ?? ''),
);

$normalisedRepos = array();

foreach ($repoList as $repo) {
    if (!is_array($repo) || empty($repo['name'])) {
        continue;
    }

    $topics = array();

    if (isset($repo['topics']) && is_array($repo['topics'])) {
        foreach ($repo['topics'] as $topic) {
            if (is_string($topic) && $topic !== '') {
                $topics[] = $topic;
            }
        }
    }

    $normalisedRepos[] = array(
        'name'             => (string) $repo['name'],
        'description'      => isset($repo['description']) && is_string($repo['description']) ? $repo['description'] : '',
        'html_url'         => (string) ($repo['html_url'] ?? ''),
        'language'         => isset($repo['language']) && is_string($repo['language']) ? $repo['language'] : '',
        'stargazers_count' => (int) ($repo['stargazers_count'] ?? 0),
        'forks_count'      => (int) ($repo['forks_count'] ?? 0),
        'topics'           => array_slice($topics, 0, 6),
        'updated_at'       => (string) ($repo['updated_at'] ?? ''),
        'pushed_at'        => (string) ($repo['pushed_at'] ?? ''),
        'fork'             => !empty($repo['fork']),
        'archived'         => !empty($repo['archived']),
        'homepage'         => isset($repo['homepage']) && is_string($repo['homepage']) ? $repo['homepage'] : '',
    );
}

$fetchedAt = time();
$cachePayload = array(
    'fetched_at' => $fetchedAt,
    'username'   => $username,
    'profile'    => $normalisedProfile,
    'repos'      => $normalisedRepos,
);

// Cache writes are best-effort — the response still succeeds if storage is read-only.
// Do not pre-check is_writable(): some Windows/OneDrive folders report false even
// when writes succeed, so attempt the write and ignore any failure instead.
if (is_dir(dirname($cacheFile))) {
    $encodedCache = json_encode($cachePayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    if ($encodedCache !== false) {
        @file_put_contents($cacheFile, $encodedCache, LOCK_EX);
    }
}

github_respond(
    array(
        'ok'         => true,
        'source'     => 'live',
        'username'   => $username,
        'fetched_at' => $fetchedAt,
        'profile'    => $normalisedProfile,
        'repos'      => $normalisedRepos,
    )
);
