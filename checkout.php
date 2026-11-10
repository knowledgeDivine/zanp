<?php
require __DIR__ . '/includes/bootstrap.php';
[$lines, $total] = cart_lines();
if (!$lines) { flash('Your bag is empty.', 'err'); redirect('cart.php'); }
$me = current_user(); $err = []; $v = ['name' => $me['name'] ?? '', 'email' => $me['email'] ?? '', 'phone' => '', 'address' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) { $err[] = 'Session expired. Reload the page and try again.'; }
    foreach ($v as $k => $_) $v[$k] = trim((string)($_POST[$k] ?? ''));
    if ($v['name'] === '' || strlen($v['name']) > 100) $err[] = 'Enter your name.';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $err[] = 'Enter a valid email address.';
    if (!preg_match('/^[0-9+() -]{7,30}$/', $v['phone'])) $err[] = 'Enter a valid phone number.';
    if (strlen($v['address']) < 8 || strlen($v['address']) > 255) $err[] = 'Enter your full delivery address.';
    if (!$err) {
        try {
            $pdo->beginTransaction();
            $pdo->prepare('INSERT INTO orders (user_id, customer_name, email, phone, address, total, status) VALUES (?,?,?,?,?,?,?)')
                ->execute([$me['id'] ?? null, $v['name'], $v['email'], $v['phone'], $v['address'], $total, 'paid']);
            $oid = (int)$pdo->lastInsertId();
            foreach ($lines as $l) {
                $u = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');
                $u->execute([$l['qty'], $l['p']['id'], $l['qty']]);
                if ($u->rowCount() !== 1) throw new RuntimeException($l['p']['name'] . ' is no longer available in that quantity.');
                $pdo->prepare('INSERT INTO order_items (order_id, product_id, size, qty, price) VALUES (?,?,?,?,?)')
                    ->execute([$oid, $l['p']['id'], $l['size'], $l['qty'], $l['p']['price']]);
            }
            $pdo->commit();
            unset($_SESSION['cart']); $_SESSION['last_order'] = $oid;
            redirect('order.php?id=' . $oid);
        } catch (RuntimeException $ex) { $pdo->rollBack(); $err[] = $ex->getMessage(); }
    }
}
$title = 'Checkout';
include __DIR__ . '/includes/header.php';
?>
<section class="wrap narrow">
  <h1 class="page-h">Checkout</h1>
  <p class="notice">Demo store: no real payment is taken. Placing the order marks it as paid.</p>
  <?php foreach ($err as $m): ?><p class="flash err inline"><?= e($m) ?></p><?php endforeach; ?>
  <form method="post" class="form">
    <?= csrf_field() ?>
    <label>Full name<input name="name" value="<?= e($v['name']) ?>" required maxlength="100" autocomplete="name"></label>
    <label>Email<input name="email" type="email" value="<?= e($v['email']) ?>" required autocomplete="email"></label>
    <label>Phone<input name="phone" value="<?= e($v['phone']) ?>" required autocomplete="tel"></label>
    <label>Delivery address<textarea name="address" rows="3" required maxlength="255" autocomplete="street-address"><?= e($v['address']) ?></textarea></label>
    <div class="total"><span>Total</span><strong><?= money($total) ?></strong></div>
    <button class="btn big" type="submit">Place order</button>
  </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
