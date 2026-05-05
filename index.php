<?php
$today = date('l, F j, Y');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="author" content="Mazen Mohamed & Muhammad Mahmoud">
  <title>Attendance Dashboard</title>
  <link rel="website icon" href="assets/images/icon.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/attendance/assets/css/style.css">
</head>

<body>
  <div class="guest-layout" style="padding-top:60px">

    <div class="guest-header" style="position:fixed;top:0;left:0;right:0;z-index:100;">
      <a href="#" class="brand-name"><img src="assets/images/icon.png" height="30px"
          style="vertical-align:middle;margin-right:8px">Devora <span>Attendance</span></a>
      <div class="flex items-center gap-3">
        <button id="themeToggle" title="Toggle theme">
          <span id="themeIcon"></span>
        </button>
        <span class="guest-badge">STUDENT VIEW</span>
        <a href="/attendance/login.php" class="btn btn-ghost" style="padding:6px 14px;font-size:.78rem">
          Admin →
        </a>
      </div>
    </div>

    <div class="guest-content">

      <!-- Live scan banner -->
      <div id="scanBanner" class="scan-banner">
        <div class="scan-dot"></div>
        <div class="scan-uid" style="color:var(--text-dim);font-size:.8rem;letter-spacing:3px">WAITING FOR CARD SCAN...
        </div>
        <span class="scan-badge idle">IDLE</span>
      </div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card blue">
          <div class="stat-label">Total Students</div>
          <div class="stat-value" id="statTotal">—</div>
          <div class="stat-sub">Registered</div>
        </div>
        <div class="stat-card green">
          <div class="stat-label">Present Today</div>
          <div class="stat-value" id="statPresent">—</div>
          <div class="stat-sub"><?= $today ?></div>
        </div>
        <div class="stat-card red">
          <div class="stat-label">Absent Today</div>
          <div class="stat-value" id="statAbsent">—</div>
          <div class="stat-sub">Not yet scanned</div>
        </div>
      </div>

      <!-- Attendance table -->
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

    </div>
  </div>

  <footer>
    <div class="footer-content">
      <p>&copy; 2026 <span style="color: var(--accent);">Devora</span> Attendance. All rights reserved.</p>
    </div>
  </footer>

  <script src="/attendance/assets/js/theme.js"></script>
  <script src="/attendance/assets/js/app.js"></script>
  <script>startDashboard(2000);</script>
</body>

</html>