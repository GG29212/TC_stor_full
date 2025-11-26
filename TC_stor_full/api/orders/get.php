<?php
// api/orders/get.php
require_once __DIR__ . '/../../config/db.php';
require_login();

$pdo = get_pdo();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    json_response(['success' => false, 'error' => 'invalid_id'], 400);
}

$sqlOrder = "SELECT 
    o.*,
    c.name AS customer_name,
    b.branch_name
FROM orders o
LEFT JOIN customers c ON o.customer_id = c.customer_id
LEFT JOIN branches b  ON o.branch_id = b.branch_id
WHERE o.order_id = :id
LIMIT 1";
$stOrder = $pdo->prepare($sqlOrder);
$stOrder->execute([':id' => $id]);
$order = $stOrder->fetch();

if (!$order) {
    json_response(['success' => false, 'error' => 'not_found'], 404);
}

$sqlItems = "SELECT 
    oi.*,
    p.product_name
FROM order_items oi
LEFT JOIN products p ON oi.product_id = p.product_id
WHERE oi.order_id = :id";
$stItems = $pdo->prepare($sqlItems);
$stItems->execute([':id' => $id]);
$items = $stItems->fetchAll();

json_response([
    'success' => true,
    'order'   => $order,
    'items'   => $items,
]);
