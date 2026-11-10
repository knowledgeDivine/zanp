<?php
require __DIR__ . '/includes/bootstrap.php';
$title = 'Your bag'; [$lines, $total] = cart_lines();
include __DIR__ . '/includes/header.php';
?>
<section class="wrap narrow">
  <h1 class="page-h">Your bag</h1>
  <?php if (!$lines): ?>
    <p class="empty">Your bag is empty.</p><a class="btn" href="<?= url('shop.php') ?>">Shop the Runner</a>
  <?php else: ?>
    <ul class="lines">
    <?php foreach ($lines as $l): ?>
      <li>
        <div><a href="<?= url('product.php?slug=' . urlencode($l['p']['slug'])) ?>"><strong><?= e($l['p']['name']) ?></strong></a><br><span class="mute">Size <?= (int)$l['size'] ?> · <?= money($l['p']['price']) ?></span></div>
        <form method="post" action="<?= url('api/cart.php') ?>" class="qty">
          <?= csrf_field() ?><input type="hidden" name="action" value="update"><input type="hidden" name="key" value="<?= e($l['key']) ?>">
          <label class="sr" for="q<?= e($l['key']) ?>">Quantity</label>
          <input id="q<?= e($l['key']) ?>" name="qty" type="number" min="0" max="10" value="<?= (int)$l['qty'] ?>">
          <button class="btn ghost" type="submit">Update</button>
        </form>
        <strong><?= money($l['sub']) ?></strong>
        <form method="post" action="<?= url('api/cart.php') ?>"><?= csrf_field() ?><input type="hidden" name="action" value="remove"><input type="hidden" name="key" value="<?= e($l['key']) ?>"><button class="link" type="submit">Remove</button></form>
      </li>
    <?php endforeach; ?>
    </ul>
    <div class="total"><span>Total</span><strong><?= money($total) ?></strong></div>
    <a class="btn big" href="<?= url('checkout.php') ?>">Go to checkout</a>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
