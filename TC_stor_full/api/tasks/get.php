<?php
require_once __DIR__ . '/../../config/db.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) json_response(['success'=>false,'error'=>'invalid_id'],400);

$pdo = get_pdo();

$sql = "SELECT 
    t.*,
    e.emp_name AS technician
FROM tasks t
LEFT JOIN employees e ON t.assigned_to = e.emp_id
WHERE t.task_id = :id
LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id'=>$id]);

$row = $stmt->fetch();

json_response(['success'=>true,'data'=>$row]);
