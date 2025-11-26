<?php
require_once "../config/db.php";
require_login();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ออเดอร์ | TC_stor</title>
<link rel="stylesheet" href="assets/css/dashboard.css">
<link rel="stylesheet" href="assets/css/orders.css">
</head>
<body>

<div class="sidebar">
    <h2>TC_stor</h2>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="products.php">📦 สินค้า</a>
    <a href="orders.php" class="active">🧾 ออเดอร์</a>
    <a href="tasks.php">🛠 งานช่าง</a>
    <a href="finance.php">💰 การเงิน</a>
    <a href="../api/auth/logout.php">🚪 ออกจากระบบ</a>
</div>

<div class="main">
    <div class="header">
        <h1>จัดการออเดอร์</h1>
    </div>

    <div class="orders-toolbar">
        <button class="btn-primary" onclick="openOrderModal()">+ สร้างออเดอร์ใหม่</button>
    </div>

    <div class="orders-table-wrapper">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>เลขที่</th>
                    <th>วันที่</th>
                    <th>ลูกค้า</th>
                    <th>สาขา</th>
                    <th>ยอดสุทธิ</th>
                    <th>สถานะชำระ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody id="orders_tbody">
            </tbody>
        </table>
    </div>
</div>

<!-- Modal สร้างออเดอร์ -->
<div class="modal-overlay" id="order_modal">
  <div class="modal modal-large">
    <h2>สร้างออเดอร์ใหม่</h2>

    <form onsubmit="saveOrder(event)">
        <div class="order-form-grid">
            <div>
                <label>ลูกค้า (ถ้ามี)</label>
                <input type="text" id="customer_name" placeholder="ชื่อลูกค้า (ไม่บังคับ)">

                <label>สาขา</label>
                <select id="order_branch_id">
                    <option value="1">สาขาหลัก</option>
                </select>

                <label>วิธีชำระเงิน</label>
                <select id="payment_method">
                    <option value="cash">เงินสด</option>
                    <option value="transfer">โอน</option>
                </select>
            </div>

            <div>
                <label>ส่วนลด (บาท)</label>
                <input type="number" step="0.01" id="discount" value="0">

                <label>ยอดรวม</label>
                <input type="text" id="total_amount" readonly>

                <label>ยอดสุทธิ</label>
                <input type="text" id="net_total" readonly>
            </div>
        </div>

        <hr>

        <div class="order-items-toolbar">
            <button type="button" class="btn-secondary" onclick="addOrderItemRow()">+ เพิ่มรายการ</button>
        </div>

        <div class="order-items-wrapper">
            <table class="order-items-table">
                <thead>
                    <tr>
                        <th>สินค้า (ID)</th>
                        <th>ชื่อสินค้า</th>
                        <th>จำนวน</th>
                        <th>ราคา/หน่วย</th>
                        <th>รวม</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="order_items_tbody">
                </tbody>
            </table>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="closeOrderModal()">ยกเลิก</button>
            <button type="submit" class="btn-primary">บันทึกออเดอร์</button>
        </div>
    </form>
  </div>
</div>

<script src="assets/js/orders.js"></script>
</body>
</html>
