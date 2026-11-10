<?php
declare(strict_types=1);
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();
require __DIR__ . '/../config/db.php';

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function money($n): string { return '₦' . number_format((float)$n); }
function url(string $p = ''): string { return BASE_URL . ltrim($p, '/'); }
function redirect(string $p): never { header('Location: ' . (str_starts_with($p, 'http') || str_starts_with($p, '/') ? $p : url($p))); exit; }

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }
function csrf_ok(): bool {
    $t = $_POST['csrf'] ?? '';
    return is_string($t) && hash_equals($_SESSION['csrf'] ?? '', $t);
}
function flash(?string $msg = null, string $type = 'ok'): ?array {
    if ($msg !== null) { $_SESSION['flash'] = ['msg' => $msg, 'type' => $type]; return null; }
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}
function current_user(): ?array {
    global $pdo;
    if (empty($_SESSION['uid'])) return null;
    static $u = false;
    if ($u === false) {
        $s = $pdo->prepare('SELECT id,name,email,role FROM users WHERE id=?');
        $s->execute([$_SESSION['uid']]);
        $u = $s->fetch() ?: null;
    }
    return $u;
}
function require_login(): array { $u = current_user(); if (!$u) { flash('Please log in first.', 'err'); redirect('login.php'); } return $u; }
function require_admin(): array { $u = current_user(); if (!$u || $u['role'] !== 'admin') { http_response_code(403); redirect('login.php'); } return $u; }

function setting(string $k, string $d = ''): string {
    global $pdo;
    static $all = null;
    if ($all === null) { $all = $pdo->query('SELECT k,v FROM settings')->fetchAll(PDO::FETCH_KEY_PAIR); }
    return $all[$k] ?? $d;
}

// Cart: session array keyed "productId-size" => qty
function cart(): array { return $_SESSION['cart'] ?? []; }
function cart_count(): int { return array_sum(cart()); }
function cart_lines(): array {
    global $pdo; $lines = []; $total = 0.0;
    foreach (cart() as $key => $qty) {
        [$pid, $size] = array_map('intval', explode('-', $key));
        $s = $pdo->prepare('SELECT * FROM products WHERE id=?'); $s->execute([$pid]);
        if ($p = $s->fetch()) { $sub = $p['price'] * $qty; $total += $sub; $lines[] = ['key' => $key, 'p' => $p, 'size' => $size, 'qty' => $qty, 'sub' => $sub]; }
    }
    return [$lines, $total];
}
function slugify(string $s): string { return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($s)), '-') ?: 'item'; }
function json_out(array $d, int $code = 200): never { http_response_code($code); header('Content-Type: application/json'); echo json_encode($d); exit; }
