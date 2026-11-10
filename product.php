<?php
require __DIR__ . '/includes/bootstrap.php';
$s = $pdo->prepare('SELECT p.*, c.name AS cat FROM products p JOIN categories c ON c.id=p.category_id WHERE p.slug=?');
$s->execute([(string)($_GET['slug'] ?? '')]);
$p = $s->fetch();
if (!$p) { http_response_code(404); $title = 'Not found'; include __DIR__ . '/includes/header.php'; echo '<section class="wrap"><h1 class="page-h">Shoe not found</h1><a class="btn" href="' . url('shop.php') . '">Back to shop</a></section>'; include __DIR__ . '/includes/footer.php'; exit; }
$three = true; $title = $p['name']; $bodyClass = 'pdp';
$model = $p['model'] ? url('uploads/' . $p['model']) : '';
include __DIR__ . '/includes/header.php';
?>
<section class="pdp-grid">
  <div class="viewer-wrap">
    <div class="stage" id="viewer" data-variant="<?= (int)$p['variant_index'] ?>" data-tint="<?= e($p['tint']) ?>" data-model="<?= e($model) ?>" aria-label="Interactive 3D <?= e($p['name']) ?>. Drag to rotate, scroll to zoom." role="img"><p class="loading">Loading the 3D shoe…</p></div>
    <div class="views" role="group" aria-label="Camera view">
      <button class="chip" data-view="side">Side</button><button class="chip" data-view="front">Front</button><button class="chip" data-view="top">Top</button><button class="chip" data-view="back">Back</button>
    </div>
  </div>
  <div class="info">
    <p class="cat"><?= e($p['cat']) ?></p>
    <h1><?= e($p['name']) ?></h1>
    <p class="price"><?= money($p['price']) ?></p>
    <p><?= nl2br(e($p['description'])) ?></p>
    <?php if (!$p['model']): ?>
    <p class="lbl">Colourway</p>
    <div class="dots" role="group" aria-label="Colourway">
      <button class="dot" data-v="0" aria-label="Midnight" aria-pressed="<?= $p['variant_index'] == 0 ? 'true' : 'false' ?>"></button>
      <button class="dot" data-v="1" aria-label="Beach" aria-pressed="<?= $p['variant_index'] == 1 ? 'true' : 'false' ?>"></button>
      <button class="dot" data-v="2" aria-label="Street" aria-pressed="<?= $p['variant_index'] == 2 ? 'true' : 'false' ?>"></button>
    </div>
    <?php endif; ?>
    <?php if ($p['stock'] > 0): ?>
    <form id="addForm" method="post" action="<?= url('api/cart.php') ?>">
      <?= csrf_field() ?><input type="hidden" name="action" value="add"><input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
      <p class="lbl">Size (EU)</p>
      <div class="sizes" role="radiogroup" aria-label="Size">
        <?php foreach ([40,41,42,43,44,45] as $i => $sz): ?>
          <label class="size"><input type="radio" name="size" value="<?= $sz ?>" <?= $sz === 42 ? 'checked' : '' ?>><span><?= $sz ?></span></label>
        <?php endforeach; ?>
      </div>
      <button class="btn big" type="submit">Add to bag</button>
      <p class="formmsg" aria-live="polite"></p>
    </form>
    <?php else: ?><p class="empty">Sold out.</p><?php endif; ?>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
