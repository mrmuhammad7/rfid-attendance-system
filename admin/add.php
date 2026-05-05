<?php
$pageTitle = 'Add Student';
require_once __DIR__ . '/_header.php';
?>

<div id="alertContainer"></div>

<!-- Phase 1: Waiting for scan -->
<div id="phaseWait" class="card" style="margin-bottom:24px">
  <div class="card-header">
    <div class="card-title">Step 1 — Scan New Card</div>
    <span class="scan-badge idle">WAITING</span>
  </div>
  <div class="scan-waiting">
    <div class="rfid-ring"><span class="rfid-ring-icon">⊛</span></div>
    <div class="scan-waiting-text">Place card on the RFID reader</div>
    <div style="font-family:'DM Mono',monospace;font-size:.65rem;color:var(--text-dim);letter-spacing:2px">
      ESP8266 will detect the card automatically
    </div>
  </div>
</div>

<!-- Phase 2: Form (hidden until scan) -->
<div id="phaseForm" class="card" style="display:none">
  <div class="card-header">
    <div class="card-title">Step 2 — Enter Student Info</div>
    <button class="btn btn-ghost" style="font-size:.78rem" onclick="resetToScan()">↩ Scan Again</button>
  </div>
  <div class="card-body" style="max-width:480px">
    <div class="form-group">
      <label class="form-label">Card UID (Auto-filled)</label>
      <input class="form-input uid-field" type="text" id="fieldUID" disabled>
    </div>
    <div class="form-group">
      <label class="form-label">Student Full Name</label>
      <input class="form-input" type="text" id="fieldName" placeholder="e.g. Mohamed Ali" required>
    </div>
    <div class="form-group">
      <label class="form-label">Student ID</label>
      <input class="form-input" type="text" id="fieldSID" placeholder="e.g. 23/121540" required>
    </div>
    <div class="flex gap-2">
      <button class="btn btn-success" onclick="submitStudent()">Save Student</button>
      <button class="btn btn-ghost"   onclick="resetToScan()">Cancel</button>
    </div>
  </div>
</div>

<?php
$extraScript = <<<'JS'
<script>
function resetToScan() {
  document.getElementById('phaseWait').style.display = '';
  document.getElementById('phaseForm').style.display = 'none';
  document.getElementById('fieldUID').value  = '';
  document.getElementById('fieldName').value = '';
  document.getElementById('fieldSID').value  = '';
  pendingCallback = null;
  // restart polling
  onPendingScan(handleScan);
}

function handleScan(uid) {
  // Check if UID is already registered
  fetch('/attendance/api/students.php')
    .then(r => r.json())
    .then(data => {
      const exists = (data.students || []).find(s => s.uid === uid);
      if (exists) {
        showAlert('error', `UID ${uid} is already registered to "${exists.name}"`);
        return;
      }
      document.getElementById('fieldUID').value = uid;
      document.getElementById('phaseWait').style.display = 'none';
      document.getElementById('phaseForm').style.display = '';
      pendingCallback = null; // stop polling
    });
}

async function submitStudent() {
  const uid  = document.getElementById('fieldUID').value.trim();
  const name = document.getElementById('fieldName').value.trim();
  const sid  = document.getElementById('fieldSID').value.trim();

  if (!uid || !name || !sid) { showAlert('error', 'All fields are required.'); return; }

  const res = await apiFetch('/attendance/api/students.php', {
    method: 'POST',
    body: JSON.stringify({ uid, name, student_id: sid })
  });

  if (res.status === 'ok') {
    showAlert('success', `Student "${name}" added successfully!`);
    setTimeout(resetToScan, 1500);
  } else {
    showAlert('error', res.message || 'Failed to add student');
  }
}

// Start listening for card scan
onPendingScan(handleScan);
</script>
JS;
require_once __DIR__ . '/_footer.php';
?>
