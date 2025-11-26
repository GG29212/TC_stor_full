// assets/js/finance.js

async function fetchJSON(url, options = {}) {
    const res = await fetch(url, options);
    return await res.json();
}

async function loadFinanceSummary() {
    try {
        const data = await fetchJSON("../api/finance/summary.php");
        if (!data.success) return;

        document.getElementById("income_total").textContent  = data.income.toLocaleString();
        document.getElementById("expense_total").textContent = data.expense.toLocaleString();
        document.getElementById("profit_total").textContent  = data.profit.toLocaleString();
    } catch (e) {
        console.error(e);
    }
}

async function loadFinanceList() {
    try {
        const data = await fetchJSON("../api/finance/list.php");
        if (!data.success) return;

        const tbody = document.getElementById("finance_tbody");
        tbody.innerHTML = "";

        data.data.forEach(row => {
            const typeLabel = row.type === "income" ? "รายรับ" : "รายจ่าย";
            tbody.innerHTML += `
                <tr>
                    <td>${row.finance_id}</td>
                    <td>${typeLabel}</td>
                    <td>${parseFloat(row.amount).toLocaleString()}</td>
                    <td>${row.description ?? ""}</td>
                    <td>${row.payment_method ?? ""}</td>
                    <td>${row.created_at ?? ""}</td>
                </tr>
            `;
        });
    } catch (e) {
        console.error(e);
    }
}

async function saveFinance(ev) {
    ev.preventDefault();

    const payload = {
        type: document.getElementById("finance_type").value,
        amount: parseFloat(document.getElementById("finance_amount").value || "0"),
        method: document.getElementById("finance_method").value,
        description: document.getElementById("finance_desc").value,
        branch_id: 1
    };

    if (!payload.amount || payload.amount <= 0) {
        alert("กรุณากรอกจำนวนเงินให้ถูกต้อง");
        return;
    }

    try {
        const res = await fetchJSON("../api/finance/create.php", {
            method: "POST",
            headers: {"Content-Type":"application/json"},
            body: JSON.stringify(payload)
        });

        if (!res.success) {
            alert("บันทึกรายการการเงินไม่สำเร็จ");
            return;
        }

        // เคลียร์ฟอร์ม
        document.getElementById("finance_amount").value = "";
        document.getElementById("finance_desc").value = "";

        // โหลดใหม่
        loadFinanceSummary();
        loadFinanceList();
    } catch (e) {
        console.error(e);
        alert("เกิดข้อผิดพลาด");
    }
}

function exportFinance() {
    window.location.href = "../api/finance/export_csv.php";
}

document.addEventListener("DOMContentLoaded", () => {
    loadFinanceSummary();
    loadFinanceList();
});
