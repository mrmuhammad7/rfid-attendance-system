<?php
$pageTitle = 'Edit Student';
require_once __DIR__ . '/_header.php';
?>

<div id="alertContainer"></div>

<!-- Phase 1: Scan -->
<div id="phaseWait" class="card" style="margin-bottom:24px">
  <div class="card-header">
    <div class="card-title">Scan Student Card to Edit</div>
    <span class="scan-badge idle">WAITING</span>
  </div>
  <div class="scan-waiting">
    <div class="rfid-ring"><span class="rfid-ring-icon">⊛</span></div>
    <div class="scan-waiting-text">Place the student's card on the reader</div>
  </div>
</div>

<!-- Phase 2: Edit form -->
<div id="phaseForm" class="card" style="display:none">
  <div class="card-header">
    <div class="card-title">Edit Student</div>
    <button class="btn btn-ghost" style="font-size:.78rem" onclick="resetToScan()">↩ Scan Again</button>
  </div>
  <div class="card-body" style="max-width:480px">
    <input type="hidden" id="fieldID">
    <div class="form-group">
      <label class="form-label">Card UID (Cannot be changed)</label>
      <input class="form-input uid-field" type="text" id="fieldUID" disabled>
    </div>
    <div class="form-group">
      <label class="form-label">Student Full Name</label>
      <input class="form-input" type="text" id="fieldName" required>
    </div>
    <div class="form-group">
      <label class="form-label">Student ID</label>
      <input class="form-input" type="text" id="fieldSID" required>
    </div>
    <div class="flex gap-2">
      <button class="btn btn-primary" onclick="submitEdit()">Update Student</button>
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
  pendingCallback = null;
  onPendingScan(handleScan);
}

function handleScan(uid) {
  fetch('/attendance/api/students.php')
    .then(r => r.json())
    .then(data => {
      const student = (data.students || []).find(s => s.uid === uid);
      if (!student) {
        showAlert('error', `UID ${uid} is not registered. Use "Add Student" first.`);
        return;
      }
      document.getElementById('fieldID').value   = student.id;
      document.getElementById('fieldUID').value  = student.uid;
      document.getElementById('fieldName').value = student.name;
      document.getElementById('fieldSID').value  = student.student_id;
      document.getElementById('phaseWait').style.display = 'none';
      document.getElementById('phaseForm').style.display = '';
      pendingCallback = null;
    });
}

async function submitEdit() {
  const id   = parseInt(document.getElementById('fieldID').value);
  const name = document.getElementById('fieldName').value.trim();
  const sid  = document.getElementById('fieldSID').value.trim();

  if (!name || !sid) { showAlert('error', 'All fields required.'); return; }

  const res = await apiFetch('/attendance/api/students.php', {
    method: 'PUT',
    body: JSON.stringify({ id, name, student_id: sid })
  });

  if (res.status === 'ok') {
    showAlert('success', 'Student updated successfully!');
    setTimeout(resetToScan, 1500);
  } else {
    showAlert('error', res.message || 'Update failed');
  }
}

onPendingScan(handleScan);
</script>
JS;
require_once __DIR__ . '/_footer.php';
?>
