<?php
// api/products/delete.php
require_once __DIR__ . '/../../config/db.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    json_response(['success' => false, 'error' => 'invalid_id'], 400);
}

$pdo = get_pdo();

$stmt = $pdo->prepare("DELETE FROM products WHERE product_id = :id");
$stmt->execute([':id' => $id]);

json_response(['success' => true]);
