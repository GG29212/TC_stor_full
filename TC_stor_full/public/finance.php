<?php
require_once "../config/db.php";
require_login();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>การเงิน | TC_stor</title>
<link rel="stylesheet" href="assets/css/dashboard.css">
<link rel="stylesheet" href="assets/css/finance.css">
</head>
<body>

<div class="sidebar">
    <h2>TC_stor</h2>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="products.php">📦 สินค้า</a>
    <a href="orders.php">🧾 ออเดอร์</a>
    <a href="tasks.php">🛠 งานช่าง</a>
    <a href="finance.php" class="active">💰 การเงิน</a>
    <a href="../api/auth/logout.php">🚪 ออกจากระบบ</a>
</div>

<div class="main">
    <div class="header">
        <h1>จัดการการเงิน</h1>
    </div>

    <!-- สรุปรายรับรายจ่าย -->
    <div class="finance-summary">
        <div class="card income-card">
            <h3>รายรับรวม</h3>
            <p class="value" id="income_total">0</p>
        </div>
        <div class="card expense-card">
            <h3>รายจ่ายรวม</h3>
            <p class="value" id="expense_total">0</p>
        </div>
        <div class="card profit-card">
            <h3>กำไรโดยประมาณ</h3>
            <p class="value" id="profit_total">0</p>
        </div>
    </div>

    <!-- ฟอร์มเพิ่มรายการ -->
    <div class="finance-form-wrapper">
        <h2>เพิ่มรายการเงินสด</h2>
        <form onsubmit="saveFinance(event)" class="finance-form">
            <div>
                <label>ประเภท</label>
                <select id="finance_type" required>
                    <option value="income">รายรับ</option>
                    <option value="expense">รายจ่าย</option>
                </select>
            </div>

            <div>
                <label>จำนวนเงิน</label>
                <input type="number" step="0.01" id="finance_amount" required>
            </div>

            <div>
                <label>วิธีชำระ</label>
                <select id="finance_method">
                    <option value="cash">เงินสด</option>
                    <option value="transfer">โอน</option>
                </select>
            </div>

            <div class="full-row">
                <label>รายละเอียด</label>
                <input type="text" id="finance_desc" placeholder="เช่น ค่าแรงช่าง, ค่าวัตถุดิบ, รายรับอื่น ๆ">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">บันทึก</button>
                <button type="button" class="btn-secondary" onclick="exportFinance()">Export CSV</button>
            </div>
        </form>
    </div>

    <!-- ตารางรายการเงิน -->
    <div class="finance-table-wrapper">
        <h2>รายการการเงินล่าสุด</h2>
        <table class="finance-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ประเภท</th>
                    <th>จำนวน</th>
                    <th>รายละเอียด</th>
                    <th>วิธีชำระ</th>
                    <th>วันที่</th>
                </tr>
            </thead>
            <tbody id="finance_tbody">
            </tbody>
        </table>
    </div>
</div>

<script src="assets/js/finance.js"></script>
</body>
</html>
