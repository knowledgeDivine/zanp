<?php
require __DIR__ . '/includes/bootstrap.php';
$three = true; $bodyClass = 'home';
$feat = $pdo->query('SELECT p.*, c.name AS cat FROM products p JOIN categories c ON c.id=p.category_id WHERE p.featured=1 ORDER BY p.id LIMIT 6')->fetchAll();
$minPrice = (int)$pdo->query('SELECT COALESCE(MIN(price),0) FROM products')->fetchColumn();
$stock = (int)$pdo->query('SELECT COALESCE(SUM(stock),0) FROM products')->fetchColumn();
$specs = [
  ['label' => 'Price', 'tag' => 'PRICE', 'value' => $minPrice, 'unit' => '₦ from'],
  ['label' => 'Colourways', 'tag' => 'COLOURWAYS', 'value' => 3, 'unit' => 'on the Runner'],
  ['label' => 'Sizes', 'tag' => 'SIZES', 'value' => 6, 'unit' => 'EU 40 to 45'],
  ['label' => 'In stock', 'tag' => 'IN STOCK', 'value' => $stock, 'unit' => 'pairs'],
];
include __DIR__ . '/includes/header.php';
?>
<div id="loader" role="status" aria-label="Loading"><span class="mono"><?= e(setting('site_name', 'ZANP')) ?></span><b id="ldn">000</b><span class="mono">Loading the 3D shoe</span></div>

<section class="hero">
  <div class="dial" aria-hidden="true"></div>
  <div class="stage" id="hero3d" aria-label="Interactive 3D ZANP Runner. Drag to rotate." role="img"><p class="loading mono">Loading</p></div>
  <h1 class="lbl-l">Runner 01</h1>
  <div class="hero-r mono">
    <span id="cwName">MIDNIGHT</span>
    <div class="dots" role="group" aria-label="Colour"><button class="dot" data-v="0" data-n="MIDNIGHT" aria-label="Midnight" aria-pressed="true"></button><button class="dot" data-v="1" data-n="BEACH" aria-label="Beach" aria-pressed="false"></button><button class="dot" data-v="2" data-n="STREET" aria-label="Street" aria-pressed="false"></button></div>
  </div>
  <div class="hero-b"><p><?= e(setting('tagline')) ?></p><a class="btn mono" href="<?= url('shop.php') ?>">Shop the Runner</a></div>
</section>

<section class="scrolly" id="story" data-specs="<?= e(json_encode($specs)) ?>">
  <div class="sticky">
    <div class="dial" aria-hidden="true"></div>
    <div class="bignum" aria-live="polite"><span id="num">0</span><small id="unit" class="mono"></small></div>
    <div class="stage" id="story3d" aria-hidden="true"></div>
    <h2 class="lbl-l" id="lblL"></h2>
    <span class="idx" id="idx">001</span>
    <div class="tag mono"><span>SPEC: <b id="tagN">01</b></span><b id="tagT"></b></div>
  </div>
</section>

<section class="feat">
  <div class="feat-head"><p class="mono">// FEATURES</p><h2>Built for the long walk home</h2></div>
  <ol class="feat-list">
    <li><span class="mono">A.</span><div><h3>Knit upper</h3><p>One piece of stretch knit, so nothing rubs at the toes.</p></div></li>
    <li><span class="mono">B.</span><div><h3>Foam sole</h3><p>A wide, soft midsole that keeps its shape from the first walk to the last.</p></div></li>
    <li><span class="mono">C.</span><div><h3>Flat grip</h3><p>A thin rubber outsole with a flat profile for wet pavement and dry floors.</p></div></li>
    <li><span class="mono">D.</span><div><h3>Three colourways</h3><p>Midnight, Beach and Street. One shape, so you can choose on colour alone.</p></div></li>
  </ol>
</section>

<section class="wrap">
  <div class="sec-head"><h2>Featured</h2><a class="mono" href="<?= url('shop.php') ?>">[ see all ]</a></div>
  <div class="grid">
    <?php foreach ($feat as $i => $p) { $n = $i + 1; include __DIR__ . '/includes/card.php'; } ?>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
