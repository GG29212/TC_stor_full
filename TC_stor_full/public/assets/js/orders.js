// assets/js/orders.js

async function fetchJSON(url, options = {}) {
    const res = await fetch(url, options);
    return await res.json();
}

async function loadOrders() {
    try {
        const data = await fetchJSON("../api/orders/list.php");
        if (!data.success) return;
        const tbody = document.getElementById("orders_tbody");
        tbody.innerHTML = "";
        data.data.forEach(o => {
            tbody.innerHTML += `
                <tr>
                    <td>${o.order_id}</td>
                    <td>${o.order_date ?? ''}</td>
                    <td>${o.customer_name ?? '-'}</td>
                    <td>${o.branch_name ?? '-'}</td>
                    <td>${parseFloat(o.net_total).toLocaleString()}</td>
                    <td>${o.payment_status ?? ''}</td>
                    <td>
                        <button class="action-btn action-view" onclick="openReceipt(${o.order_id})">ใบเสร็จ</button>
                    </td>
                </tr>
            `;
        });
    } catch (e) {
        console.error(e);
    }
}

function openReceipt(order_id) {
    window.open("receipt.php?id=" + order_id, "_blank");
}

function openOrderModal() {
    document.getElementById("customer_name").value = "";
    document.getElementById("discount").value = "0";
    document.getElementById("total_amount").value = "";
    document.getElementById("net_total").value = "";
    document.getElementById("order_items_tbody").innerHTML = "";
    addOrderItemRow();
    document.getElementById("order_modal").style.display = "flex";
}

function closeOrderModal() {
    document.getElementById("order_modal").style.display = "none";
}

function addOrderItemRow() {
    const tbody = document.getElementById("order_items_tbody");
    const row = document.createElement("tr");

    row.innerHTML = `
        <td><input type="number" class="item_product_id" placeholder="ID"></td>
        <td><input type="text" class="item_product_name" placeholder="ชื่อสินค้า (แสดงเฉยๆ)"></td>
        <td><input type="number" class="item_qty" value="1" min="1" oninput="calcOrderTotals()"></td>
        <td><input type="number" class="item_price" value="0" step="0.01" oninput="calcOrderTotals()"></td>
        <td><span class="item_total">0</span></td>
        <td><button type="button" class="action-btn action-del" onclick="removeOrderItemRow(this)">ลบ</button></td>
    `;
    tbody.appendChild(row);
}

function removeOrderItemRow(btn) {
    const tr = btn.closest("tr");
    tr.remove();
    calcOrderTotals();
}

function calcOrderTotals() {
    const rows = document.querySelectorAll("#order_items_tbody tr");
    let total = 0;
    rows.forEach(r => {
        const qty = parseFloat(r.querySelector(".item_qty").value || "0");
        const price = parseFloat(r.querySelector(".item_price").value || "0");
        const sum = qty * price;
        r.querySelector(".item_total").textContent = sum.toLocaleString();
        total += sum;
    });
    const discount = parseFloat(document.getElementById("discount").value || "0");
    const net = total - discount;
    document.getElementById("total_amount").value = total.toLocaleString();
    document.getElementById("net_total").value = net.toLocaleString();
}

async function saveOrder(ev) {
    ev.preventDefault();

    const rows = document.querySelectorAll("#order_items_tbody tr");
    if (rows.length === 0) {
        alert("กรุณาเพิ่มรายการสินค้าอย่างน้อย 1 รายการ");
        return;
    }

    const items = [];
    rows.forEach(r => {
        const pid = parseInt(r.querySelector(".item_product_id").value || "0");
        const qty = parseInt(r.querySelector(".item_qty").value || "0");
        const price = parseFloat(r.querySelector(".item_price").value || "0");
        if (pid > 0 && qty > 0 && price >= 0) {
            items.push({
                product_id: pid,
                quantity: qty,
                unit_price: price
            });
        }
    });

    if (items.length === 0) {
        alert("ข้อมูลรายการสินค้าไม่ถูกต้อง");
        return;
    }

    const payload = {
        branch_id: parseInt(document.getElementById("order_branch_id").value),
        customer_id: null, // ถ้าอยากใช้จริงต้องผูก table customers
        discount: parseFloat(document.getElementById("discount").value || "0"),
        payment_status: "paid",
        payment_method: document.getElementById("payment_method").value,
        items: items
    };

    try {
        const res = await fetchJSON("../api/orders/create.php", {
            method:"POST",
            headers:{"Content-Type":"application/json"},
            body: JSON.stringify(payload)
        });

        if (!res.success) {
            alert("บันทึกออเดอร์ไม่สำเร็จ: " + (res.error || ''));
            return;
        }

        closeOrderModal();
        loadOrders();
        if (res.order_id) {
            openReceipt(res.order_id);
        }
    } catch (e) {
        console.error(e);
        alert("เกิดข้อผิดพลาด");
    }
}

document.addEventListener("DOMContentLoaded", () => {
    loadOrders();
});
