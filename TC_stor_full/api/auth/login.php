<?php
// api/auth/login.php
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'method_not_allowed'], 405);
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);
if (!is_array($data) || !isset($data['username'], $data['password'])) {
    // รองรับแบบ form ด้วย
    $data = $_POST;
}

$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');

if ($username === '' || $password === '') {
    json_response(['success' => false, 'error' => 'username_or_password_required'], 400);
}

$pdo = get_pdo();

$stmt = $pdo->prepare("SELECT user_id, username, password_hash, role FROM users WHERE username = :u LIMIT 1");
$stmt->execute([':u' => $username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    $accepts = $_SERVER['HTTP_ACCEPT'] ?? '';
    $xhr = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($xhr || strpos($accepts, 'application/json') !== false) {
        json_response(['success' => false, 'error' => 'invalid_credentials'], 401);
    } else {
        $_SESSION['error'] = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
        header('Location: ../../public/login.php');
        exit;
    }
}

$_SESSION['user_id'] = (int)$user['user_id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

$accepts = $_SERVER['HTTP_ACCEPT'] ?? '';
$xhr = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if ($xhr || strpos($accepts, 'application/json') !== false) {
    json_response([
        'success' => true,
        'user' => [
            'user_id' => (int)$user['user_id'],
            'username' => $user['username'],
            'role' => $user['role'],
        ]
    ]);
} else {
    header("Location: ../../public/dashboard.php");
    exit;
}
