<?php
// api/products/update.php
require_once __DIR__ . '/../../config/db.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    json_response(['success' => false, 'error' => 'method_not_allowed'], 405);
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);
if (!is_array($data) || empty($data)) {
    $data = $_POST;
}

$id         = isset($data['product_id']) ? (int)$data['product_id'] : 0;
$name       = trim($data['product_name'] ?? '');
$category   = isset($data['category_id']) ? (int)$data['category_id'] : null;
$type       = isset($data['type_id']) ? (int)$data['type_id'] : null;
$cost       = isset($data['cost_price']) ? (float)$data['cost_price'] : 0;
$sell       = isset($data['sell_price']) ? (float)$data['sell_price'] : 0;
$barcode    = trim($data['barcode'] ?? '');

if ($id <= 0 || $name === '') {
    json_response(['success' => false, 'error' => 'invalid_input'], 400);
}

$pdo = get_pdo();

$sql = "UPDATE products 
        SET product_name = :name,
            category_id  = :cat,
            type_id      = :type,
            cost_price   = :cost,
            sell_price   = :sell,
            barcode      = :barcode
        WHERE product_id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':name'    => $name,
    ':cat'     => $category,
    ':type'    => $type,
    ':cost'    => $cost,
    ':sell'    => $sell,
    ':barcode' => $barcode,
    ':id'      => $id,
]);

json_response(['success' => true]);
