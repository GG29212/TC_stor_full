<?php
require_once __DIR__ . '/../../config/db.php';
require_login();

$data = json_decode(file_get_contents("php://input"), true);

$type = $data['type'] ?? '';
$amount = isset($data['amount']) ? (float)$data['amount'] : 0;
$desc = $data['description'] ?? '';
$method = $data['method'] ?? 'cash';
$branch = isset($data['branch_id']) ? (int)$data['branch_id'] : 1;

if (!in_array($type,['income','expense'])) {
    json_response(['success'=>false,'error'=>'invalid_type'],400);
}

$pdo = get_pdo();

$sql = "INSERT INTO finance (type, amount, description, payment_method, branch_id, created_at)
        VALUES (:t, :amt, :des, :m, :b, NOW())";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':t'=>$type,
    ':amt'=>$amount,
    ':des'=>$desc,
    ':m'=>$method,
    ':b'=>$branch
]);

json_response(['success'=>true]);


