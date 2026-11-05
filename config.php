<?php
/**
 * Shared config: same DB + same session domain as the Personal site and Marketplace,
 * so one login (users table) works across every artarenastudio.com subdomain.
 */

$cookieDomain = '.artarenastudio.com';
session_set_cookie_params([
    'lifetime' => 60 * 60 * 24 * 14,
    'path'     => '/',
    'domain'   => $cookieDomain,
    'secure'   => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_name('ARTARENA_SESSION');
session_start();

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'u609460025_artarena_core');
define('DB_USER', 'u609460025_artarena_adm');
define('DB_PASS', 'Snd3sh#ArtVault92q');

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }
    return $pdo;
}

function e(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

function is_admin(): bool {
    return is_logged_in() && !empty($_SESSION['is_admin']);
}

function require_admin(): void {
    if (!is_admin()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function current_user(): ?array {
    if (!is_logged_in()) return null;
    static $u = null;
    if ($u === null) {
        $stmt = db()->prepare('SELECT id, name, email, role, is_admin FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $u = $stmt->fetch() ?: null;
    }
    return $u;
}

/** Singleton foundation settings row (id=1): about/mission text, donation + Razorpay config. */
function fsettings(): array {
    static $s = null;
    if ($s === null) {
        $s = db()->query("SELECT * FROM foundation_settings WHERE id = 1")->fetch() ?: [];
    }
    return $s;
}

/**
 * Handles an optional file upload for a form field (images, or images+video when
 * $allowedExt is widened). Returns the stored relative path, or keeps $existing
 * unchanged if no new valid file was uploaded (so edits don't wipe the image).
 */
function handle_upload(string $field, ?string $existing, string $subdir = 'uploads/images', array $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif']): ?string {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return $existing;
    }
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return $existing;
    }
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        return $existing;
    }
    $dir = __DIR__ . '/' . $subdir;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $name = 'img_' . bin2hex(random_bytes(6)) . '.' . $ext;
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $dir . '/' . $name)) {
        return $existing;
    }
    return $subdir . '/' . $name;
}

/** Renders a stored image/media path correctly whether it's an old full URL or a new relative upload path. */
function img_url(?string $path): string {
    if (!$path) return '';
    if (preg_match('#^https?://#i', $path)) return $path;
    return '/' . ltrim($path, '/');
}

define('ASSET_VERSION', '1');
