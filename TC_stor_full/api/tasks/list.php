<?php
require_once __DIR__ . '/../../config/db.php';
require_login();

$pdo = get_pdo();

$sql = "SELECT 
    t.task_id,
    t.order_id,
    t.title,
    t.description,
    t.status,
    t.assigned_to,
    t.created_at,
    e.emp_name AS technician
FROM tasks t
LEFT JOIN employees e ON t.assigned_to = e.emp_id
ORDER BY t.task_id DESC
LIMIT 200";

$stmt = $pdo->query($sql);

json_response(['success' => true, 'data' => $stmt->fetchAll()]);
