<?php
/**
 * Shared configuration loader for the standalone PHP site.
 *
 * Values are read from the project `.env` file when present, then from the
 * process environment. Secrets are never printed or sent to the browser.
 *
 * @package ImranPortfolio
 */

declare(strict_types=1);

if (!defined('PORTFOLIO_ROOT')) {
    define('PORTFOLIO_ROOT', dirname(__DIR__));
}

if (!defined('PORTFOLIO_STORAGE')) {
    define('PORTFOLIO_STORAGE', PORTFOLIO_ROOT . DIRECTORY_SEPARATOR . 'storage');
}

/**
 * Read a configuration value.
 *
 * @param string      $key     Environment key.
 * @param string|null $default Fallback value.
 * @return string|null
 */
function portfolio_env(string $key, ?string $default = null): ?string
{
    static $values = null;

    if ($values === null) {
        $values = array();
        $file = PORTFOLIO_ROOT . DIRECTORY_SEPARATOR . '.env';

        if (is_file($file) && is_readable($file)) {
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines ?: array() as $line) {
                $line = trim($line);

                if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
                    continue;
                }

                $line = preg_replace('/^export\s+/', '', $line) ?? $line;
                [$name, $value] = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
                    $quote = $value[0];
                    if (substr($value, -1) === $quote) {
                        $value = substr($value, 1, -1);
                    }
                } else {
                    $value = preg_replace('/\s+#.*$/', '', $value) ?? $value;
                }

                if ($name !== '') {
                    $values[$name] = $value;
                }
            }
        }

        $values = array_merge($values, $_ENV, $_SERVER);
    }

    if (array_key_exists($key, $values) && $values[$key] !== '') {
        return (string) $values[$key];
    }

    $fromProcess = getenv($key);
    if ($fromProcess !== false && $fromProcess !== '') {
        return (string) $fromProcess;
    }

    return $default;
}

/**
 * Convert common truthy values to a boolean.
 *
 * @param string|null $value Value.
 * @return bool
 */
function portfolio_env_bool(?string $value): bool
{
    return in_array(strtolower(trim((string) $value)), array('1', 'true', 'yes', 'on'), true);
}

/**
 * Load and cache a JSON document.
 *
 * @param string $relativePath Path relative to the project root.
 * @return array<mixed>
 */
function portfolio_json(string $relativePath): array
{
    static $cache = array();

    if (isset($cache[$relativePath])) {
        return $cache[$relativePath];
    }

    $file = PORTFOLIO_ROOT . DIRECTORY_SEPARATOR . str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $relativePath);

    if (!is_file($file) || !is_readable($file)) {
        return $cache[$relativePath] = array();
    }

    $decoded = json_decode((string) file_get_contents($file), true);
    $cache[$relativePath] = is_array($decoded) ? $decoded : array();

    return $cache[$relativePath];
}

/**
 * Return the editable profile settings.
 *
 * @return array<string, mixed>
 */
function portfolio_profile(): array
{
    $defaults = array(
        'full_name'        => 'MD Imran Hosan',
        'brand_name'       => 'IMRAN HOSAN',
        'logo_text'        => 'IMRAN.DEV',
        'title'            => 'Web Designer & WordPress Developer',
        'identity'         => 'Web Designer | WordPress Developer',
        'email'            => 'imransikdar462@gmail.com',
        'phone'            => '+8801623331050',
        'whatsapp'         => '+8801623331050',
        'whatsapp_url'     => 'https://wa.me/8801623331050',
        'location'         => 'Mirpur 10, Dhaka, Bangladesh',
        'linkedin'         => 'https://www.linkedin.com/in/imran-sikdar-02183733b',
        'github'           => '',
        'facebook'         => '',
        'instagram'        => '',
        'profile_image'    => 'assets/images/imran.jpg',
        'resume'           => 'assets/files/imran-cv.pdf',
        'availability'     => 'Available for new projects',
        'response_time'    => 'Replies within 24 hours',
        'hero_heading'     => 'Building Digital Experiences That Inspire.',
        'hero_description' => 'I am MD Imran Hosan, a Web Designer and WordPress Developer passionate about creating modern, responsive, user-friendly, and visually stunning websites that help businesses grow online.',
        'bio_short'        => '',
        'bio_long'         => '',
    );

    return array_merge($defaults, portfolio_json('data/profile.json'));
}

/**
 * Return the editable project list.
 *
 * @return array<int, array<string, mixed>>
 */
function portfolio_projects(): array
{
    $data = portfolio_json('data/projects.json');

    return isset($data['projects']) && is_array($data['projects']) ? $data['projects'] : array();
}

/**
 * Normalise a URL for safe HTML output.
 *
 * @param string $url URL.
 * @return string
 */
function portfolio_safe_url(string $url): string
{
    $url = trim($url);

    if ($url === '' || $url[0] === '#' || strpos($url, 'mailto:') === 0 || strpos($url, 'tel:') === 0) {
        return $url;
    }

    if (strpos($url, 'assets/') === 0 || strpos($url, 'data/') === 0 || strpos($url, 'demo.html') === 0) {
        return $url;
    }

    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

    return in_array($scheme, array('http', 'https'), true) ? $url : '';
}

/**
 * Ensure the storage directory exists.
 *
 * @return string
 */
function portfolio_storage_dir(): string
{
    if (!is_dir(PORTFOLIO_STORAGE)) {
        @mkdir(PORTFOLIO_STORAGE, 0750, true);
    }

    return PORTFOLIO_STORAGE;
}
