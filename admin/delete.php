<?php
$pageTitle = 'Delete Students';
require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../includes/db.php';

$pdo      = getDB();
$students = $pdo->query("SELECT * FROM students ORDER BY name")->fetchAll();
?>

<div id="alertContainer"></div>

<div class="card">
  <div class="card-header">
    <div class="card-title">All Students</div>
    <button class="btn btn-danger" onclick="deleteSelected()">✕ Delete Selected</button>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th><input type="checkbox" onchange="toggleSelectAll(this)" title="Select All"></th>
          <th>Name</th>
          <th>Student ID</th>
          <th>Card UID</th>
          <th>Registered</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($students)): ?>
          <tr><td colspan="5" style="text-align:center;color:var(--text-dim);padding:40px">No students registered yet.</td></tr>
        <?php else: ?>
          <?php foreach ($students as $s): ?>
          <tr>
            <td><input type="checkbox" class="student-check" value="<?= $s['id'] ?>"></td>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td class="mono"><?= htmlspecialchars($s['student_id']) ?></td>
            <td class="mono"><?= htmlspecialchars($s['uid']) ?></td>
            <td class="text-dim text-sm"><?= date('M j, Y', strtotime($s['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/_footer.php'; ?>
