<?php
require __DIR__ . '/includes/bootstrap.php';
if (current_user()) redirect('account.php');
$err = []; $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    if (!csrf_ok()) $err[] = 'Session expired. Reload the page and try again.';
    elseif (($_SESSION['fails'] ?? 0) >= 5 && time() - ($_SESSION['fail_t'] ?? 0) < 300) $err[] = 'Too many attempts. Wait a few minutes and try again.';
    else {
        $s = $pdo->prepare('SELECT * FROM users WHERE email=?'); $s->execute([$email]); $u = $s->fetch();
        if ($u && password_verify((string)($_POST['password'] ?? ''), $u['password_hash'])) {
            session_regenerate_id(true); $_SESSION['uid'] = (int)$u['id']; unset($_SESSION['fails']);
            redirect($u['role'] === 'admin' ? 'admin/' : 'shop.php');
        }
        $_SESSION['fails'] = ($_SESSION['fails'] ?? 0) + 1; $_SESSION['fail_t'] = time();
        $err[] = 'Wrong email or password.';
    }
}
$title = 'Log in';
include __DIR__ . '/includes/header.php';
?>
<section class="wrap narrow">
  <h1 class="page-h">Log in</h1>
  <?php foreach ($err as $m): ?><p class="flash err inline"><?= e($m) ?></p><?php endforeach; ?>
  <form method="post" class="form">
    <?= csrf_field() ?>
    <label>Email<input name="email" type="email" value="<?= e($email) ?>" required autocomplete="email"></label>
    <label>Password<input name="password" type="password" required autocomplete="current-password"></label>
    <button class="btn big" type="submit">Log in</button>
  </form>
  <p>New here? <a href="<?= url('register.php') ?>">Create an account</a></p>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
