<?php
require_once "../config/db.php";
require_login();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>สินค้า | TC_stor</title>
<link rel="stylesheet" href="assets/css/dashboard.css">
<link rel="stylesheet" href="assets/css/products.css">
</head>
<body>

<div class="sidebar">
    <h2>TC_stor</h2>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="products.php" class="active">📦 สินค้า</a>
    <a href="orders.php">🧾 ออเดอร์</a>
    <a href="tasks.php">🛠 งานช่าง</a>
    <a href="finance.php">💰 การเงิน</a>
    <a href="../api/auth/logout.php">🚪 ออกจากระบบ</a>
</div>

<div class="main">
    <div class="header">
        <h1>จัดการสินค้า</h1>
    </div>

    <div class="products-toolbar">
        <button class="btn-primary" onclick="openProductModal()">+ เพิ่มสินค้า</button>
        <select id="branch_filter" onchange="loadProducts()">
            <option value="">ทุกสาขา</option>
        </select>
    </div>

    <div class="products-table-wrapper">
        <table class="products-table">
            <thead>
                <tr>
                    <th>รหัส</th>
                    <th>ชื่อสินค้า</th>
                    <th>หมวดหมู่</th>
                    <th>ประเภท</th>
                    <th>ต้นทุน</th>
                    <th>ขาย</th>
                    <th>กำไร %</th>
                    <th>สต็อก</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody id="products_tbody">
            </tbody>
        </table>
    </div>
</div>

<!-- Modal เพิ่ม/แก้ไขสินค้า -->
<div class="modal-overlay" id="product_modal">
  <div class="modal">
    <h2 id="modal_title">เพิ่มสินค้า</h2>
    <form onsubmit="saveProduct(event)">
        <input type="hidden" id="product_id">

        <label>ชื่อสินค้า</label>
        <input type="text" id="product_name" required>

        <label>หมวดหมู่</label>
        <select id="category_id" required></select>

        <label>ประเภท</label>
        <select id="type_id" required></select>

        <label>ต้นทุน (บาท)</label>
        <input type="number" step="0.01" id="cost_price" required>

        <label>ราคาขาย (บาท)</label>
        <input type="number" step="0.01" id="sell_price" required>

        <label>จำนวนสต็อกเริ่มต้น</label>
        <input type="number" id="stock_qty" value="0">

        <label>สาขา</label>
        <select id="branch_id">
            <option value="1">สาขาหลัก</option>
        </select>

        <label>บาร์โค้ด (ถ้ามี)</label>
        <input type="text" id="barcode">

        <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="closeProductModal()">ยกเลิก</button>
            <button type="submit" class="btn-primary">บันทึก</button>
        </div>
    </form>
  </div>
</div>

<script src="assets/js/products.js"></script>
</body>
</html>
