<?php
require_once __DIR__ . '/../includes/db.php';
corsHeaders();

$pdo = getDB();
$today = date('Y-m-d');

$total = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$present = $pdo->prepare("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE date = ? AND status = 'present'");
$present->execute([$today]);
$present = $present->fetchColumn();

// Attendance list for today
$rows = $pdo->prepare("
    SELECT s.name, s.student_id,
           CASE WHEN a.id IS NOT NULL THEN 1 ELSE 0 END AS present
    FROM students s
    LEFT JOIN attendance a ON a.student_id = s.id AND a.date = ?
    ORDER BY s.name
");
$rows->execute([$today]);
$students = $rows->fetchAll();

$attendance = $pdo->query("
SELECT 
	s.name,
    s.student_id,
    COUNT(DISTINCT a.date)	AS present_days,
    ROUND(COUNT(DISTINCT a.date) * 100.0 / 
    (SELECT COUNT(DISTINCT a.date) FROM attendance a)) AS attendance_percentage
FROM attendance a
RIGHT JOIN students s ON a.student_id = s.id
GROUP BY student_id
")->fetchAll();

$rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

jsonResponse([
    'total' => (int) $total,
    'present' => (int) $present,
    'absent' => (int) ($total - $present),
    'rate' => $rate,
    'date' => $today,
    'students' => $students,
    'attendance' => $attendance,
]);