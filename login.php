<?php
require_once __DIR__ . '/includes/auth.php';
if (isLoggedIn()) {
  header('Location: /attendance/admin/');
  exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (login($_POST['mail'] ?? '', $_POST['password'] ?? '')) {
    header('Location: /attendance/admin/');
    exit;
  }
  $error = 'Invalid email or password.';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="author" content="Mazen Mohamed & Muhammad Mahmoud">
  <title>Admin Login — Attendance</title>
  <link rel="website icon" href="assets/images/icon.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/attendance/assets/css/style.css">
</head>

<body>
  <div style="position:fixed;top:20px;right:20px;z-index:100">
    <button id="themeToggle" title="Toggle theme">
      <span id="themeIcon"></span>
    </button>
  </div>
  <div class="login-wrap">
    <div class="login-box">
      <div class="brand">
        <div class="mono-tag">// RFID SYSTEM</div>
        <h1>Admin <span style="color:var(--accent)">Login</span></h1>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group ">
          <label class="form-label">Email Address</label>
          <input class="form-input" type="email" name="mail" placeholder="admin@attendance.com" required
            value="<?= htmlspecialchars($_POST['mail'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <input class="form-input" type="password" name="password" placeholder="••••••••" required>
        </div>
        <button class="btn btn-primary w-full" style="justify-content:center;margin-top:8px" type="submit">
          Sign In →
        </button>
      </form>

      <div style="margin-top:24px;text-align:center">
        <a href="/attendance/" class="text-dim2 text-sm">← View Student Dashboard</a>
      </div>
    </div>
  </div>

  <script src="/attendance/assets/js/theme.js"></script>
</body>

</html>