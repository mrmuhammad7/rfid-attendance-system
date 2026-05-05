<?php
require_once __DIR__ . '/../includes/db.php';
corsHeaders();

// ── GET: fetch latest scan for real-time dashboard polling ──────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $pdo  = getDB();
    $today = date('Y-m-d');

    // Return last scan event stored in a temp file (lightweight real-time)
    $cacheFile = sys_get_temp_dir() . '/rfid_last_scan.json';
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 30) {
        header('Content-Type: application/json');
        echo file_get_contents($cacheFile);
    } else {
        jsonResponse(['uid' => null]);
    }
    exit;
}

// ── POST: ESP8266 sends UID ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $uid   = strtoupper(trim($input['uid'] ?? ''));

    if (empty($uid)) {
        jsonResponse(['status' => 'error', 'message' => 'UID required'], 400);
    }

    $pdo   = getDB();
    $today = date('Y-m-d');

    // Find student
    $stmt = $pdo->prepare("SELECT * FROM students WHERE uid = ?");
    $stmt->execute([$uid]);
    $student = $stmt->fetch();

    if (!$student) {
        $result = [
            'status'    => 'unknown',
            'uid'       => $uid,
            'message'   => 'Card not registered',
            'timestamp' => date('Y-m-d H:i:s'),
        ];
        file_put_contents(sys_get_temp_dir() . '/rfid_last_scan.json', json_encode($result));
        jsonResponse($result);
    }

    // Mark attendance (once per day)
    $check = $pdo->prepare("SELECT id FROM attendance WHERE student_id = ? AND date = ?");
    $check->execute([$student['id'], $today]);
    $alreadyMarked = $check->fetch();

    if (!$alreadyMarked) {
        $ins = $pdo->prepare("INSERT INTO attendance (student_id, date, status) VALUES (?, ?, 'present')");
        $ins->execute([$student['id'], $today]);
    }

    $result = [
        'status'     => 'success',
        'uid'        => $uid,
        'name'       => $student['name'],
        'student_id' => $student['student_id'],
        'known'      => true,
        'already'    => (bool)$alreadyMarked,
        'timestamp'  => date('Y-m-d H:i:s'),
    ];

    file_put_contents(sys_get_temp_dir() . '/rfid_last_scan.json', json_encode($result));
    jsonResponse($result);
}

jsonResponse(['status' => 'error', 'message' => 'Method not allowed'], 405);
