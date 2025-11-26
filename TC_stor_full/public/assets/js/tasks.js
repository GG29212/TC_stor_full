// assets/js/tasks.js

async function fetchJSON(url, options = {}) {
    const res = await fetch(url, options);
    return await res.json();
}

async function loadTasks() {
    try {
        const data = await fetchJSON("../api/tasks/list.php");
        if (!data.success) return;

        const tbody = document.getElementById("tasks_tbody");
        tbody.innerHTML = "";

        data.data.forEach(t => {
            let statusLabel = t.status;
            if (t.status === "waiting") statusLabel = "รอทำ";
            else if (t.status === "in_progress") statusLabel = "กำลังทำ";
            else if (t.status === "delayed") statusLabel = "ล่าช้า";
            else if (t.status === "done") statusLabel = "เสร็จแล้ว";

            tbody.innerHTML += `
                <tr>
                    <td>${t.task_id}</td>
                    <td>${t.title ?? ""}</td>
                    <td>${t.order_id ?? "-"}</td>
                    <td>${t.technician ?? "-"}</td>
                    <td>${statusLabel}</td>
                    <td>${t.created_at ?? ""}</td>
                    <td>
                        <button class="action-btn action-assign" onclick="assignTask(${t.task_id})">มอบหมาย</button>
                        <button class="action-btn action-status" onclick="changeStatus(${t.task_id})">เปลี่ยนสถานะ</button>
                        <button class="action-btn action-done" onclick="markDone(${t.task_id})">เสร็จแล้ว</button>
                    </td>
                </tr>
            `;
        });
    } catch (e) {
        console.error(e);
    }
}

function openTaskModal() {
    document.getElementById("task_title").value = "";
    document.getElementById("task_description").value = "";
    document.getElementById("task_order_id").value = "";
    document.getElementById("task_modal").style.display = "flex";
}

function closeTaskModal() {
    document.getElementById("task_modal").style.display = "none";
}

async function saveTask(ev) {
    ev.preventDefault();

    const payload = {
        title: document.getElementById("task_title").value,
        description: document.getElementById("task_description").value,
        order_id: document.getElementById("task_order_id").value || null
    };

    if (!payload.title) {
        alert("กรุณากรอกหัวข้องาน");
        return;
    }

    try {
        const res = await fetchJSON("../api/tasks/create.php", {
            method: "POST",
            headers: {"Content-Type":"application/json"},
            body: JSON.stringify(payload)
        });
        if (!res.success) {
            alert("บันทึกงานไม่สำเร็จ");
            return;
        }
        closeTaskModal();
        loadTasks();
    } catch (e) {
        console.error(e);
        alert("เกิดข้อผิดพลาด");
    }
}

async function assignTask(task_id) {
    const emp_id = prompt("กรอกรหัสช่าง (emp_id) ที่ต้องการมอบหมาย:");
    if (!emp_id) return;

    const payload = {
        task_id: task_id,
        emp_id: parseInt(emp_id)
    };

    try {
        const res = await fetchJSON("../api/tasks/assign.php", {
            method:"POST",
            headers:{"Content-Type":"application/json"},
            body: JSON.stringify(payload)
        });

        if (!res.success) {
            alert("มอบหมายงานไม่สำเร็จ");
            return;
        }
        loadTasks();
    } catch (e) {
        console.error(e);
        alert("เกิดข้อผิดพลาด");
    }
}

async function changeStatus(task_id, passed_status = null) {
    const choice = passed_status || prompt("สถานะใหม่: waiting / in_progress / delayed / done");
    if (!choice) return;

    const valid = ["waiting","in_progress","delayed","done"];
    if (!valid.includes(choice)) {
        alert("ค่าสถานะไม่ถูกต้อง");
        return;
    }

    const payload = { task_id: task_id, status: choice };

    try {
        const res = await fetchJSON("../api/tasks/update_status.php", {
            method:"POST",
            headers:{"Content-Type":"application/json"},
            body: JSON.stringify(payload)
        });

        if (!res.success) {
            alert("เปลี่ยนสถานะไม่สำเร็จ");
            return;
        }
        loadTasks();
    } catch (e) {
        console.error(e);
        alert("เกิดข้อผิดพลาด");
    }
}

function markDone(task_id) {
    changeStatus(task_id, "done");
}

document.addEventListener("DOMContentLoaded", () => {
    loadTasks();
});
