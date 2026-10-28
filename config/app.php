<?php
ob_start();
// config/app.php

define('APP_NAME', 'Proposta Services');
define('APP_URL', 'http://localhost/proposta-services/public');
define('APP_VERSION', '1.0.0');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>false,'httponly'=>true,'samesite'=>'Strict']);
    session_start();
}

require_once __DIR__ . '/database.php';

spl_autoload_register(function($class) {
    $paths = [
        __DIR__ . '/../app/models/'      . $class . '.php',
        __DIR__ . '/../app/controllers/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) { require_once $path; return; }
    }
});

function auth(): ?array { return $_SESSION['user'] ?? null; }

function requireUser(): void {
    ob_end_clean(); ob_start();
    $user = auth();
    if (!$user) { header('Location: '.APP_URL.'/login.php'); exit; }
    if (($user['role'] ?? 'user') === 'admin') { header('Location: '.APP_URL.'/admin/index.php'); exit; }
    if (($user['status'] ?? 'ativo') === 'bloqueado') { session_destroy(); header('Location: '.APP_URL.'/login.php'); exit; }
}

function requireAdmin(): void {
    ob_end_clean(); ob_start();
    $user = auth();
    if (!$user) { header('Location: '.APP_URL.'/login.php'); exit; }
    if (($user['role'] ?? 'user') !== 'admin') { header('Location: '.APP_URL.'/dashboard.php'); exit; }
}

function requireAuth(): void {
    ob_end_clean(); ob_start();
    if (!auth()) { header('Location: '.APP_URL.'/login.php'); exit; }
}

function redirect(string $path): void {
    ob_end_clean();
    header('Location: ' . APP_URL . '/' . ltrim($path, '/'));
    exit;
}

function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) { $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f; }
    return null;
}

function formatCurrency(float $value): string { return 'R$ ' . number_format($value, 2, ',', '.'); }
function formatDate(?string $date): string { if (!$date) return '-'; return date('d/m/Y', strtotime($date)); }
function sanitize(string $value): string { return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8'); }
function generateToken(int $length = 32): string { return bin2hex(random_bytes($length)); }

function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = generateToken(16); }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica CSRF. Em caso de falha, redireciona para referer
 * apenas se for do mesmo domínio (evita open redirect).
 * Token é regenerado após falha.
 */
function verifyCsrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        unset($_SESSION['csrf_token']); // força regeneração
        flash('error', 'Token de segurança inválido. Tente novamente.');
        $appHost = parse_url(APP_URL, PHP_URL_HOST);
        $ref     = $_SERVER['HTTP_REFERER'] ?? '';
        $refHost = $ref ? parse_url($ref, PHP_URL_HOST) : '';
        if ($ref && $appHost && $refHost === $appHost) {
            header('Location: ' . $ref);
        } else {
            header('Location: ' . APP_URL . '/dashboard.php');
        }
        exit;
    }
}

// ── Rate limiting para login (via sessão) ─────────────────────────────────
// Bloqueia por 15 min após 5 tentativas falhas (por e-mail + IP)

function loginAttemptFail(string $email): void {
    $key  = 'login_att_' . md5($email . ($_SERVER['REMOTE_ADDR'] ?? ''));
    $data = $_SESSION[$key] ?? ['count' => 0, 'first' => time()];
    if (time() - $data['first'] > 900) { $data = ['count' => 0, 'first' => time()]; }
    $data['count']++;
    $_SESSION[$key] = $data;
}

function loginAttemptSuccess(string $email): void {
    unset($_SESSION['login_att_' . md5($email . ($_SERVER['REMOTE_ADDR'] ?? ''))]);
}

function loginIsBlocked(string $email): bool {
    $key  = 'login_att_' . md5($email . ($_SERVER['REMOTE_ADDR'] ?? ''));
    $data = $_SESSION[$key] ?? null;
    if (!$data) return false;
    if (time() - $data['first'] > 900) { unset($_SESSION[$key]); return false; }
    return $data['count'] >= 5;
}

function loginBlockedMinutesLeft(string $email): int {
    $key  = 'login_att_' . md5($email . ($_SERVER['REMOTE_ADDR'] ?? ''));
    $data = $_SESSION[$key] ?? null;
    if (!$data) return 0;
    return (int) ceil(max(0, 900 - (time() - $data['first'])) / 60);
}
