<?php
require_once "../config/db.php";
require_login();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Dashboard | TC_stor</title>
<link rel="stylesheet" href="assets/css/dashboard.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="sidebar">
    <h2>TC_stor</h2>
    <a href="dashboard.php" class="active">📊 Dashboard</a>
    <a href="products.php">📦 สินค้า</a>
    <a href="orders.php">🧾 ออเดอร์</a>
    <a href="tasks.php">🛠 งานช่าง</a>
    <a href="finance.php">💰 การเงิน</a>
    <a href="../api/auth/logout.php">🚪 ออกจากระบบ</a>
</div>

<div class="main">
    <div class="header">
        <h1>แผงควบคุมระบบ</h1>
    </div>

    <div class="stats-grid">

        <div class="card" id="sales_today_box">
            <h3>ยอดขายวันนี้</h3>
            <p class="value" id="sales_today">0</p>
            <span id="orders_today">0 ออเดอร์</span>
        </div>

        <div class="card" id="sales_month_box">
            <h3>ยอดขายเดือนนี้</h3>
            <p class="value" id="sales_month">0</p>
            <span id="orders_month">0 ออเดอร์</span>
        </div>

        <div class="card" id="profit_box">
            <h3>กำไร</h3>
            <p class="value" id="profit">0</p>
        </div>

        <div class="card" id="task_box">
            <h3>งานทั้งหมด</h3>
            <p class="value" id="tasks_open">0</p>
            <span id="tasks_delayed">ล่าช้า: 0</span>
        </div>

    </div>

    <div class="chart-section">
        <canvas id="salesChart"></canvas>
    </div>

    <div class="low-stock-section">
        <h2>สินค้าใกล้หมด</h2>
        <table class="low-stock-table" id="low_stock_table">
            <thead>
                <tr>
                    <th>สินค้า</th>
                    <th>คงเหลือ</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

</div>

<script src="assets/js/dashboard.js"></script>
</body>
</html>
