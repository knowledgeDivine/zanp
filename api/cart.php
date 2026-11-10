<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$wantsJson = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_ok()) {
    if ($wantsJson) json_out(['ok' => false, 'error' => 'Session expired. Reload the page.'], 400);
    redirect('cart.php');
}
$action = (string)($_POST['action'] ?? '');
$cart = cart();
if ($action === 'add') {
    $pid = (int)($_POST['product_id'] ?? 0); $size = (int)($_POST['size'] ?? 0);
    $s = $pdo->prepare('SELECT stock FROM products WHERE id=?'); $s->execute([$pid]); $p = $s->fetch();
    if (!$p || $size < 38 || $size > 48) {
        if ($wantsJson) json_out(['ok' => false, 'error' => 'Choose a size.'], 422);
        flash('Choose a size.', 'err'); redirect('cart.php');
    }
    $k = "$pid-$size"; $cart[$k] = min(($cart[$k] ?? 0) + 1, max(1, (int)$p['stock']));
} elseif ($action === 'update') {
    $k = (string)($_POST['key'] ?? ''); $q = (int)($_POST['qty'] ?? 0);
    if (isset($cart[$k])) { if ($q <= 0) unset($cart[$k]); else $cart[$k] = min($q, 10); }
} elseif ($action === 'remove') {
    unset($cart[(string)($_POST['key'] ?? '')]);
}
$_SESSION['cart'] = $cart;
if ($wantsJson) json_out(['ok' => true, 'count' => array_sum($cart)]);
redirect('cart.php');
