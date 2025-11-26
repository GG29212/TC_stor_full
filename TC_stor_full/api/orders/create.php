<?php
// api/orders/create.php
require_once __DIR__ . '/../../config/db.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'method_not_allowed'], 405);
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);
if (!is_array($data) || empty($data)) {
    $data = $_POST;
}

$branch_id      = isset($data['branch_id']) ? (int)$data['branch_id'] : 1;
$customer_id    = isset($data['customer_id']) ? (int)$data['customer_id'] : null;
$discount       = isset($data['discount']) ? (float)$data['discount'] : 0.0;
$payment_status = $data['payment_status'] ?? 'paid';
$payment_method = $data['payment_method'] ?? 'cash';
$items          = $data['items'] ?? [];

if (!is_array($items) || count($items) === 0) {
    json_response(['success' => false, 'error' => 'items_required'], 400);
}

$pdo = get_pdo();

try {
    $pdo->beginTransaction();

    // คำนวณยอดรวม
    $total_amount = 0.0;
    foreach ($items as $it) {
        $q = (int)($it['quantity'] ?? 0);
        $p = (float)($it['unit_price'] ?? 0);
        $total_amount += $q * $p;
    }
    $net_total = $total_amount - $discount;

    // สร้าง order
    $sqlOrder = "INSERT INTO orders (customer_id, branch_id, total_amount, discount, net_total, payment_status, order_date, status)
                 VALUES (:cust, :branch, :total, :discount, :net, :pay_status, NOW(), 'completed')";
    $stOrder = $pdo->prepare($sqlOrder);
    $stOrder->execute([
        ':cust'       => $customer_id,
        ':branch'     => $branch_id,
        ':total'      => $total_amount,
        ':discount'   => $discount,
        ':net'        => $net_total,
        ':pay_status' => $payment_status,
    ]);
    $order_id = (int)$pdo->lastInsertId();

    // loop รายการสินค้า + ตัดสต็อก
    $stItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price, created_at)
                             VALUES (:oid, :pid, :qty, :price, NOW())");

    $stStockSelect = $pdo->prepare("SELECT quantity FROM product_stocks WHERE product_id = :pid AND branch_id = :bid FOR UPDATE");
    $stStockUpdate = $pdo->prepare("UPDATE product_stocks SET quantity = quantity - :qty WHERE product_id = :pid AND branch_id = :bid");

    $stMovement = $pdo->prepare("INSERT INTO stock_movements (product_id, branch_id, type, quantity, ref, created_at)
                                 VALUES (:pid, :bid, 'out', :qty, :ref, NOW())");

    foreach ($items as $it) {
        $pid  = (int)$it['product_id'];
        $qty  = (int)$it['quantity'];
        $price= (float)$it['unit_price'];

        if ($pid <= 0 || $qty <= 0) {
            throw new Exception('invalid_item');
        }

        // insert order item
        $stItem->execute([
            ':oid'   => $order_id,
            ':pid'   => $pid,
            ':qty'   => $qty,
            ':price' => $price,
        ]);

        // lock stock row
        $stStockSelect->execute([':pid' => $pid, ':bid' => $branch_id]);
        $row = $stStockSelect->fetch();
        if (!$row) {
            throw new Exception("stock_not_found_for_product_{$pid}");
        }
        $current_qty = (int)$row['quantity'];
        if ($current_qty < $qty) {
            throw new Exception("insufficient_stock_for_product_{$pid}");
        }

        // update stock
        $stStockUpdate->execute([':qty' => $qty, ':pid' => $pid, ':bid' => $branch_id]);

        // insert movement
        $stMovement->execute([
            ':pid' => $pid,
            ':bid' => $branch_id,
            ':qty' => $qty,
            ':ref' => 'order#' . $order_id,
        ]);
    }

    // บันทึก finance (income)
    $stFin = $pdo->prepare("INSERT INTO finance (branch_id, order_id, type, amount, description, payment_method, created_at)
                            VALUES (:branch, :oid, 'income', :amount, :desc, :method, NOW())");
    $stFin->execute([
        ':branch' => $branch_id,
        ':oid'    => $order_id,
        ':amount' => $net_total,
        ':desc'   => 'Order #' . $order_id,
        ':method' => $payment_method,
    ]);

    $pdo->commit();

    json_response([
        'success'  => true,
        'order_id' => $order_id,
        'total'    => $total_amount,
        'net'      => $net_total,
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    json_response(['success' => false, 'error' => $e->getMessage()], 400);
}
