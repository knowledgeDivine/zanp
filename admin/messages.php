<?php require __DIR__ . '/_head.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok()) { $pdo->prepare('DELETE FROM messages WHERE id=?')->execute([(int)$_POST['id']]); flash('Message deleted.'); redirect('admin/messages.php'); }
$rows = $pdo->query('SELECT * FROM messages ORDER BY id DESC')->fetchAll();
?>
<h1 class="page-h">Messages</h1>
<?php if (!$rows): ?><p class="empty">No messages yet.</p><?php endif; ?>
<?php foreach ($rows as $r): ?><article class="msg"><strong><?= e($r['name']) ?></strong> <a href="mailto:<?= e($r['email']) ?>"><?= e($r['email']) ?></a> <span class="mute"><?= e($r['created_at']) ?></span>
<p><?= nl2br(e($r['message'])) ?></p>
<form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="link" type="submit">Delete</button></form></article><?php endforeach; ?>
<?php require __DIR__ . '/_foot.php'; ?>
