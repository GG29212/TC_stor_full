<?php
require_once __DIR__ . '/../../config/db.php';
require_login();

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=finance_export.csv");

$pdo = get_pdo();

$sql = "SELECT finance_id, type, amount, description, payment_method, created_at 
        FROM finance ORDER BY finance_id DESC";
$stmt = $pdo->query($sql);

$output = fopen("php://output","w");
fputcsv($output, ['ID','Type','Amount','Description','Method','Created']);

while ($r = $stmt->fetch()) {
    fputcsv($output, $r);
}

fclose($output);
exit;
