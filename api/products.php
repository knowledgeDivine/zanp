<?php
// Used two ways: included by shop.php (HTML only) and called by fetch() for live search.
require_once __DIR__ . '/../includes/bootstrap.php';
$q = trim((string)($_GET['q'] ?? '')); $cat = trim((string)($_GET['cat'] ?? ''));
$sql = 'SELECT p.*, c.name AS cat FROM products p JOIN categories c ON c.id=p.category_id WHERE 1=1';
$args = [];
if ($q !== '') { $sql .= ' AND (p.name LIKE ? OR p.description LIKE ?)'; $args[] = "%$q%"; $args[] = "%$q%"; }
if ($cat !== '') { $sql .= ' AND c.slug = ?'; $args[] = $cat; }
$sql .= ' ORDER BY p.featured DESC, p.id DESC';
$st = $pdo->prepare($sql); $st->execute($args);
$rows = $st->fetchAll();
if (empty($html_only)) header('Content-Type: text/html; charset=utf-8');
if (!$rows) { echo '<p class="empty">No shoes match that search. Try another word or pick All.</p>'; return; }
foreach ($rows as $p) include __DIR__ . '/../includes/card.php';
