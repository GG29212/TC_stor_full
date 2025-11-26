<?php
require_once __DIR__ . '/../../config/db.php';
require_login();

$data = json_decode(file_get_contents("php://input"), true);

$task_id = $data['task_id'] ?? 0;
$status  = $data['status'] ?? '';

$valid = ['waiting','in_progress','delayed','done'];

if ($task_id <= 0 || !in_array($status,$valid)) {
    json_response(['success'=>false,'error'=>'invalid_data'],400);
}

$pdo = get_pdo();

$sql = "UPDATE tasks SET status = :s WHERE task_id = :tid";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':s'=>$status,
    ':tid'=>$task_id
]);

json_response(['success'=>true]);
