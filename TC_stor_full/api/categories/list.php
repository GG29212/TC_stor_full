<?php
require_once __DIR__ . '/../../config/db.php';
require_login();

$pdo=get_pdo();

$stmt=$pdo->query("SELECT * FROM categories ORDER BY category_id DESC");
json_response(['success'=>true,'data'=>$stmt->fetchAll()]);
