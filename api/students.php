<?php
require_once __DIR__ . '/../includes/db.php';
corsHeaders();

$pdo    = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// ── GET: list all students ──────────────────────────────────────────────────
if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY name");
    jsonResponse(['status' => 'ok', 'students' => $stmt->fetchAll()]);
}

// ── POST: add student ───────────────────────────────────────────────────────
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $uid   = strtoupper(trim($input['uid']        ?? ''));
    $name  = trim($input['name']       ?? '');
    $sid   = trim($input['student_id'] ?? '');

    if (!$uid || !$name || !$sid) {
        jsonResponse(['status' => 'error', 'message' => 'All fields required'], 400);
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO students (uid, name, student_id) VALUES (?, ?, ?)");
        $stmt->execute([$uid, $name, $sid]);
        jsonResponse(['status' => 'ok', 'message' => 'Student added', 'id' => $pdo->lastInsertId()]);
    } catch (PDOException $e) {
        jsonResponse(['status' => 'error', 'message' => 'UID already registered'], 409);
    }
}

// ── PUT: update student ─────────────────────────────────────────────────────
if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id    = (int)($input['id']         ?? 0);
    $name  = trim($input['name']        ?? '');
    $sid   = trim($input['student_id']  ?? '');

    if (!$id || !$name || !$sid) {
        jsonResponse(['status' => 'error', 'message' => 'All fields required'], 400);
    }

    $stmt = $pdo->prepare("UPDATE students SET name = ?, student_id = ? WHERE id = ?");
    $stmt->execute([$name, $sid, $id]);
    jsonResponse(['status' => 'ok', 'message' => 'Student updated']);
}

// ── DELETE: delete students ─────────────────────────────────────────────────
if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $ids   = array_filter(array_map('intval', $input['ids'] ?? []));

    if (empty($ids)) {
        jsonResponse(['status' => 'error', 'message' => 'No IDs provided'], 400);
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("DELETE FROM students WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    jsonResponse(['status' => 'ok', 'message' => count($ids) . ' student(s) deleted']);
}

jsonResponse(['status' => 'error', 'message' => 'Method not allowed'], 405);
