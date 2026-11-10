<?php /** @var array $p */ ?>
<a class="card" href="<?= url('product.php?slug=' . urlencode($p['slug'])) ?>">
  <div class="thumb"><?php if (!empty($n)): ?><span class="no mono">[<?= str_pad((string)$n, 2, "0", STR_PAD_LEFT) ?>]</span><?php endif; ?>
    <?php if (!empty($p['image'])): ?>
      <img src="<?= url('uploads/' . e($p['image'])) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
    <?php else: ?>
      <img alt="<?= e($p['name']) ?>" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-thumb data-variant="<?= (int)$p['variant_index'] ?>" data-tint="<?= e($p['tint']) ?>"<?= !empty($p['model']) ? ' data-model="' . e(url('uploads/' . $p['model'])) . '"' : '' ?>>
    <?php endif; ?>
  </div>
  <div class="meta"><h3><?= e($p['name']) ?></h3><span><?= money($p['price']) ?></span></div>
  <p class="cat mono"><?= e($p['cat'] ?? '') ?></p>
</a>
