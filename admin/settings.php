<?php require __DIR__ . '/_head.php';
$err = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)$_POST['site_name']); $tag = trim((string)$_POST['tagline']); $acc = (string)$_POST['accent'];
    if (!csrf_ok()) $err[] = 'Session expired. Reload and try again.';
    if ($name === '' || strlen($name) > 40) $err[] = 'Enter a site name (up to 40 characters).';
    if (strlen($tag) > 160) $err[] = 'Keep the tagline under 160 characters.';
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $acc)) $err[] = 'Pick a valid colour.';
    if (!$err) {
        $u = $pdo->prepare('REPLACE INTO settings (k,v) VALUES (?,?)');
        foreach (['site_name' => $name, 'tagline' => $tag, 'accent' => $acc] as $k => $v) $u->execute([$k, $v]);
        flash('Settings saved.'); redirect('admin/settings.php');
    }
}
?>
<h1 class="page-h">Settings</h1>
<?php foreach ($err as $m): ?><p class="flash err inline"><?= e($m) ?></p><?php endforeach; ?>
<form method="post" class="form">
<?= csrf_field() ?>
<label>Site name<input name="site_name" value="<?= e(setting('site_name')) ?>" required maxlength="40"></label>
<label>Tagline<input name="tagline" value="<?= e(setting('tagline')) ?>" maxlength="160"></label>
<label>Accent colour<input name="accent" type="color" value="<?= e(setting('accent', '#ff4d12')) ?>"></label>
<button class="btn big" type="submit">Save settings</button>
</form>
<?php require __DIR__ . '/_foot.php'; ?>
