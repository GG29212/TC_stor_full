// assets/js/dashboard.js

async function loadDashboard() {
    const res = await fetch("../api/dashboard/summary.php");
    const data = await res.json();

    if (!data.success) return;

    // ยอดขายวันนี้
    document.getElementById("sales_today").textContent = data.sales.today.amount.toLocaleString();
    document.getElementById("orders_today").textContent = data.sales.today.orders + " ออเดอร์";

    // ยอดขายเดือนนี้
    document.getElementById("sales_month").textContent = data.sales.month.amount.toLocaleString();
    document.getElementById("orders_month").textContent = data.sales.month.orders + " ออเดอร์";

    // กำไร
    document.getElementById("profit").textContent = data.finance.profit.toLocaleString();

    // งาน
    document.getElementById("tasks_open").textContent = data.tasks.open;
    document.getElementById("tasks_delayed").textContent = "ล่าช้า: " + data.tasks.delayed;

    // สินค้าใกล้หมด
    const lowTable = document.querySelector("#low_stock_table tbody");
    lowTable.innerHTML = "";
    data.stock.low_items.forEach(item => {
        lowTable.innerHTML += `
            <tr>
                <td>${item.product_name}</td>
                <td>${item.quantity}</td>
            </tr>
        `;
    });

    // โหลดกราฟ
    loadChart([
        data.sales.today.amount,
        data.sales.month.amount,
        data.finance.profit
    ]);
}

function loadChart(values) {
    const ctx = document.getElementById("salesChart");

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["วันนี้", "เดือนนี้", "กำไรสุทธิ"],
            datasets: [{
                label: "ยอดขาย (บาท)",
                data: values,
                backgroundColor: ["#38bdf8", "#6366f1", "#34d399"]
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

loadDashboard();
