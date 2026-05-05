<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/_header.php';
$today = date('l, F j, Y');
?>

<!-- Live scan banner -->
<div id="scanBanner" class="scan-banner">
  <div class="scan-dot"></div>
  <div class="scan-uid" style="color:var(--text-dim);font-size:.8rem;letter-spacing:3px">WAITING FOR CARD SCAN...</div>
  <span class="scan-badge idle">IDLE</span>
</div>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card blue">
    <div class="stat-label">Total Students</div>
    <div class="stat-value" id="statTotal">—</div>
    <div class="stat-sub">Registered in DB</div>
  </div>
  <div class="stat-card green">
    <div class="stat-label">Present Today</div>
    <div class="stat-value" id="statPresent">—</div>
    <div class="stat-sub"><?= $today ?></div>
  </div>
  <div class="stat-card red">
    <div class="stat-label">Absent Today</div>
    <div class="stat-value" id="statAbsent">—</div>
    <div class="stat-sub">Not scanned yet</div>
  </div>
</div>

<!-- Table -->
<div class="card">
  <div class="card-header">
    <div class="card-title">Today's Attendance</div>
    <div class="text-mono text-sm text-dim"><?= $today ?></div>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Student Name</th>
          <th style="text-align:center">Student ID</th>
          <th style="text-align:center">Date</th>
          <th style="text-align:center">Status</th>
          <th style="text-align:center">Percentage</th>
        </tr>
      </thead>
      <tbody id="attendanceTable">
        <tr>
          <td colspan="5" style="text-align:center;color:var(--text-dim);padding:40px">Loading...</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<?php
$extraScript = '<script>startDashboard(2000);</script>';
require_once __DIR__ . '/_footer.php';
?>