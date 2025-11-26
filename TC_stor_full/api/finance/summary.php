<?php
require_once __DIR__ . '/../../config/db.php';
require_login();

$pdo = get_pdo();

$sql = "SELECT 
    SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) AS income,
    SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) AS expense
FROM finance";

$row = $pdo->query($sql)->fetch();

json_response([
    'success'=>true,
    'income'=>(float)$row['income'],
    'expense'=>(float)$row['expense'],
    'profit' => (float)$row['income'] - (float)$row['expense']
]);
