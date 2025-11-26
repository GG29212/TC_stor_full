<?php 
session_start();
if (!empty($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>เข้าสู่ระบบ | TC_stor</title>
<link rel="stylesheet" href="assets/css/main.css">
<style>
body{
    height:100vh;
    margin:0;
    padding:0;
    display:flex;
    align-items:center;
    justify-content:center;
    background: radial-gradient(circle at top, #1d4ed8, #020617);
    font-family: 'Segoe UI', sans-serif;
}
.login-box{
    width:330px;
    background:rgba(15,23,42,0.82);
    box-shadow:0 0 35px rgba(56,189,248,0.45);
    border-radius:16px;
    padding:30px 26px;
    backdrop-filter:blur(12px);
    border:1px solid rgba(148,163,184,0.2);
}
.login-box h2{
    text-align:center;
    color:#e5e7eb;
    margin-bottom:20px;
    font-weight:600;
    letter-spacing:1px;
}
.input-box{
    margin-bottom:12px;
}
.input-box input{
    width:100%;
    padding:10px;
    border-radius:8px;
    border:1px solid #1f2937;
    background:#020617;
    color:#e5e7eb;
    font-size:14px;
}
.input-box input:focus{
    outline:none;
    border-color:#38bdf8;
    box-shadow:0 0 0 1px rgba(56,189,248,0.6);
}
button{
    width:100%;
    padding:10px;
    border:none;
    margin-top:4px;
    cursor:pointer;
    background:linear-gradient(135deg,#38bdf8,#6366f1);
    color:#e5e7eb;
    font-weight:600;
    border-radius:999px;
}
button:hover{
    box-shadow:0 0 16px rgba(56,189,248,0.8);
}
.error{
    color:#f97316;
    margin-bottom:10px;
    font-size:13px;
}
</style>
</head>
<body>

<div class="login-box">
    <h2>TC_stor</h2>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="POST" action="../api/auth/login.php">
        <div class="input-box">
            <input type="text" name="username" placeholder="ชื่อผู้ใช้" required>
        </div>

        <div class="input-box">
            <input type="password" name="password" placeholder="รหัสผ่าน" required>
        </div>

        <button>เข้าสู่ระบบ</button>
    </form>
</div>

</body>
</html>