<?php
require_once __DIR__ . '/../../config/db.php';
require_login();

$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) $data = $_POST;

$title       = trim($data['title'] ?? '');
$description = trim($data['description'] ?? '');
$order_id    = $data['order_id'] ?? null;

if ($title === '') {
    json_response(['success'=>false,'error'=>'title_required'],400);
}

$pdo = get_pdo();

$sql = "INSERT INTO tasks (order_id, title, description, status, created_at)
        VALUES (:oid, :title, :des, 'waiting', NOW())";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':oid'=>$order_id,
    ':title'=>$title,
    ':des'=>$description,
]);

json_response(['success'=>true]);
