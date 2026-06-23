<?php
require_once '../includes/db.php';

$msg = "";
$edit_data = null;

if (isset($_POST['action']) && $_POST['action'] === 'insert') {
    $sid = mysqli_real_escape_string($conn, trim($_POST['student_id']));
    $cid = intval($_POST['course_id']);
    $grade = mysqli_real_escape_string($conn, trim($_POST['grade']));
    $date  = mysqli_real_escape_string($conn, $_POST['enrolled_on']);

    $sql = "INSERT INTO Enrollments (student_id,course_id,grade,enrolled_on)
            VALUES ('$sid',$cid,'" . ($grade ?: 'NULL') . "','$date')";
    $msg = mysqli_query($conn, $sql)
        ? "success|Enrollment added successfully!"
        : "error|" . mysqli_error($conn);
}

if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $eid   = intval($_POST['enrollment_id']);
    $grade = mysqli_real_escape_string($conn, trim($_POST['grade']));
    $date  = mysqli_real_escape_string($conn, $_POST['enrolled_on']);

    $sql = "UPDATE Enrollments SET grade='$grade', enrolled_on='$date' WHERE enrollment_id=$eid";
    $msg = mysqli_query($conn, $sql)
        ? "success|Enrollment updated!"
        : "error|" . mysqli_error($conn);
}

if (isset($_GET['delete'])) {
    $id  = intval($_GET['delete']);
    $msg = mysqli_query($conn, "DELETE FROM Enrollments WHERE enrollment_id=$id")
        ? "success|Enrollment removed."
        : "error|" . mysqli_error($conn);
}

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $r  = mysqli_query($conn, "SELECT * FROM Enrollments WHERE enrollment_id=$id");
    $edit_data = mysqli_fetch_assoc($r);
}

$enrollments = mysqli_query($conn,
    "SELECT e.*, s.name AS student_name, c.course_name, c.course_code
     FROM Enrollments e
     JOIN Students s ON e.student_id = s.student_id
     JOIN Courses  c ON e.course_id  = c.course_id
     ORDER BY e.enrollment_id DESC");

$student_list = [];
$r = mysqli_query($conn, "SELECT student_id, name FROM Students ORDER BY name");
while ($row = mysqli_fetch_assoc($r)) $student_list[] = $row;

$course_list = [];
$r = mysqli_query($conn, "SELECT course_id, course_code, course_name FROM Courses ORDER BY course_code");
while ($row = mysqli_fetch_assoc($r)) $course_list[] = $row;

$grades = ['A+','A','A-','B+','B','B-','C+','C','D','F'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Enrollments — SMS</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/nav.php'; ?>
<main class="page-content">
  <div class="page-header">
    <h1 class="page-title">Enrollments &amp; Grades</h1>
    <button class="btn btn-primary" onclick="toggleForm()">+ Enroll Student</button>
  </div>

  <?php if ($msg): [$type,$text] = explode('|',$msg,2); ?>
  <div class="alert alert-<?= $type ?>"><?= $text ?></div><?php endif; ?>

  <div class="card form-card <?= $edit_data ? 'open' : '' ?>" id="enrollForm">
    <h2 class="card-title"><?= $edit_data ? 'Update Grade / Date' : 'New Enrollment' ?></h2>
    <form method="POST">
      <input type="hidden" name="action" value="<?= $edit_data ? 'update' : 'insert' ?>">
      <?php if ($edit_data): ?>
      <input type="hidden" name="enrollment_id" value="<?= $edit_data['enrollment_id'] ?>">
      <?php endif; ?>
      <div class="form-grid">
        <?php if (!$edit_data): ?>
        <div class="form-group">
          <label>Student *</label>
          <select name="student_id" required>
            <option value="">— Select Student —</option>
            <?php foreach ($student_list as $s): ?>
            <option value="<?= htmlspecialchars($s['student_id']) ?>">
              <?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['student_id']) ?>)
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Course *</label>
          <select name="course_id" required>
            <option value="">— Select Course —</option>
            <?php foreach ($course_list as $c): ?>
            <option value="<?= $c['course_id'] ?>">
              [<?= htmlspecialchars($c['course_code']) ?>] <?= htmlspecialchars($c['course_name']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>
        <div class="form-group">
          <label>Grade</label>
          <select name="grade">
            <option value="">Not graded yet</option>
            <?php foreach ($grades as $g): ?>
            <option value="<?= $g ?>" <?= ($edit_data['grade'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Enrolled On</label>
          <input type="date" name="enrolled_on"
                 value="<?= $edit_data['enrolled_on'] ?? date('Y-m-d') ?>">
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $edit_data ? 'Update' : 'Enroll' ?></button>
        <a href="enrollments.php" class="btn btn-ghost">Cancel</a>
      </div>
    </form>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>#</th><th>Student</th><th>Course</th><th>Grade</th><th>Enrolled On</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php while ($e = mysqli_fetch_assoc($enrollments)): ?>
          <tr>
            <td><?= $e['enrollment_id'] ?></td>
            <td><?= htmlspecialchars($e['student_name']) ?></td>
            <td><span class="badge badge-blue"><?= htmlspecialchars($e['course_code']) ?></span> <?= htmlspecialchars($e['course_name']) ?></td>
            <td>
              <?php if ($e['grade']): ?>
              <span class="grade-badge grade-<?= strtolower(str_replace('+','-plus',str_replace('-','-minus',$e['grade']))) ?>">
                <?= htmlspecialchars($e['grade']) ?>
              </span>
              <?php else: ?>
              <span class="text-muted">Pending</span>
              <?php endif; ?>
            </td>
            <td><?= $e['enrolled_on'] ?></td>
            <td class="actions">
              <a href="?edit=<?= $e['enrollment_id'] ?>" class="btn btn-sm btn-edit">Edit</a>
              <a href="?delete=<?= $e['enrollment_id'] ?>" class="btn btn-sm btn-delete"
                 onclick="return confirm('Remove this enrollment?')">Delete</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<script>function toggleForm(){document.getElementById('enrollForm').classList.toggle('open');}</script>
</body>
</html>
