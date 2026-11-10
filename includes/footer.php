</main>
<footer class="foot">
  <div class="blk"><h4 class="mono">[ contact ]</h4>
    <p class="mono">a:</p><a href="<?= url('contact.php') ?>">Send us a message</a>
    <p class="mono">b:</p><a href="<?= url('account.php') ?>">Your account and orders</a></div>
  <div class="blk"><h4 class="mono">[ menu ]</h4>
    <a href="<?= url('shop.php') ?>">Shop</a><a href="<?= url('index.php#story') ?>">Specs</a><a href="<?= url('cart.php') ?>">Bag</a><a href="<?= url('login.php') ?>">Log in</a></div>
  <form class="blk newsletter" data-newsletter>
    <h4 class="mono">[ newsletter ]</h4>
    <label for="nl">Get news of new drops</label>
    <div class="nlrow"><input id="nl" name="email" type="email" placeholder="you@email.com" required><?= csrf_field() ?><button class="btn" type="submit">Subscribe</button></div>
    <p class="nlmsg mono" aria-live="polite"></p>
  </form>
  <div class="base mono"><span><?= e(setting('site_name', 'ZANP')) ?> © <?= date('Y') ?></span><span>3D model: “Materials Variants Shoe”, © 2021 Shopify, CC BY 4.0. See CREDITS.md.</span></div>
  <div class="mark" aria-hidden="true"><?= e(setting('site_name', 'ZANP')) ?></div>
</footer>
<script>window.ZANP_BASE=<?= json_encode(BASE_URL) ?>;window.ZANP_CSRF=<?= json_encode(csrf_token()) ?>;</script>
<?php if (!empty($three)): ?>
<script src="<?= url('assets/vendor/three.min.js') ?>"></script>
<script src="<?= url('assets/vendor/GLTFLoader.js') ?>"></script>
<script src="<?= url('assets/vendor/OrbitControls.js') ?>"></script>
<script src="<?= url('assets/vendor/RoomEnvironment.js') ?>"></script>
<script src="<?= url('assets/js/zanp3d.js') ?>"></script>
<?php endif; ?>
<script src="<?= url('assets/js/main.js') ?>"></script>
</body>
</html>
