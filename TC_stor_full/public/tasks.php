<?php
require_once "../config/db.php";
require_login();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>งานช่าง | TC_stor</title>
<link rel="stylesheet" href="assets/css/dashboard.css">
<link rel="stylesheet" href="assets/css/tasks.css">
</head>
<body>

<div class="sidebar">
    <h2>TC_stor</h2>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="products.php">📦 สินค้า</a>
    <a href="orders.php">🧾 ออเดอร์</a>
    <a href="tasks.php" class="active">🛠 งานช่าง</a>
    <a href="finance.php">💰 การเงิน</a>
    <a href="../api/auth/logout.php">🚪 ออกจากระบบ</a>
</div>

<div class="main">
    <div class="header">
        <h1>จัดการงานช่าง</h1>
    </div>

    <div class="tasks-toolbar">
        <button class="btn-primary" onclick="openTaskModal()">+ เพิ่มงานใหม่</button>
    </div>

    <div class="tasks-table-wrapper">
        <table class="tasks-table">
            <thead>
                <tr>
                    <th>รหัสงาน</th>
                    <th>หัวข้องาน</th>
                    <th>ออเดอร์ที่เกี่ยวข้อง</th>
                    <th>ช่าง</th>
                    <th>สถานะ</th>
                    <th>วันที่สร้าง</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody id="tasks_tbody">
            </tbody>
        </table>
    </div>
</div>

<!-- Modal เพิ่มงาน -->
<div class="modal-overlay" id="task_modal">
  <div class="modal">
    <h2>เพิ่มงานใหม่</h2>
    <form onsubmit="saveTask(event)">
        <label>หัวข้องาน</label>
        <input type="text" id="task_title" required>

        <label>รายละเอียด</label>
        <textarea id="task_description" rows="3"></textarea>

        <label>เลขที่ออเดอร์ (ถ้ามี)</label>
        <input type="number" id="task_order_id" placeholder="เชื่อมกับออเดอร์ (ไม่บังคับ)">

        <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="closeTaskModal()">ยกเลิก</button>
            <button type="submit" class="btn-primary">บันทึก</button>
        </div>
    </form>
  </div>
</div>

<script src="assets/js/tasks.js"></script>
</body>
</html>
