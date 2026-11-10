<?php
require __DIR__ . '/includes/bootstrap.php';
$id = (int)($_GET['id'] ?? 0); $me = current_user();
$s = $pdo->prepare('SELECT * FROM orders WHERE id=?'); $s->execute([$id]); $o = $s->fetch();
$allowed = $o && (($_SESSION['last_order'] ?? 0) === $id || ($me && ($me['role'] === 'admin' || (int)$o['user_id'] === (int)$me['id'])));
if (!$allowed) { http_response_code(404); redirect('shop.php'); }
$i = $pdo->prepare('SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?'); $i->execute([$id]);
$title = 'Order #' . $id;
include __DIR__ . '/includes/header.php';
?>
<section class="wrap narrow">
  <h1 class="page-h">Thank you, <?= e($o['customer_name']) ?></h1>
  <p>Order <strong>#<?= $id ?></strong> is confirmed. We’ll deliver to <?= e($o['address']) ?>.</p>
  <ul class="lines">
    <?php foreach ($i->fetchAll() as $it): ?><li><span><?= e($it['name']) ?> · size <?= (int)$it['size'] ?> × <?= (int)$it['qty'] ?></span><strong><?= money($it['price'] * $it['qty']) ?></strong></li><?php endforeach; ?>
  </ul>
  <div class="total"><span>Total</span><strong><?= money($o['total']) ?></strong></div>
  <a class="btn" href="<?= url('shop.php') ?>">Keep shopping</a>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
