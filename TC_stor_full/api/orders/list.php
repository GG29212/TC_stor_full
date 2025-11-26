<?php
// api/orders/list.php
require_once __DIR__ . '/../../config/db.php';
require_login();

$pdo = get_pdo();

$sql = "SELECT 
    o.order_id,
    o.order_date,
    o.total_amount,
    o.discount,
    o.net_total,
    o.payment_status,
    o.status,
    c.name AS customer_name,
    b.branch_name
FROM orders o
LEFT JOIN customers c ON o.customer_id = c.customer_id
LEFT JOIN branches b  ON o.branch_id = b.branch_id
ORDER BY o.order_date DESC, o.order_id DESC
LIMIT 200";

$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll();

json_response(['success' => true, 'data' => $rows]);
