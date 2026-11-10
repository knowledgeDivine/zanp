<?php
require __DIR__ . '/includes/bootstrap.php';
$three = true; $title = 'Shop';
$cats = $pdo->query('SELECT * FROM categories ORDER BY id')->fetchAll();
$q = trim((string)($_GET['q'] ?? '')); $cat = (string)($_GET['cat'] ?? '');
include __DIR__ . '/includes/header.php';
?>
<section class="wrap">
  <h1 class="page-h">Shop</h1>
  <div class="filters">
    <input id="q" type="search" placeholder="Search shoes" value="<?= e($q) ?>" aria-label="Search shoes">
    <div class="chips" role="group" aria-label="Category">
      <button class="chip" data-cat="" aria-pressed="<?= $cat === '' ? 'true' : 'false' ?>">All</button>
      <?php foreach ($cats as $c): ?><button class="chip" data-cat="<?= e($c['slug']) ?>" aria-pressed="<?= $cat === $c['slug'] ? 'true' : 'false' ?>"><?= e($c['name']) ?></button><?php endforeach; ?>
    </div>
  </div>
  <div class="grid" id="grid" aria-live="polite">
    <?php $_GET['q'] = $q; $_GET['cat'] = $cat; $html_only = true; include __DIR__ . '/api/products.php'; ?>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
