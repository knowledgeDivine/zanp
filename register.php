<?php
require __DIR__ . '/includes/bootstrap.php';
if (current_user()) redirect('account.php');
$err = []; $v = ['name' => '', 'email' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) $err[] = 'Session expired. Reload the page and try again.';
    $v['name'] = trim((string)($_POST['name'] ?? '')); $v['email'] = strtolower(trim((string)($_POST['email'] ?? '')));
    $pw = (string)($_POST['password'] ?? '');
    if ($v['name'] === '' || strlen($v['name']) > 100) $err[] = 'Enter your name.';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL) || strlen($v['email']) > 150) $err[] = 'Enter a valid email address.';
    if (strlen($pw) < 8) $err[] = 'Use a password with at least 8 characters.';
    if (!$err) {
        $c = $pdo->prepare('SELECT id FROM users WHERE email=?'); $c->execute([$v['email']]);
        if ($c->fetch()) $err[] = 'That email already has an account. Log in instead.';
        else {
            $pdo->prepare('INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?,?)')->execute([$v['name'], $v['email'], password_hash($pw, PASSWORD_DEFAULT), 'customer']);
            session_regenerate_id(true); $_SESSION['uid'] = (int)$pdo->lastInsertId();
            flash('Welcome to ZANP.'); redirect('shop.php');
        }
    }
}
$title = 'Create account';
include __DIR__ . '/includes/header.php';
?>
<section class="wrap narrow">
  <h1 class="page-h">Create account</h1>
  <?php foreach ($err as $m): ?><p class="flash err inline"><?= e($m) ?></p><?php endforeach; ?>
  <form method="post" class="form">
    <?= csrf_field() ?>
    <label>Name<input name="name" value="<?= e($v['name']) ?>" required maxlength="100" autocomplete="name"></label>
    <label>Email<input name="email" type="email" value="<?= e($v['email']) ?>" required autocomplete="email"></label>
    <label>Password (8 or more characters)<input name="password" type="password" required minlength="8" autocomplete="new-password"></label>
    <button class="btn big" type="submit">Create account</button>
  </form>
  <p>Already have an account? <a href="<?= url('login.php') ?>">Log in</a></p>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
