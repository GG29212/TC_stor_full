<?php
// config/db.php
// แก้ค่าตรงนี้ให้ตรงกับ MySQL ของอู๋
$db_host = '127.0.0.1';
$db_name = 'tc_stor';
$db_user = 'root';
$db_pass = '';
$db_charset = 'utf8mb4';

session_start();

function get_pdo(): PDO {
    static $pdo = null;
    global $db_host, $db_name, $db_user, $db_pass, $db_charset;

    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = "mysql:host={$db_host};dbname={$db_name};charset={$db_charset}";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $db_user, $db_pass, $options);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'DB connection failed']);
        exit;
    }

    return $pdo;
}

function json_response($data, int $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function require_login() {
    if (empty($_SESSION['user_id'])) {
        $accepts = $_SERVER['HTTP_ACCEPT'] ?? '';
        $xhr = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        if ($xhr || strpos($accepts, 'application/json') !== false) {
            json_response(['success' => false, 'error' => 'unauthorized'], 401);
        } else {
            // For regular browser requests, redirect to login page
            $login = dirname($_SERVER['SCRIPT_NAME']) . '/login.php';
            header('Location: ' . $login);
            exit;
        }
    }
}
