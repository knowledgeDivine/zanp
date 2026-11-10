<?php require __DIR__ . '/_head.php';
$n = fn($sql) => (int)$pdo->query($sql)->fetchColumn();
$rev = (float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status<>'cancelled'")->fetchColumn();
$recent = $pdo->query('SELECT * FROM orders ORDER BY id DESC LIMIT 5')->fetchAll();
?>
<h1 class="page-h">Overview</h1>
<div class="stats">
  <div><span><?= $n('SELECT COUNT(*) FROM orders') ?></span>Orders</div>
  <div><span><?= money($rev) ?></span>Revenue</div>
  <div><span><?= $n('SELECT COUNT(*) FROM products') ?></span>Products</div>
  <div><span><?= $n('SELECT COUNT(*) FROM messages') ?></span>Messages</div>
  <div><span><?= $n('SELECT COUNT(*) FROM subscribers') ?></span>Subscribers</div>
  <div><span><?= $n('SELECT COUNT(*) FROM users') ?></span>Users</div>
</div>
<h2>Recent orders</h2>
<table><tr><th>#</th><th>Customer</th><th>Total</th><th>Status</th></tr>
<?php foreach ($recent as $r): ?><tr><td><?= (int)$r['id'] ?></td><td><?= e($r['customer_name']) ?></td><td><?= money($r['total']) ?></td><td><?= e($r['status']) ?></td></tr><?php endforeach; ?></table>
<?php require __DIR__ . '/_foot.php'; ?>
