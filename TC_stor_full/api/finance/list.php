<?php
require_once __DIR__ . '/../../config/db.php';
require_login();

$pdo = get_pdo();

$sql = "SELECT * FROM finance ORDER BY created_at DESC LIMIT 300";

$stmt = $pdo->query($sql);

json_response(['success'=>true,'data'=>$stmt->fetchAll()]);
