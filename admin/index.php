<?php
/**
 * Secure standalone admin for profile settings and projects.
 *
 * @package ImranPortfolio
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/inc/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(array(
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Strict',
    ));
    session_start();
}

/**
 * Escape HTML.
 *
 * @param mixed $value Value.
 * @return string
 */
function admin_e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Return a CSRF token.
 *
 * @return string
 */
function admin_csrf(): string
{
    if (empty($_SESSION['portfolio_admin_csrf'])) {
        $_SESSION['portfolio_admin_csrf'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['portfolio_admin_csrf'];
}

/**
 * Validate a CSRF token.
 *
 * @return bool
 */
function admin_verify_csrf(): bool
{
    $posted = (string) ($_POST['csrf'] ?? '');
    $stored = (string) ($_SESSION['portfolio_admin_csrf'] ?? '');

    return $posted !== '' && $stored !== '' && hash_equals($stored, $posted);
}

/**
 * Clean a single-line text value.
 *
 * @param mixed $value Value.
 * @param int   $max   Maximum length.
 * @return string
 */
function admin_text($value, int $max = 300): string
{
    $value = strip_tags((string) $value);
    $value = str_replace(array("\r", "\0"), '', $value);
    $value = trim(preg_replace('/[ \t]+/', ' ', $value) ?? $value);

    return mb_substr($value, 0, $max);
}

/**
 * Clean a multiline text value.
 *
 * @param mixed $value Value.
 * @param int   $max   Maximum length.
 * @return string
 */
function admin_multiline($value, int $max = 5000): string
{
    $value = strip_tags((string) $value);
    $value = str_replace(array("\r\n", "\r"), "\n", $value);
    $value = trim($value);

    return mb_substr($value, 0, $max);
}

/**
 * Validate an optional URL.
 *
 * @param mixed $value Value.
 * @return string
 */
function admin_url($value): string
{
    $value = trim((string) $value);

    if ($value === '') {
        return '';
    }

    return filter_var($value, FILTER_VALIDATE_URL) ? $value : '';
}

/**
 * Build a filesystem-safe slug.
 *
 * @param string $value Value.
 * @return string
 */
function admin_slug(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    $value = trim($value, '-');

    return $value !== '' ? $value : 'project-' . time();
}

/**
 * Write a JSON file atomically.
 *
 * @param string $path Data relative to project root.
 * @param array  $data Data.
 * @return bool
 */
function admin_write_json(string $path, array $data): bool
{
    $file = PORTFOLIO_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    if ($json === false) {
        return false;
    }

    $temp = $file . '.tmp';
    if (@file_put_contents($temp, $json . PHP_EOL, LOCK_EX) === false) {
        return false;
    }

    return @rename($temp, $file);
}

/**
 * Store an uploaded image and return its public relative path.
 *
 * @param string $field    Upload field.
 * @param string $current  Existing path.
 * @param string $subfolder Subfolder under assets/images.
 * @param string $prefix   File prefix.
 * @return array{0:string,1:string} Path and an optional error.
 */
function admin_upload_image(string $field, string $current, string $subfolder = '', string $prefix = 'profile'): array
{
    if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return array($current, '');
    }

    $file = $_FILES[$field];

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return array($current, 'The image upload failed.');
    }

    if (($file['size'] ?? 0) > 4 * 1024 * 1024) {
        return array($current, 'Images must be smaller than 4 MB.');
    }

    $info = @getimagesize($file['tmp_name']);
    $mimes = array(
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_WEBP => 'webp',
    );

    if (!$info || !isset($mimes[$info[2]])) {
        return array($current, 'Please upload a JPG, PNG or WebP image.');
    }

    $ext = $mimes[$info[2]];
    $dir = PORTFOLIO_ROOT . '/assets/images' . ($subfolder !== '' ? '/' . trim($subfolder, '/') : '');
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    $name = $prefix . '-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;

    if (!@move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        return array($current, 'The image could not be saved.');
    }

    return array('assets/images' . ($subfolder !== '' ? '/' . trim($subfolder, '/') : '') . '/' . $name, '');
}

/**
 * Store the uploaded CV as a PDF.
 *
 * @param string $current Current path.
 * @return array{0:string,1:string}
 */
function admin_upload_cv(string $current): array
{
    if (empty($_FILES['resume_file']) || ($_FILES['resume_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return array($current, '');
    }

    $file = $_FILES['resume_file'];

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return array($current, 'The CV upload failed.');
    }

    if (($file['size'] ?? 0) > 8 * 1024 * 1024) {
        return array($current, 'The CV must be smaller than 8 MB.');
    }

    $head = (string) @file_get_contents($file['tmp_name'], false, null, 0, 5);
    if ($head !== '%PDF-') {
        return array($current, 'Please upload a valid PDF file.');
    }

    $dir = PORTFOLIO_ROOT . '/assets/files';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    if (!@move_uploaded_file($file['tmp_name'], $dir . '/imran-cv.pdf')) {
        return array($current, 'The CV could not be saved.');
    }

    return array('assets/files/imran-cv.pdf', '');
}

$passwordHash = trim((string) portfolio_env('ADMIN_PASSWORD_HASH', ''));
$loggedIn = !empty($_SESSION['portfolio_admin']);
$notice = '';
$noticeType = 'ok';

// -----------------------------------------------------------------------------
// Authentication
// -----------------------------------------------------------------------------
if (($_POST['action'] ?? '') === 'login') {
    if (!admin_verify_csrf()) {
        $notice = 'Security check failed. Please try again.';
        $noticeType = 'error';
    } elseif ($passwordHash === '') {
        $notice = 'Admin access is not configured yet. Add ADMIN_PASSWORD_HASH to .env.';
        $noticeType = 'error';
    } else {
        $attempts = (int) ($_SESSION['portfolio_login_attempts'] ?? 0);
        $lastAttempt = (int) ($_SESSION['portfolio_login_last'] ?? 0);

        if ($attempts >= 6 && (time() - $lastAttempt) < 900) {
            $notice = 'Too many attempts. Please wait 15 minutes.';
            $noticeType = 'error';
        } elseif (password_verify((string) ($_POST['password'] ?? ''), $passwordHash)) {
            session_regenerate_id(true);
            $_SESSION['portfolio_admin'] = true;
            $_SESSION['portfolio_login_attempts'] = 0;
            header('Location: index.php');
            exit;
        } else {
            $_SESSION['portfolio_login_attempts'] = $attempts + 1;
            $_SESSION['portfolio_login_last'] = time();
            $notice = 'Incorrect password.';
            $noticeType = 'error';
        }
    }
}

if (($_POST['action'] ?? '') === 'logout') {
    if (admin_verify_csrf()) {
        $_SESSION = array();
        session_destroy();
        header('Location: index.php');
        exit;
    }
}

if ($loggedIn && isset($_GET['saved'])) {
    $notice = 'Changes saved successfully.';
}

if ($loggedIn && isset($_GET['deleted'])) {
    $notice = 'Project deleted.';
}

// -----------------------------------------------------------------------------
// Save profile
// -----------------------------------------------------------------------------
if ($loggedIn && ($_POST['action'] ?? '') === 'save_profile') {
    if (!admin_verify_csrf()) {
        $notice = 'Security check failed.';
        $noticeType = 'error';
    } else {
        $profile = portfolio_profile();
        $textFields = array(
            'full_name', 'brand_name', 'logo_text', 'title', 'identity', 'phone',
            'location', 'availability', 'response_time', 'hero_heading', 'linkedin_label',
        );

        foreach ($textFields as $field) {
            if (isset($_POST[$field])) {
                $profile[$field] = admin_text($_POST[$field], $field === 'hero_heading' ? 220 : 160);
            }
        }

        $profile['email'] = admin_text($_POST['email'] ?? '', 190);
        $profile['whatsapp'] = admin_text($_POST['whatsapp'] ?? '', 40);
        $profile['whatsapp_url'] = admin_url($_POST['whatsapp_url'] ?? '');
        $profile['linkedin'] = admin_url($_POST['linkedin'] ?? '');
        $profile['github'] = admin_url($_POST['github'] ?? '');
        $profile['facebook'] = admin_url($_POST['facebook'] ?? '');
        $profile['instagram'] = admin_url($_POST['instagram'] ?? '');
        $profile['hero_description'] = admin_multiline($_POST['hero_description'] ?? '', 600);
        $profile['bio_short'] = admin_multiline($_POST['bio_short'] ?? '', 800);
        $profile['bio_long'] = admin_multiline($_POST['bio_long'] ?? '', 5000);

        if (!filter_var($profile['email'], FILTER_VALIDATE_EMAIL)) {
            $notice = 'Please enter a valid email address.';
            $noticeType = 'error';
        } elseif ($profile['full_name'] === '') {
            $notice = 'Full name is required.';
            $noticeType = 'error';
        } else {
            [$profile['profile_image'], $imageError] = admin_upload_image('profile_image_file', (string) $profile['profile_image']);
            [$profile['resume'], $cvError] = admin_upload_cv((string) $profile['resume']);

            if ($imageError !== '') {
                $notice = $imageError;
                $noticeType = 'error';
            } elseif ($cvError !== '') {
                $notice = $cvError;
                $noticeType = 'error';
            } else {
                $profile['updated_at'] = date('c');
                if (admin_write_json('data/profile.json', $profile)) {
                    header('Location: index.php?saved=1');
                    exit;
                }
                $notice = 'Profile could not be saved. Check file permissions on the data folder.';
                $noticeType = 'error';
            }
        }
    }
}

// -----------------------------------------------------------------------------
// Save / delete project
// -----------------------------------------------------------------------------
if ($loggedIn && ($_POST['action'] ?? '') === 'save_project') {
    if (!admin_verify_csrf()) {
        $notice = 'Security check failed.';
        $noticeType = 'error';
    } else {
        $data = portfolio_json('data/projects.json');
        $projects = isset($data['projects']) && is_array($data['projects']) ? $data['projects'] : array();
        $originalId = admin_slug((string) ($_POST['original_id'] ?? ''));
        $id = admin_slug((string) ($_POST['project_id'] ?? ''));
        $name = admin_text($_POST['project_name'] ?? '', 120);

        if ($name === '') {
            $notice = 'Project name is required.';
            $noticeType = 'error';
        } else {
            $project = array(
                'id'              => $id,
                'name'            => $name,
                'category'        => admin_slug((string) ($_POST['project_category'] ?? 'web-design')),
                'category_label'  => admin_text($_POST['project_category_label'] ?? 'Web Design', 60),
                'category_detail' => admin_text($_POST['project_category_detail'] ?? '', 120),
                'year'            => admin_text($_POST['project_year'] ?? date('Y'), 10),
                'description'     => admin_multiline($_POST['project_description'] ?? '', 600),
                'image'           => admin_text($_POST['project_image'] ?? '', 300),
                'tech'            => array_values(array_filter(array_map('trim', explode(',', (string) ($_POST['project_tech'] ?? ''))))),
                'demo_url'        => admin_text($_POST['project_demo_url'] ?? '', 300),
                'source_url'      => admin_text($_POST['project_source_url'] ?? '', 300),
                'concept'         => !empty($_POST['project_concept']),
                'featured'        => !empty($_POST['project_featured']),
            );

            [$project['image'], $imageError] = admin_upload_image('project_image_file', $project['image'], 'projects', 'project');
            $project['tech'] = array_slice(array_map('admin_text', $project['tech']), 0, 12);

            if ($imageError !== '') {
                $notice = $imageError;
                $noticeType = 'error';
            } else {
                $updated = false;
                foreach ($projects as $index => $existing) {
                    if ($originalId !== '' && ($existing['id'] ?? '') === $originalId) {
                        $projects[$index] = $project;
                        $updated = true;
                        break;
                    }
                }

                if (!$updated) {
                    foreach ($projects as $existing) {
                        if (($existing['id'] ?? '') === $id) {
                            $id = $id . '-' . substr(bin2hex(random_bytes(2)), 0, 4);
                            $project['id'] = $id;
                            break;
                        }
                    }
                    $projects[] = $project;
                }

                if (admin_write_json('data/projects.json', array('projects' => array_values($projects)))) {
                    header('Location: index.php?saved=1#projects');
                    exit;
                }

                $notice = 'Projects could not be saved. Check file permissions on the data folder.';
                $noticeType = 'error';
            }
        }
    }
}

if ($loggedIn && ($_POST['action'] ?? '') === 'delete_project') {
    if (!admin_verify_csrf()) {
        $notice = 'Security check failed.';
        $noticeType = 'error';
    } else {
        $deleteId = admin_slug((string) ($_POST['delete_id'] ?? ''));
        $data = portfolio_json('data/projects.json');
        $projects = isset($data['projects']) && is_array($data['projects']) ? $data['projects'] : array();
        $projects = array_values(array_filter($projects, static function ($project) use ($deleteId): bool {
            return ($project['id'] ?? '') !== $deleteId;
        }));

        if (admin_write_json('data/projects.json', array('projects' => $projects))) {
            header('Location: index.php?deleted=1#projects');
            exit;
        }

        $notice = 'The project could not be deleted.';
        $noticeType = 'error';
    }
}

$profile = portfolio_profile();
$projects = portfolio_projects();
$editId = isset($_GET['edit']) ? admin_slug((string) $_GET['edit']) : '';
$editProject = null;

foreach ($projects as $project) {
    if (($project['id'] ?? '') === $editId) {
        $editProject = $project;
        break;
    }
}

$profileImage = (string) $profile['profile_image'];
$profileImagePath = PORTFOLIO_ROOT . '/' . $profileImage;
$profileImagePreview = is_file($profileImagePath) ? '../' . $profileImage : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Portfolio Admin — IMRAN.DEV</title>
  <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php if (!$loggedIn): ?>
  <main class="login-shell">
    <div class="login-card">
      <a class="admin-brand" href="../index.html">
        <span>IMRAN<span class="dot">.</span>DEV</span>
        <small>Portfolio control room</small>
      </a>

      <?php if ($notice !== ''): ?>
        <div class="notice notice--<?php echo admin_e($noticeType); ?>"><?php echo admin_e($notice); ?></div>
      <?php endif; ?>

      <?php if ($passwordHash === ''): ?>
        <div class="notice notice--warn">
          <strong>Admin is locked.</strong>
          <p>Generate a password hash from the project folder:</p>
          <code>php tools/hash-password.php "your-strong-password"</code>
          <p>Then add it to <code>.env</code> as <code>ADMIN_PASSWORD_HASH</code> and reload this page.</p>
        </div>
      <?php else: ?>
        <form method="post" class="form-stack">
          <input type="hidden" name="action" value="login">
          <input type="hidden" name="csrf" value="<?php echo admin_e(admin_csrf()); ?>">
          <label class="field">
            <span>Password</span>
            <input type="password" name="password" required autocomplete="current-password" autofocus>
          </label>
          <button class="btn" type="submit">Sign in</button>
        </form>
        <p class="login-note">Protected area. Sessions are HTTP-only and CSRF-protected.</p>
      <?php endif; ?>

      <a class="back-link" href="../index.html">← Back to portfolio</a>
    </div>
  </main>
<?php else: ?>
  <header class="admin-header">
    <div class="admin-header__inner">
      <a class="admin-brand" href="index.php">
        <span>IMRAN<span class="dot">.</span>DEV</span>
        <small>Portfolio control room</small>
      </a>
      <nav class="admin-nav" aria-label="Admin">
        <a href="#profile">Profile</a>
        <a href="#projects">Projects</a>
        <a href="../index.html" target="_blank" rel="noopener">View site</a>
        <form method="post">
          <input type="hidden" name="action" value="logout">
          <input type="hidden" name="csrf" value="<?php echo admin_e(admin_csrf()); ?>">
          <button class="btn btn--ghost" type="submit">Log out</button>
        </form>
      </nav>
    </div>
  </header>

  <main class="admin-main">
    <?php if ($notice !== ''): ?>
      <div class="notice notice--<?php echo admin_e($noticeType); ?>"><?php echo admin_e($notice); ?></div>
    <?php endif; ?>

    <section class="panel" id="profile">
      <div class="panel__head">
        <div>
          <p class="kicker">Personal profile settings</p>
          <h1>Your details, edited once.</h1>
          <p class="muted">These values are stored in <code>data/profile.json</code>. Add <code>data-profile</code> attributes anywhere in the HTML to reuse them.</p>
        </div>
        <a class="btn btn--ghost" href="../index.html" target="_blank" rel="noopener">Preview ↗</a>
      </div>

      <form method="post" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="action" value="save_profile">
        <input type="hidden" name="csrf" value="<?php echo admin_e(admin_csrf()); ?>">

        <div class="form-grid">
          <label class="field"><span>Full name</span><input type="text" name="full_name" value="<?php echo admin_e($profile['full_name']); ?>" maxlength="120" required></label>
          <label class="field"><span>Professional title</span><input type="text" name="title" value="<?php echo admin_e($profile['title']); ?>" maxlength="160"></label>
          <label class="field"><span>Brand name</span><input type="text" name="brand_name" value="<?php echo admin_e($profile['brand_name']); ?>" maxlength="80"></label>
          <label class="field"><span>Logo text</span><input type="text" name="logo_text" value="<?php echo admin_e($profile['logo_text']); ?>" maxlength="40"></label>
          <label class="field"><span>Email</span><input type="email" name="email" value="<?php echo admin_e($profile['email']); ?>" maxlength="190" required></label>
          <label class="field"><span>Phone</span><input type="text" name="phone" value="<?php echo admin_e($profile['phone']); ?>" maxlength="40"></label>
          <label class="field"><span>WhatsApp number</span><input type="text" name="whatsapp" value="<?php echo admin_e($profile['whatsapp']); ?>" maxlength="40"></label>
          <label class="field"><span>WhatsApp URL</span><input type="url" name="whatsapp_url" value="<?php echo admin_e($profile['whatsapp_url']); ?>"></label>
          <label class="field field--wide"><span>Location</span><input type="text" name="location" value="<?php echo admin_e($profile['location']); ?>" maxlength="160"></label>
          <label class="field"><span>LinkedIn URL</span><input type="url" name="linkedin" value="<?php echo admin_e($profile['linkedin']); ?>"></label>
          <label class="field"><span>LinkedIn label</span><input type="text" name="linkedin_label" value="<?php echo admin_e($profile['linkedin_label']); ?>" maxlength="120"></label>
          <label class="field"><span>GitHub URL (optional)</span><input type="url" name="github" value="<?php echo admin_e($profile['github']); ?>"></label>
          <label class="field"><span>Facebook URL (optional)</span><input type="url" name="facebook" value="<?php echo admin_e($profile['facebook']); ?>"></label>
          <label class="field"><span>Instagram URL (optional)</span><input type="url" name="instagram" value="<?php echo admin_e($profile['instagram']); ?>"></label>
          <label class="field"><span>Availability text</span><input type="text" name="availability" value="<?php echo admin_e($profile['availability']); ?>" maxlength="80"></label>
          <label class="field"><span>Response time</span><input type="text" name="response_time" value="<?php echo admin_e($profile['response_time']); ?>" maxlength="80"></label>
          <label class="field field--wide"><span>Hero heading</span><input type="text" name="hero_heading" value="<?php echo admin_e($profile['hero_heading']); ?>" maxlength="220"></label>
          <label class="field field--wide"><span>Hero description</span><textarea name="hero_description" rows="3" maxlength="600"><?php echo admin_e($profile['hero_description']); ?></textarea></label>
          <label class="field field--wide"><span>Short biography</span><textarea name="bio_short" rows="4" maxlength="800"><?php echo admin_e($profile['bio_short']); ?></textarea></label>
          <label class="field field--wide"><span>Full biography (blank line = new paragraph)</span><textarea name="bio_long" rows="9" maxlength="5000"><?php echo admin_e($profile['bio_long']); ?></textarea></label>
        </div>

        <div class="upload-grid">
          <div class="upload-card">
            <span>Profile image</span>
            <?php if ($profileImagePreview !== ''): ?>
              <img src="<?php echo admin_e($profileImagePreview); ?>" alt="Current profile image preview">
            <?php endif; ?>
            <input type="file" name="profile_image_file" accept="image/jpeg,image/png,image/webp">
            <small>JPG, PNG or WebP · max 4 MB · current: <?php echo admin_e($profileImage); ?></small>
          </div>
          <div class="upload-card">
            <span>Resume PDF</span>
            <p class="file-status"><?php echo admin_e($profile['resume']); ?></p>
            <input type="file" name="resume_file" accept="application/pdf">
            <small>PDF only · max 8 MB · saved as assets/files/imran-cv.pdf</small>
          </div>
        </div>

        <button class="btn" type="submit">Save profile</button>
      </form>
    </section>

    <section class="panel" id="projects">
      <div class="panel__head">
        <div>
          <p class="kicker">Project portfolio</p>
          <h1>Add, edit and remove projects.</h1>
          <p class="muted">Changes are written to <code>data/projects.json</code> and appear on the portfolio instantly.</p>
        </div>
        <a class="btn btn--ghost" href="#project-form"><?php echo $editProject ? 'Editing project' : 'Add a project'; ?> ↓</a>
      </div>

      <div class="project-list">
        <?php foreach ($projects as $project): ?>
          <article class="project-row">
            <div class="project-row__thumb">
              <?php $thumbPath = '../' . (string) ($project['image'] ?? ''); ?>
              <?php if (!empty($project['image']) && is_file(PORTFOLIO_ROOT . '/' . $project['image'])): ?>
                <img src="<?php echo admin_e($thumbPath); ?>" alt="" loading="lazy">
              <?php else: ?>
                <span>No image</span>
              <?php endif; ?>
            </div>
            <div class="project-row__body">
              <div class="project-row__title">
                <strong><?php echo admin_e($project['name'] ?? 'Untitled'); ?></strong>
                <span class="pill"><?php echo admin_e($project['category_label'] ?? $project['category'] ?? ''); ?></span>
                <?php if (!empty($project['concept'])): ?><span class="pill pill--muted">Concept</span><?php endif; ?>
              </div>
              <p><?php echo admin_e(mb_strimwidth((string) ($project['description'] ?? ''), 0, 150, '…')); ?></p>
              <small><?php echo admin_e($project['year'] ?? ''); ?> · <?php echo admin_e(implode(', ', (array) ($project['tech'] ?? array()))); ?></small>
            </div>
            <div class="project-row__actions">
              <a class="btn btn--small btn--ghost" href="index.php?edit=<?php echo admin_e(urlencode((string) ($project['id'] ?? ''))); ?>#project-form">Edit</a>
              <form method="post" onsubmit="return confirm('Delete this project? This cannot be undone.');">
                <input type="hidden" name="action" value="delete_project">
                <input type="hidden" name="csrf" value="<?php echo admin_e(admin_csrf()); ?>">
                <input type="hidden" name="delete_id" value="<?php echo admin_e((string) ($project['id'] ?? '')); ?>">
                <button class="btn btn--small btn--danger" type="submit">Delete</button>
              </form>
            </div>
          </article>
        <?php endforeach; ?>
        <?php if (empty($projects)): ?>
          <p class="muted">No projects yet. Add your first one below.</p>
        <?php endif; ?>
      </div>

      <form method="post" enctype="multipart/form-data" class="admin-form project-form" id="project-form">
        <input type="hidden" name="action" value="save_project">
        <input type="hidden" name="csrf" value="<?php echo admin_e(admin_csrf()); ?>">
        <input type="hidden" name="original_id" value="<?php echo admin_e($editProject['id'] ?? ''); ?>">

        <h2><?php echo $editProject ? 'Edit project' : 'Add a project'; ?></h2>

        <div class="form-grid">
          <label class="field"><span>Project name</span><input type="text" name="project_name" value="<?php echo admin_e($editProject['name'] ?? ''); ?>" maxlength="120" required></label>
          <label class="field"><span>Slug / ID</span><input type="text" name="project_id" value="<?php echo admin_e($editProject['id'] ?? ''); ?>" maxlength="80" placeholder="my-project"></label>
          <label class="field"><span>Category slug</span><input type="text" name="project_category" value="<?php echo admin_e($editProject['category'] ?? 'web-design'); ?>" maxlength="50"></label>
          <label class="field"><span>Category label</span><input type="text" name="project_category_label" value="<?php echo admin_e($editProject['category_label'] ?? 'Web Design'); ?>" maxlength="60"></label>
          <label class="field"><span>Category detail</span><input type="text" name="project_category_detail" value="<?php echo admin_e($editProject['category_detail'] ?? ''); ?>" maxlength="120" placeholder="Brand · Web · WordPress"></label>
          <label class="field"><span>Year</span><input type="text" name="project_year" value="<?php echo admin_e($editProject['year'] ?? date('Y')); ?>" maxlength="10"></label>
          <label class="field field--wide"><span>Description</span><textarea name="project_description" rows="4" maxlength="600"><?php echo admin_e($editProject['description'] ?? ''); ?></textarea></label>
          <label class="field field--wide"><span>Technologies (comma separated)</span><input type="text" name="project_tech" value="<?php echo admin_e(implode(', ', (array) ($editProject['tech'] ?? array()))); ?>" placeholder="WordPress, PHP, CSS3"></label>
          <label class="field"><span>Live demo URL</span><input type="text" name="project_demo_url" value="<?php echo admin_e($editProject['demo_url'] ?? ''); ?>" placeholder="https://… or demo.html?id=…"></label>
          <label class="field"><span>Source code URL</span><input type="text" name="project_source_url" value="<?php echo admin_e($editProject['source_url'] ?? ''); ?>" placeholder="https://github.com/…"></label>
          <label class="field field--wide"><span>Existing image path</span><input type="text" name="project_image" value="<?php echo admin_e($editProject['image'] ?? ''); ?>" placeholder="assets/images/projects/project-name.jpg"></label>
          <label class="field field--wide"><span>Upload new thumbnail</span><input type="file" name="project_image_file" accept="image/jpeg,image/png,image/webp"></label>
        </div>

        <div class="check-row">
          <label><input type="checkbox" name="project_concept" value="1" <?php checked((bool) ($editProject['concept'] ?? true)); ?>> Concept project</label>
          <label><input type="checkbox" name="project_featured" value="1" <?php checked((bool) ($editProject['featured'] ?? true)); ?>> Featured</label>
        </div>

        <div class="form-actions">
          <button class="btn" type="submit"><?php echo $editProject ? 'Update project' : 'Add project'; ?></button>
          <?php if ($editProject): ?><a class="btn btn--ghost" href="index.php#projects">Cancel edit</a><?php endif; ?>
        </div>
      </form>
    </section>

    <footer class="admin-footer">
      <p>IMRAN.DEV portfolio admin · Profile and project data are stored locally as JSON.</p>
      <p>SMTP configured: <strong><?php echo portfolio_env('SMTP_HOST', '') !== '' ? 'yes' : 'no'; ?></strong> · Dev mail mode: <strong><?php echo portfolio_env_bool(portfolio_env('CONTACT_DEV_MODE', 'false')) ? 'on' : 'off'; ?></strong></p>
    </footer>
  </main>
<?php endif; ?>

<?php
/**
 * Tiny checked() helper for the admin template.
 *
 * @param bool $condition Condition.
 * @return void
 */
function checked(bool $condition): void
{
    echo $condition ? ' checked' : '';
}
?>
</body>
</html>
