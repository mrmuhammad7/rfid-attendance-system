const API = {
    scan: "/attendance/api/scan.php",
    stats: "/attendance/api/stats.php",
    students: "/attendance/api/students.php",
    pending: "/attendance/api/pending_scan.php",
};

// ── Generic fetch helpers ────────────────────────────────────
async function apiFetch(url, options = {}) {
    const res = await fetch(url, {
        headers: { "Content-Type": "application/json" },
        ...options,
    });
    return res.json();
}

// ── Format timestamp ─────────────────────────────────────────
function fmtTime(ts) {
    if (!ts) return "";
    const d = new Date(ts);
    return d.toLocaleTimeString("en-US", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
    });
}

// ── Update scan banner ───────────────────────────────────────
let lastUID = null;

function updateScanBanner(data) {
    const banner = document.getElementById("scanBanner");
    if (!banner) return;

    if (!data || !data.uid) {
        banner.className = "scan-banner";
        banner.innerHTML = `
      <div class="scan-dot"></div>
      <div class="scan-uid" style="color:var(--text-dim);font-size:.8rem;letter-spacing:3px">
        WAITING FOR CARD SCAN...
      </div>
      <span class="scan-badge idle">IDLE</span>`;
        return;
    }

    if (data.uid === lastUID) return;
    lastUID = data.uid;

    const ok = data.status === "success";
    banner.className = "scan-banner " + (ok ? "success" : "unknown");
    banner.innerHTML = `
    <div class="scan-dot"></div>
    <div class="scan-uid">${data.uid}</div>
    <div class="scan-info">
      <div class="scan-name">${data.name || "Unknown Card"}</div>
      <div class="scan-meta">${data.student_id || ""} ${data.timestamp ? "· " + fmtTime(data.timestamp) : ""}</div>
    </div>
    <span class="scan-badge ${ok ? "ok" : "bad"}">${ok ? "AUTHORIZED" : "UNKNOWN"}</span>`;

    // Reset after 5s
    setTimeout(() => {
        if (banner) updateScanBanner(null);
    }, 5000);
}

// ── Update stats ─────────────────────────────────────────────
function updateStats(data) {
    const set = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    };
    set("statTotal", data.total ?? "—");
    set("statPresent", data.present ?? "—");
    set("statAbsent", data.absent ?? "—");
}

// ── Render student attendance table ─────────────────────────
function renderTable(students, attendance) {
    const tbody = document.getElementById("attendanceTable");
    if (!tbody) return;
    const today = new Date().toLocaleDateString("en-GB");

    // Create a map of attendance percentages by student_id
    const attendanceMap = {};
    if (attendance && attendance.length > 0) {
        attendance.forEach((a) => {
            attendanceMap[a.student_id] = a.attendance_percentage;
        });
    }

    // Use students data for today's attendance, but add percentage from attendance data
    tbody.innerHTML = students
        .map(
            (s) => `
    <tr>
      <td >${s.name}</td>
      <td class="mono" style="text-align:center">${s.student_id}</td>
      <td class="mono text-dim" style="text-align:center">${today}</td>
      <td style="text-align:center">
        <span class="badge ${s.present ? "present" : "absent"} dot">
          ${s.present ? "Present" : "Absent"}
        </span>
      </td>
      <td style="text-align:center">
        <div style="display:flex;align-items:center;gap:8px;justify-content:center">
          <span style="min-width:40px;color:${(attendanceMap[s.student_id] ?? 0) >= 70 ? "#10b981" : "#ef4444"};font-weight:bold">${attendanceMap[s.student_id] !== undefined ? attendanceMap[s.student_id] + "%" : "-"}</span>
          <div style="width:100px;height:5px;background:#eee;border-radius:4px;overflow:hidden">
            <div style="height:100%;width:${attendanceMap[s.student_id] ?? 0}%;background:${(attendanceMap[s.student_id] ?? 0) >= 70 ? "#10b981" : "#ef4444"};transition:width 0.3s ease"></div>
          </div>
        </div>
      </td>
    </tr>`,
        )
        .join("");
}

// ── Poll scan & stats ────────────────────────────────────────
async function pollDashboard() {
    try {
        const [scanData, statsData] = await Promise.all([
            apiFetch(API.scan),
            apiFetch(API.stats),
        ]);
        updateScanBanner(scanData);
        updateStats(statsData);
        renderTable(statsData.students || [], statsData.attendance || []);
    } catch (e) {
        console.warn("Poll error:", e);
    }
}

// ── Poll pending scan (for Admin Add/Edit pages) ─────────────
let pendingCallback = null;

async function pollPendingScan() {
    try {
        const data = await apiFetch(API.pending);
        if (data.uid && pendingCallback) {
            pendingCallback(data.uid);
        }
    } catch (e) {}
}

function onPendingScan(cb) {
    pendingCallback = cb;
    // Clear any existing pending scan when starting to listen
    apiFetch(API.pending, { method: "DELETE" }).catch(() => {});
    setInterval(pollPendingScan, 1000);
}

// ── Delete selected students ─────────────────────────────────
async function deleteSelected() {
    const checks = [...document.querySelectorAll(".student-check:checked")];
    if (!checks.length) return alert("Select at least one student.");

    const ids = checks.map((c) => parseInt(c.value));
    if (!confirm(`Delete ${ids.length} student(s)? This cannot be undone.`))
        return;

    try {
        const res = await apiFetch(API.students, {
            method: "DELETE",
            body: JSON.stringify({ ids }),
        });
        if (res.status === "ok") {
            showAlert("success", res.message);
            checks.forEach((c) => c.closest("tr").remove());
            // update count
            const remaining =
                document.querySelectorAll(".student-check").length;
            const el = document.getElementById("statTotal");
            if (el) el.textContent = remaining;
        } else {
            showAlert("error", res.message);
        }
    } catch (e) {
        showAlert("error", "Request failed");
    }
}

// ── Select all checkbox ──────────────────────────────────────
function toggleSelectAll(master) {
    document
        .querySelectorAll(".student-check")
        .forEach((c) => (c.checked = master.checked));
}

// ── Show alert ───────────────────────────────────────────────
function showAlert(type, msg) {
    const container = document.getElementById("alertContainer");
    if (!container) return;
    const div = document.createElement("div");
    div.className = `alert alert-${type}`;
    div.textContent = msg;
    container.prepend(div);
    setTimeout(() => div.remove(), 4000);
}

// ── Start dashboard polling ──────────────────────────────────
function startDashboard(intervalMs = 2000) {
    pollDashboard();
    setInterval(pollDashboard, intervalMs);
}
