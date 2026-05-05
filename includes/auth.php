<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// isLoggedIn
// ============================================
function isLoggedIn(): bool {
    return isset($_SESSION['admin_id']) 
        && isset($_SESSION['loggedin']) 
        && $_SESSION['loggedin'] === true;
}

// ============================================
// requireLogin
// ============================================
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /attendance/login.php');
        exit;
    }
}

// ============================================
// login
// ============================================
function login(string $mail, string $password): bool {
    require_once __DIR__ . '/db.php';
    $pdo  = getDB();
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE mail = ?");
    $stmt->execute([$mail]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_mail'] = $admin['mail'];
        $_SESSION['loggedin']   = true;
        return true;
    }
    return false;
}

// ============================================
// logout
// ============================================
function logout(): void {
    session_destroy();
    header('Location: /attendance/');
    exit;
}