<?php
// api/products/list.php
require_once __DIR__ . '/../../config/db.php';
require_login();

$pdo = get_pdo();

$branch_id = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : null;

$sql = "SELECT 
    p.product_id,
    p.product_name,
    c.category_name,
    t.type_name,
    p.cost_price,
    p.sell_price,
    ROUND(
        CASE 
            WHEN p.cost_price > 0 
            THEN ((p.sell_price - p.cost_price) / p.cost_price) * 100
            ELSE 0 
        END, 2
    ) AS profit_percent,
    s.quantity AS stock_qty,
    s.branch_id
FROM products p
LEFT JOIN categories c ON p.category_id = c.category_id
LEFT JOIN product_types t ON p.type_id = t.type_id
LEFT JOIN product_stocks s ON p.product_id = s.product_id";

$params = [];
if ($branch_id) {
    $sql .= " WHERE s.branch_id = :bid";
    $params[':bid'] = $branch_id;
}
$sql .= " ORDER BY p.product_id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

json_response(['success' => true, 'data' => $rows]);
