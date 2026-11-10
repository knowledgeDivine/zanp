<?php require __DIR__ . '/_head.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok() && ($_POST['action'] ?? '') === 'delete') {
    try { $pdo->prepare('DELETE FROM products WHERE id=?')->execute([(int)$_POST['id']]); flash('Product deleted.'); }
    catch (PDOException $e) { flash('That product has orders, so it can’t be deleted. Set its stock to 0 instead.', 'err'); }
    redirect('admin/products.php');
}
$rows = $pdo->query('SELECT p.*, c.name AS cat FROM products p JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC')->fetchAll();
?>
<div class="sec-head"><h1 class="page-h">Products</h1><a class="btn" href="<?= url('admin/product_edit.php') ?>">Add product</a></div>
<table><tr><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Featured</th><th></th></tr>
<?php foreach ($rows as $r): ?><tr>
<td><?= e($r['name']) ?></td><td><?= e($r['cat']) ?></td><td><?= money($r['price']) ?></td><td><?= (int)$r['stock'] ?></td><td><?= $r['featured'] ? 'Yes' : 'No' ?></td>
<td class="act"><a href="<?= url('admin/product_edit.php?id=' . (int)$r['id']) ?>">Edit</a>
<form method="post" onsubmit="return confirm('Delete this product?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="link" type="submit">Delete</button></form></td></tr><?php endforeach; ?></table>
<?php require __DIR__ . '/_foot.php'; ?>
