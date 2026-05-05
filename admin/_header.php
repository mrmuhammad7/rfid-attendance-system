<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="author" content="Mazen Mohamed & Muhammad Mahmoud">
  <title><?= $pageTitle ?? 'Admin' ?> — RFID Attendance</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/attendance/assets/css/style.css">
  <link rel="website icon" href="../assets/images/icon.png">
</head>

<body>
  <div class="layout">

    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-brand">
        <div class="logo-mark">// ESP8266</div>
        <h2>RFID <span>Attendance</span></h2>
      </div>

      <nav class="sidebar-nav">
        <div class="nav-section-label">Overview</div>
        <a href="/attendance/admin/" class="nav-item <?= $currentPage === 'index' ? 'active' : '' ?>">
          <span class="nav-icon">▦</span> Dashboard
        </a>

        <div class="nav-section-label">Students</div>
        <a href="/attendance/admin/add.php" class="nav-item <?= $currentPage === 'add' ? 'active' : '' ?>">
          <span class="nav-icon">＋</span> Add Student
        </a>
        <a href="/attendance/admin/edit.php" class="nav-item <?= $currentPage === 'edit' ? 'active' : '' ?>">
          <span class="nav-icon">✎</span> Edit Student
        </a>
        <a href="/attendance/admin/delete.php" class="nav-item <?= $currentPage === 'delete' ? 'active' : '' ?>">
          <span class="nav-icon">✕</span> Delete Students
        </a>
      </nav>

      <div class="sidebar-footer">
        <div class="admin-pill">
          <div class="admin-avatar"><?= strtoupper(substr($_SESSION['admin_mail'], 0, 2)) ?></div>
          <div class="admin-info">
            <div class="admin-role">Admin</div>
            <div class="admin-email"><?= htmlspecialchars($_SESSION['admin_mail']) ?></div>
          </div>
          <a href="/attendance/logout.php" class="logout-btn" title="Logout">⏻</a>
        </div>
      </div>
    </aside>

    <!-- Main -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-title"><?= $pageTitle ?? 'Dashboard' ?></div>
        <div style="display:flex;align-items:center;gap:16px">
          <button id="themeToggle" title="Toggle theme">
            <span id="themeIcon"></span>
          </button>
          <div class="topbar-meta" id="liveClock"></div>
        </div>
      </div>
      <div class="page-content">