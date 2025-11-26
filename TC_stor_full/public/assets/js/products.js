// assets/js/products.js

async function fetchJSON(url, options = {}) {
    const res = await fetch(url, options);
    return await res.json();
}

async function loadBranches() {
    const sel = document.getElementById("branch_filter");
    const selForm = document.getElementById("branch_id");
    try {
        const data = await fetchJSON("../api/branches/list.php");
        if (!data.success) return;
        sel.innerHTML = `<option value="">ทุกสาขา</option>`;
        selForm.innerHTML = "";
        data.data.forEach(b => {
            sel.innerHTML += `<option value="${b.branch_id}">${b.branch_name}</option>`;
            selForm.innerHTML += `<option value="${b.branch_id}">${b.branch_name}</option>`;
        });
    } catch (e) {
        console.error(e);
    }
}

async function loadCategoriesTypes() {
    try {
        const catRes = await fetchJSON("../api/categories/list.php");
        const typeRes = await fetchJSON("../api/types/list.php");

        const catSel = document.getElementById("category_id");
        const typeSel = document.getElementById("type_id");

        catSel.innerHTML = "";
        typeSel.innerHTML = "";

        if (catRes.success) {
            catRes.data.forEach(c => {
                catSel.innerHTML += `<option value="${c.category_id}">${c.category_name}</option>`;
            });
        }
        if (typeRes.success) {
            typeRes.data.forEach(t => {
                typeSel.innerHTML += `<option value="${t.type_id}">${t.type_name}</option>`;
            });
        }
    } catch (e) {
        console.error(e);
    }
}

async function loadProducts() {
    const branchSel = document.getElementById("branch_filter");
    const branch_id = branchSel.value;
    let url = "../api/products/list.php";
    if (branch_id) url += "?branch_id=" + encodeURIComponent(branch_id);

    try {
        const data = await fetchJSON(url);
        if (!data.success) return;

        const tbody = document.getElementById("products_tbody");
        tbody.innerHTML = "";
        data.data.forEach(p => {
            tbody.innerHTML += `
                <tr>
                    <td>${p.product_id}</td>
                    <td>${p.product_name}</td>
                    <td>${p.category_name ?? ''}</td>
                    <td>${p.type_name ?? ''}</td>
                    <td>${parseFloat(p.cost_price).toLocaleString()}</td>
                    <td>${parseFloat(p.sell_price).toLocaleString()}</td>
                    <td>${p.profit_percent ?? 0}</td>
                    <td>${p.stock_qty ?? 0}</td>
                    <td>
                        <button class="action-btn action-edit" onclick="editProduct(${p.product_id})">แก้ไข</button>
                        <button class="action-btn action-del" onclick="deleteProduct(${p.product_id})">ลบ</button>
                    </td>
                </tr>
            `;
        });
    } catch (e) {
        console.error(e);
    }
}

function openProductModal() {
    document.getElementById("product_id").value = "";
    document.getElementById("product_name").value = "";
    document.getElementById("cost_price").value = "";
    document.getElementById("sell_price").value = "";
    document.getElementById("stock_qty").value = "0";
    document.getElementById("barcode").value = "";
    document.getElementById("modal_title").textContent = "เพิ่มสินค้า";
    document.getElementById("product_modal").style.display = "flex";
}

function closeProductModal() {
    document.getElementById("product_modal").style.display = "none";
}

async function editProduct(id) {
    try {
        const data = await fetchJSON("../api/products/get.php?id=" + id);
        if (!data.success) return;

        const p = data.data;
        document.getElementById("product_id").value = p.product_id;
        document.getElementById("product_name").value = p.product_name;
        document.getElementById("category_id").value = p.category_id;
        document.getElementById("type_id").value = p.type_id;
        document.getElementById("cost_price").value = p.cost_price;
        document.getElementById("sell_price").value = p.sell_price;
        document.getElementById("barcode").value = p.barcode ?? "";
        document.getElementById("stock_qty").value = 0; // ไม่แก้สต็อกตรงนี้

        document.getElementById("modal_title").textContent = "แก้ไขสินค้า";
        document.getElementById("product_modal").style.display = "flex";
    } catch (e) {
        console.error(e);
    }
}

async function saveProduct(ev) {
    ev.preventDefault();

    const id = document.getElementById("product_id").value;
    const payload = {
        product_id: id ? parseInt(id) : undefined,
        product_name: document.getElementById("product_name").value,
        category_id: parseInt(document.getElementById("category_id").value),
        type_id: parseInt(document.getElementById("type_id").value),
        cost_price: parseFloat(document.getElementById("cost_price").value),
        sell_price: parseFloat(document.getElementById("sell_price").value),
        stock_qty: parseInt(document.getElementById("stock_qty").value || "0"),
        branch_id: parseInt(document.getElementById("branch_id").value),
        barcode: document.getElementById("barcode").value
    };

    let url = "../api/products/create.php";
    if (id) url = "../api/products/update.php";

    try {
        const res = await fetchJSON(url, {
            method: "POST",
            headers: {"Content-Type":"application/json"},
            body: JSON.stringify(payload)
        });

        if (!res.success) {
            alert("บันทึกไม่สำเร็จ");
            return;
        }

        closeProductModal();
        loadProducts();
    } catch (e) {
        console.error(e);
        alert("เกิดข้อผิดพลาด");
    }
}

async function deleteProduct(id) {
    if (!confirm("ต้องการลบสินค้านี้จริงหรือไม่?")) return;
    try {
        const res = await fetchJSON("../api/products/delete.php?id=" + id);
        if (res.success) {
            loadProducts();
        } else {
            alert("ลบไม่สำเร็จ");
        }
    } catch (e) {
        console.error(e);
        alert("เกิดข้อผิดพลาด");
    }
}

document.addEventListener("DOMContentLoaded", () => {
    loadBranches();
    loadCategoriesTypes();
    loadProducts();
});
