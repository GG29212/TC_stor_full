<?php
// api/auth/logout.php
require_once __DIR__ . '/../../config/db.php';

session_unset();
session_destroy();

$accepts = $_SERVER['HTTP_ACCEPT'] ?? '';
$xhr = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if ($xhr || strpos($accepts, 'application/json') !== false) {
	json_response(['success' => true]);
} else {
	// Redirect back to login page for browser link
	header('Location: ../../public/login.php');
	exit;
}
