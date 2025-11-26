<?php
// api/products/get.php
require_once __DIR__ . '/../../config/db.php';
require_login();

$pdo = get_pdo();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    json_response(['success' => false, 'error' => 'invalid_id'], 400);
}

$sql = "SELECT 
    p.*,
    c.category_name,
    t.type_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.category_id
LEFT JOIN product_types t ON p.type_id = t.type_id
WHERE p.product_id = :id
LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$row = $stmt->fetch();

if (!$row) {
    json_response(['success' => false, 'error' => 'not_found'], 404);
}

json_response(['success' => true, 'data' => $row]);
