<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_ok()) json_out(['ok' => false, 'error' => 'Session expired. Reload the page.'], 400);
$email = trim((string)($_POST['email'] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) json_out(['ok' => false, 'error' => 'Enter a valid email address.'], 422);
$pdo->prepare('INSERT IGNORE INTO subscribers (email) VALUES (?)')->execute([$email]);
json_out(['ok' => true, 'message' => 'You’re on the list.']);
