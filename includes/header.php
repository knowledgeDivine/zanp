<?php
require_once __DIR__ . '/bootstrap.php';
$title = isset($title) ? $title . ' | ' . setting('site_name', 'ZANP') : setting('site_name', 'ZANP');
$flash = flash(); $me = current_user();
$accent = preg_match('/^#[0-9a-fA-F]{6}$/', setting('accent', '#ff4d12')) ? setting('accent', '#ff4d12') : '#ff4d12';
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e(setting('tagline')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap">
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
<style>:root{--accent:<?= e($accent) ?>}</style>
</head>
<body class="<?= e($bodyClass ?? '') ?>">
<a class="skip" href="#main">Skip to content</a>
<header class="hud">
  <a class="logo" href="<?= url() ?>" aria-label="<?= e(setting('site_name', 'ZANP')) ?> home"><?= e(setting('site_name', 'ZANP')) ?></a>
  <div class="hud-mid">
    <div class="clock mono">LAGOS, <span id="clock">--:--:--</span></div>
    <nav class="links mono" aria-label="Main">
      <a href="<?= url('shop.php') ?>">Shop</a>
      <a href="<?= url('index.php#story') ?>">Specs</a>
      <a href="<?= url('contact.php') ?>">Contact</a>
      <?php if ($me): ?>
        <a href="<?= url('account.php') ?>">Account</a>
        <?php if ($me['role'] === 'admin'): ?><a href="<?= url('admin/') ?>">Admin</a><?php endif; ?>
        <a href="<?= url('logout.php') ?>">Log out</a>
      <?php else: ?><a href="<?= url('login.php') ?>">Log in</a><?php endif; ?>
    </nav>
  </div>
  <div class="ticker" id="ticker" aria-hidden="true"></div>
  <a class="hud-cta mono" href="<?= url('cart.php') ?>">Bag [<span id="cartCount"><?= cart_count() ?></span>]</a>
</header>
<?php if ($flash): ?><div class="flash <?= e($flash['type']) ?>" role="status"><?= e($flash['msg']) ?></div><?php endif; ?>
<main id="main">
