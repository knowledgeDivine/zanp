<?php require __DIR__ . '/_head.php';
$statuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok() && in_array($_POST['status'] ?? '', $statuses, true)) {
    $pdo->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$_POST['status'], (int)$_POST['id']]); flash('Order updated.'); redirect('admin/orders.php');
}
$rows = $pdo->query('SELECT * FROM orders ORDER BY id DESC')->fetchAll();
?>
<h1 class="page-h">Orders</h1>
<table><tr><th>#</th><th>Customer</th><th>Contact</th><th>Total</th><th>Date</th><th>Status</th></tr>
<?php foreach ($rows as $r): ?><tr>
<td><a href="<?= url('order.php?id=' . (int)$r['id']) ?>"><?= (int)$r['id'] ?></a></td><td><?= e($r['customer_name']) ?></td><td><?= e($r['email']) ?><br><?= e($r['phone']) ?></td><td><?= money($r['total']) ?></td><td><?= e(date('j M Y', strtotime($r['created_at']))) ?></td>
<td><form method="post" class="inl"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
<select name="status" aria-label="Status"><?php foreach ($statuses as $s): ?><option <?= $s === $r['status'] ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select><button class="link" type="submit">Save</button></form></td></tr><?php endforeach; ?></table>
<?php require __DIR__ . '/_foot.php'; ?>
