<?php
require_once __DIR__ . '/../../config/db.php';
require_login();

$name = trim($_POST['name'] ?? '');

if ($name === '') json_response(['success'=>false,'error'=>'name_required'],400);

$pdo=get_pdo();

$stmt=$pdo->prepare("INSERT INTO categories (category_name) VALUES (:n)");
$stmt->execute([':n'=>$name]);

json_response(['success'=>true]);
