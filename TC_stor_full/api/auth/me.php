<?php
// api/auth/me.php
require_once __DIR__ . '/../../config/db.php';

if (empty($_SESSION['user_id'])) {
    json_response(['authenticated' => false]);
}

json_response([
    'authenticated' => true,
    'user' => [
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? null,
        'role' => $_SESSION['role'] ?? null,
    ]
]);
