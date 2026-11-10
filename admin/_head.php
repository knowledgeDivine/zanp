<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();
$flash = flash();
$page = basename($_SERVER['SCRIPT_NAME']);
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin | <?= e(setting('site_name', 'ZANP')) ?></title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap">
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head><body class="admin">
<header class="nav"><a class="logo" href="<?= url('admin/') ?>"><?= e(setting('site_name', 'ZANP')) ?> admin</a>
<nav class="links" aria-label="Admin">
<?php foreach (['index.php' => 'Overview', 'products.php' => 'Products', 'orders.php' => 'Orders', 'messages.php' => 'Messages', 'settings.php' => 'Settings'] as $f => $l): ?>
<a href="<?= url('admin/' . $f) ?>" <?= $page === $f ? 'aria-current="page"' : '' ?>><?= $l ?></a>
<?php endforeach; ?>
<a href="<?= url() ?>">View site</a><a href="<?= url('logout.php') ?>">Log out</a></nav></header>
<?php if ($flash): ?><div class="flash <?= e($flash['type']) ?>" role="status"><?= e($flash['msg']) ?></div><?php endif; ?>
<main class="wrap">
