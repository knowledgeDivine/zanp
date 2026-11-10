<?php
require __DIR__ . '/includes/bootstrap.php';
$me = require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok()) {
    $n = trim((string)($_POST['name'] ?? ''));
    if ($n !== '' && strlen($n) <= 100) { $pdo->prepare('UPDATE users SET name=? WHERE id=?')->execute([$n, $me['id']]); flash('Name updated.'); }
    redirect('account.php');
}
$o = $pdo->prepare('SELECT * FROM orders WHERE user_id=? ORDER BY id DESC'); $o->execute([$me['id']]); $orders = $o->fetchAll();
$title = 'Account';
include __DIR__ . '/includes/header.php';
?>
<section class="wrap narrow">
  <h1 class="page-h">Account</h1>
  <form method="post" class="form">
    <?= csrf_field() ?>
    <label>Name<input name="name" value="<?= e($me['name']) ?>" required maxlength="100"></label>
    <p class="mute"><?= e($me['email']) ?></p>
    <button class="btn" type="submit">Save name</button>
  </form>
  <h2>Your orders</h2>
  <?php if (!$orders): ?><p class="empty">No orders yet.</p><?php else: ?>
  <ul class="lines"><?php foreach ($orders as $r): ?>
    <li><a href="<?= url('order.php?id=' . (int)$r['id']) ?>">Order #<?= (int)$r['id'] ?></a><span class="mute"><?= e(date('j M Y', strtotime($r['created_at']))) ?> · <?= e($r['status']) ?></span><strong><?= money($r['total']) ?></strong></li>
  <?php endforeach; ?></ul>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
