<?php
require_once "../config/db.php";
require_login();

$pdo = get_pdo();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ไม่มีเลขที่ออเดอร์");
}

$sqlOrder = "SELECT 
    o.*,
    c.name AS customer_name,
    b.branch_name
FROM orders o
LEFT JOIN customers c ON o.customer_id = c.customer_id
LEFT JOIN branches b  ON o.branch_id = b.branch_id
WHERE o.order_id = :id LIMIT 1";
$st = $pdo->prepare($sqlOrder);
$st->execute([':id'=>$id]);
$order = $st->fetch();

if (!$order) {
    die("ไม่พบออเดอร์");
}

$sqlItems = "SELECT 
    oi.*,
    p.product_name
FROM order_items oi
LEFT JOIN products p ON oi.product_id = p.product_id
WHERE oi.order_id = :id";
$st2 = $pdo->prepare($sqlItems);
$st2->execute([':id'=>$id]);
$items = $st2->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ใบเสร็จ #<?=$order['order_id'];?></title>
<style>
body{font-family:"Segoe UI",sans-serif;font-size:13px;margin:20px;}
h2{margin:0 0 10px;}
table{width:100%;border-collapse:collapse;margin-top:10px;}
th,td{border:1px solid #ccc;padding:6px;text-align:left;}
.text-right{text-align:right;}
</style>
</head>
<body>
<h2>ใบเสร็จรับเงิน</h2>
<p>เลขที่: <?=$order['order_id'];?> | วันที่: <?=$order['order_date'];?></p>
<p>ลูกค้า: <?=$order['customer_name'] ?? '-';?> | สาขา: <?=$order['branch_name'] ?? '-';?></p>

<table>
    <thead>
        <tr>
            <th>สินค้า</th>
            <th class="text-right">จำนวน</th>
            <th class="text-right">ราคา/หน่วย</th>
            <th class="text-right">รวม</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total = 0;
        foreach($items as $it):
            $sum = $it['quantity'] * $it['unit_price'];
            $total += $sum;
        ?>
        <tr>
            <td><?=$it['product_name'];?></td>
            <td class="text-right"><?=$it['quantity'];?></td>
            <td class="text-right"><?=number_format($it['unit_price'],2);?></td>
            <td class="text-right"><?=number_format($sum,2);?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
$discount = (float)$order['discount'];
$net = (float)$order['net_total'];
?>
<p class="text-right">ยอดรวม: <?=number_format($total,2);?> บาท</p>
<p class="text-right">ส่วนลด: <?=number_format($discount,2);?> บาท</p>
<p class="text-right"><strong>ยอดสุทธิ: <?=number_format($net,2);?> บาท</strong></p>

</body>
</html>
