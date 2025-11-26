<?php
// api/dashboard/summary.php
require_once __DIR__ . '/../../config/db.php';
require_login();

$pdo = get_pdo();

// ยอดขายวันนี้
$today = date('Y-m-d');
$sqlToday = "SELECT 
    COALESCE(SUM(net_total),0) AS sales_today,
    COUNT(*) AS orders_today
FROM orders
WHERE DATE(order_date) = :d";
$stToday = $pdo->prepare($sqlToday);
$stToday->execute([':d' => $today]);
$todayRow = $stToday->fetch();

// ยอดขายเดือนนี้
$ym = date('Y-m');
$sqlMonth = "SELECT 
    COALESCE(SUM(net_total),0) AS sales_month,
    COUNT(*) AS orders_month
FROM orders
WHERE DATE_FORMAT(order_date, '%Y-%m') = :ym";
$stMonth = $pdo->prepare($sqlMonth);
$stMonth->execute([':ym' => $ym]);
$monthRow = $stMonth->fetch();

// สินค้าใกล้หมด
$sqlLow = "SELECT 
    p.product_id,
    p.product_name,
    s.branch_id,
    s.quantity,
    p.alert_min
FROM product_stocks s
JOIN products p ON s.product_id = p.product_id
WHERE s.quantity <= p.alert_min
ORDER BY s.quantity ASC
LIMIT 20";
$low = $pdo->query($sqlLow)->fetchAll();

// งาน (tasks)
$sqlTasks = "SELECT 
    SUM(CASE WHEN status = 'waiting' OR status = 'in_progress' THEN 1 ELSE 0 END) AS tasks_open,
    SUM(CASE WHEN status = 'delayed' THEN 1 ELSE 0 END) AS tasks_delayed,
    SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) AS tasks_done
FROM tasks";
$tasks = $pdo->query($sqlTasks)->fetch();

// สรุปกำไรแบบคร่าว ๆ จาก finance
$sqlProfit = "SELECT 
    COALESCE(SUM(CASE WHEN type='income' THEN amount ELSE 0 END),0) AS total_income,
    COALESCE(SUM(CASE WHEN type='expense' THEN amount ELSE 0 END),0) AS total_expense
FROM finance";
$fin = $pdo->query($sqlProfit)->fetch();

json_response([
    'success' => true,
    'sales' => [
        'today' => [
            'amount' => (float)$todayRow['sales_today'],
            'orders' => (int)$todayRow['orders_today'],
        ],
        'month' => [
            'amount' => (float)$monthRow['sales_month'],
            'orders' => (int)$monthRow['orders_month'],
        ],
    ],
    'stock' => [
        'low_items' => $low,
    ],
    'tasks' => [
        'open'    => (int)$tasks['tasks_open'],
        'delayed' => (int)$tasks['tasks_delayed'],
        'done'    => (int)$tasks['tasks_done'],
    ],
    'finance' => [
        'income'  => (float)$fin['total_income'],
        'expense' => (float)$fin['total_expense'],
        'profit'  => (float)$fin['total_income'] - (float)$fin['total_expense'],
    ],
]);
