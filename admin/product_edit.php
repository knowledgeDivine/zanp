<?php require __DIR__ . '/_head.php';
$id = (int)($_GET['id'] ?? 0);
$cats = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$p = ['name' => '', 'description' => '', 'price' => '', 'category_id' => $cats[0]['id'] ?? 1, 'variant_index' => 0, 'tint' => '#ffffff', 'stock' => 20, 'featured' => 0, 'image' => null, 'model' => null];
if ($id) { $s = $pdo->prepare('SELECT * FROM products WHERE id=?'); $s->execute([$id]); $p = $s->fetch() ?: redirect('admin/products.php'); }
$err = [];

function save_upload(string $field, array $exts, array $mimes, int $max, ?string $magic = null): ?string {
    if (empty($_FILES[$field]['name']) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Upload failed. Check the file size limit in php.ini.');
    if ($f['size'] > $max) throw new RuntimeException('File is too large (max ' . round($max / 1048576) . ' MB).');
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $exts, true)) throw new RuntimeException('File type not allowed: .' . $ext);
    if ($magic !== null) { if (file_get_contents($f['tmp_name'], false, null, 0, 4) !== $magic) throw new RuntimeException('That does not look like a valid .glb file.'); }
    else { $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']); if (!in_array($mime, $mimes, true)) throw new RuntimeException('That image type is not allowed.'); }
    $name = bin2hex(random_bytes(10)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], __DIR__ . '/../uploads/' . $name)) throw new RuntimeException('Could not save the file.');
    return $name;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) $err[] = 'Session expired. Reload and try again.';
    $p['name'] = trim((string)$_POST['name']); $p['description'] = trim((string)$_POST['description']);
    $p['price'] = (string)$_POST['price']; $p['category_id'] = (int)$_POST['category_id'];
    $p['variant_index'] = max(0, min(2, (int)$_POST['variant_index'])); $p['stock'] = max(0, (int)$_POST['stock']);
    $p['tint'] = preg_match('/^#[0-9a-fA-F]{6}$/', (string)$_POST['tint']) ? $_POST['tint'] : '#ffffff';
    $p['featured'] = isset($_POST['featured']) ? 1 : 0;
    if ($p['name'] === '' || strlen($p['name']) > 120) $err[] = 'Enter a product name.';
    if (!is_numeric($p['price']) || $p['price'] <= 0) $err[] = 'Enter a price above 0.';
    if ($p['description'] === '') $err[] = 'Enter a description.';
    if (!$err) {
        try {
            if ($img = save_upload('image', ['jpg', 'jpeg', 'png', 'webp'], ['image/jpeg', 'image/png', 'image/webp'], 3 * 1048576)) $p['image'] = $img;
            if ($mod = save_upload('model', ['glb'], [], 10 * 1048576, 'glTF')) $p['model'] = $mod;
            if ($id) {
                $pdo->prepare('UPDATE products SET category_id=?,name=?,description=?,price=?,variant_index=?,tint=?,image=?,model=?,stock=?,featured=? WHERE id=?')
                    ->execute([$p['category_id'], $p['name'], $p['description'], $p['price'], $p['variant_index'], $p['tint'], $p['image'], $p['model'], $p['stock'], $p['featured'], $id]);
            } else {
                $slug = slugify($p['name']); $c = $pdo->prepare('SELECT COUNT(*) FROM products WHERE slug=?'); $c->execute([$slug]);
                if ($c->fetchColumn()) $slug .= '-' . random_int(100, 999);
                $pdo->prepare('INSERT INTO products (category_id,name,slug,description,price,variant_index,tint,image,model,stock,featured) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
                    ->execute([$p['category_id'], $p['name'], $slug, $p['description'], $p['price'], $p['variant_index'], $p['tint'], $p['image'], $p['model'], $p['stock'], $p['featured']]);
            }
            flash('Product saved.'); redirect('admin/products.php');
        } catch (RuntimeException $ex) { $err[] = $ex->getMessage(); }
    }
}
?>
<h1 class="page-h"><?= $id ? 'Edit product' : 'Add product' ?></h1>
<?php foreach ($err as $m): ?><p class="flash err inline"><?= e($m) ?></p><?php endforeach; ?>
<form method="post" enctype="multipart/form-data" class="form">
<?= csrf_field() ?>
<label>Name<input name="name" value="<?= e($p['name']) ?>" required maxlength="120"></label>
<label>Description<textarea name="description" rows="4" required><?= e($p['description']) ?></textarea></label>
<label>Price (₦)<input name="price" type="number" step="0.01" min="1" value="<?= e($p['price']) ?>" required></label>
<label>Category<select name="category_id"><?php foreach ($cats as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $c['id'] == $p['category_id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></label>
<label>Colourway of the built-in shoe<select name="variant_index"><?php foreach (['Midnight', 'Beach', 'Street'] as $i => $n): ?><option value="<?= $i ?>" <?= $i == $p['variant_index'] ? 'selected' : '' ?>><?= $n ?></option><?php endforeach; ?></select></label>
<label>Tint (multiplies the colourway)<input name="tint" type="color" value="<?= e($p['tint']) ?>"></label>
<label>Stock<input name="stock" type="number" min="0" value="<?= (int)$p['stock'] ?>"></label>
<label class="check"><input type="checkbox" name="featured" <?= $p['featured'] ? 'checked' : '' ?>> Show on the home page</label>
<label>Photo (JPG, PNG or WebP, up to 3 MB)<input name="image" type="file" accept="image/jpeg,image/png,image/webp"></label>
<label>Own 3D model (.glb, up to 10 MB, replaces the built-in shoe)<input name="model" type="file" accept=".glb"></label>
<button class="btn big" type="submit">Save product</button>
</form>
<?php require __DIR__ . '/_foot.php'; ?>
