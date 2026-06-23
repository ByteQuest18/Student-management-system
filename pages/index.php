<?php
require_once '../includes/db.php';

$total_students   = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM Students"))[0];
$total_courses    = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM Courses"))[0];
$total_enrollments= mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM Enrollments"))[0];
$total_teachers   = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM Teachers"))[0];

$recent_students = mysqli_query($conn,
    "SELECT s.name, s.student_id, d.dept_name, s.semester, s.created_at
     FROM Students s LEFT JOIN Departments d ON s.dept_id=d.dept_id
     ORDER BY s.created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard — Student Management System</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/nav.php'; ?>
<main class="page-content">
  <div class="page-header">
    <div>
      <h1 class="page-title">Dashboard</h1>
      <p class="page-sub">Welcome to the Student Management System</p>
    </div>
  </div>

  <div class="stats-grid">
    <div class="stat-card stat-green">
      <div class="stat-icon">🎓</div>
      <div class="stat-value"><?= $total_students ?></div>
      <div class="stat-label">Students</div>
      <a href="students.php" class="stat-link">Manage →</a>
    </div>
    <div class="stat-card stat-blue">
      <div class="stat-icon">📚</div>
      <div class="stat-value"><?= $total_courses ?></div>
      <div class="stat-label">Courses</div>
      <a href="courses.php" class="stat-link">Manage →</a>
    </div>
    <div class="stat-card stat-orange">
      <div class="stat-icon">📋</div>
      <div class="stat-value"><?= $total_enrollments ?></div>
      <div class="stat-label">Enrollments</div>
      <a href="enrollments.php" class="stat-link">Manage →</a>
    </div>
    <div class="stat-card stat-purple">
      <div class="stat-icon">👩‍🏫</div>
      <div class="stat-value"><?= $total_teachers ?></div>
      <div class="stat-label">Teachers</div>
    </div>
  </div>

  <div class="card">
    <h2 class="card-title">Recently Added Students</h2>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Student ID</th><th>Name</th><th>Department</th><th>Semester</th><th>Added</th></tr>
        </thead>
        <tbody>
          <?php while ($s = mysqli_fetch_assoc($recent_students)): ?>
          <tr>
            <td><span class="badge"><?= htmlspecialchars($s['student_id']) ?></span></td>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td><?= htmlspecialchars($s['dept_name'] ?? '—') ?></td>
            <td>Sem <?= $s['semester'] ?></td>
            <td><?= date('d M Y', strtotime($s['created_at'])) ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
</body>
</html>
