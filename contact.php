<?php
require __DIR__ . '/includes/bootstrap.php';
$err = []; $v = ['name' => '', 'email' => '', 'message' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) $err[] = 'Session expired. Reload the page and try again.';
    foreach ($v as $k => $_) $v[$k] = trim((string)($_POST[$k] ?? ''));
    if ($v['name'] === '' || strlen($v['name']) > 100) $err[] = 'Enter your name.';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $err[] = 'Enter a valid email address.';
    if (strlen($v['message']) < 10 || strlen($v['message']) > 3000) $err[] = 'Write a message of at least 10 characters.';
    if (!$err) {
        $pdo->prepare('INSERT INTO messages (name,email,message) VALUES (?,?,?)')->execute([$v['name'], $v['email'], $v['message']]);
        flash('Thanks. We’ve got your message and will reply by email.'); redirect('contact.php');
    }
}
$title = 'Contact';
include __DIR__ . '/includes/header.php';
?>
<section class="wrap narrow">
  <h1 class="page-h">Contact</h1>
  <p>Questions about sizing, orders or a drop? Write to us here.</p>
  <?php foreach ($err as $m): ?><p class="flash err inline"><?= e($m) ?></p><?php endforeach; ?>
  <form method="post" class="form">
    <?= csrf_field() ?>
    <label>Name<input name="name" value="<?= e($v['name']) ?>" required maxlength="100"></label>
    <label>Email<input name="email" type="email" value="<?= e($v['email']) ?>" required></label>
    <label>Message<textarea name="message" rows="5" required minlength="10" maxlength="3000"><?= e($v['message']) ?></textarea></label>
    <button class="btn big" type="submit">Send message</button>
  </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
