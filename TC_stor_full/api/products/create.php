<?php
// api/products/create.php
require_once __DIR__ . '/../../config/db.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'method_not_allowed'], 405);
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);
if (!is_array($data) || empty($data)) {
    $data = $_POST;
}

$name       = trim($data['product_name'] ?? '');
$category   = isset($data['category_id']) ? (int)$data['category_id'] : null;
$type       = isset($data['type_id']) ? (int)$data['type_id'] : null;
$cost       = isset($data['cost_price']) ? (float)$data['cost_price'] : 0;
$sell       = isset($data['sell_price']) ? (float)$data['sell_price'] : 0;
$branch_id  = isset($data['branch_id']) ? (int)$data['branch_id'] : 1;
$stock_qty  = isset($data['stock_qty']) ? (int)$data['stock_qty'] : 0;
$barcode    = trim($data['barcode'] ?? '');

if ($name === '') {
    json_response(['success' => false, 'error' => 'product_name_required'], 400);
}

$pdo = get_pdo();

try {
    $pdo->beginTransaction();

    $sql = "INSERT INTO products (product_name, category_id, type_id, cost_price, sell_price, barcode, created_at)
            VALUES (:name, :cat, :type, :cost, :sell, :barcode, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name'    => $name,
        ':cat'     => $category,
        ':type'    => $type,
        ':cost'    => $cost,
        ':sell'    => $sell,
        ':barcode' => $barcode,
    ]);
    $product_id = (int)$pdo->lastInsertId();

    // สร้าง stock row
    $s = $pdo->prepare("INSERT INTO product_stocks (product_id, branch_id, quantity, last_updated) 
                        VALUES (:pid, :bid, :qty, NOW())
                        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), last_updated = NOW()");
    $s->execute([
        ':pid' => $product_id,
        ':bid' => $branch_id,
        ':qty' => $stock_qty,
    ]);

    $pdo->commit();

    json_response(['success' => true, 'product_id' => $product_id]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    json_response(['success' => false, 'error' => 'create_failed', 'detail' => $e->getMessage()], 500);
}
