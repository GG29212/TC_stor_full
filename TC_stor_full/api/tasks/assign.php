<?php
require_once __DIR__ . '/../../config/db.php';
require_login();

$data = json_decode(file_get_contents("php://input"), true);

$task_id = $data['task_id'] ?? 0;
$emp_id  = $data['emp_id'] ?? 0;

if ($task_id <= 0 || $emp_id <= 0) {
    json_response(['success'=>false,'error'=>'invalid_data'],400);
}

$pdo = get_pdo();

$sql = "UPDATE tasks SET assigned_to = :emp, status='in_progress' WHERE task_id = :tid";

$stmt = $pdo->prepare($sql);
$stmt->execute([':emp'=>$emp_id, ':tid'=>$task_id]);

json_response(['success'=>true]);
