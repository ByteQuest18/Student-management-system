<?php
require_once '../includes/db.php';

$msg = "";
$edit_data = null;

if (isset($_POST['action']) && $_POST['action'] === 'insert') {
    $code   = mysqli_real_escape_string($conn, trim($_POST['course_code']));
    $name   = mysqli_real_escape_string($conn, trim($_POST['course_name']));
    $credit = floatval($_POST['credit_hour']);
    $dept   = intval($_POST['dept_id']);
    $teach  = intval($_POST['teacher_id']);

    $sql = "INSERT INTO Courses (course_code,course_name,credit_hour,dept_id,teacher_id)
            VALUES ('$code','$name',$credit," . ($dept ?: 'NULL') . "," . ($teach ?: 'NULL') . ")";
    $msg = mysqli_query($conn, $sql)
        ? "success|Course <strong>$name</strong> added!"
        : "error|" . mysqli_error($conn);
}

if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id     = intval($_POST['course_id']);
    $code   = mysqli_real_escape_string($conn, trim($_POST['course_code']));
    $name   = mysqli_real_escape_string($conn, trim($_POST['course_name']));
    $credit = floatval($_POST['credit_hour']);
    $dept   = intval($_POST['dept_id']);
    $teach  = intval($_POST['teacher_id']);

    $sql = "UPDATE Courses SET course_code='$code',course_name='$name',credit_hour=$credit,
            dept_id=" . ($dept ?: 'NULL') . ",teacher_id=" . ($teach ?: 'NULL') . "
            WHERE course_id=$id";
    $msg = mysqli_query($conn, $sql)
        ? "success|Course updated!"
        : "error|" . mysqli_error($conn);
}

if (isset($_GET['delete'])) {
    $id  = intval($_GET['delete']);
    $msg = mysqli_query($conn, "DELETE FROM Courses WHERE course_id=$id")
        ? "success|Course deleted."
        : "error|" . mysqli_error($conn);
}

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $r  = mysqli_query($conn, "SELECT * FROM Courses WHERE course_id=$id");
    $edit_data = mysqli_fetch_assoc($r);
}

$courses = mysqli_query($conn,
    "SELECT c.*, d.dept_name, t.name AS teacher_name
     FROM Courses c
     LEFT JOIN Departments d ON c.dept_id   = d.dept_id
     LEFT JOIN Teachers    t ON c.teacher_id= t.teacher_id
     ORDER BY c.course_code");

$dept_list = [];
$res = mysqli_query($conn, "SELECT * FROM Departments ORDER BY dept_name");
while ($r = mysqli_fetch_assoc($res)) $dept_list[] = $r;

$teacher_list = [];
$res = mysqli_query($conn, "SELECT * FROM Teachers ORDER BY name");
while ($r = mysqli_fetch_assoc($res)) $teacher_list[] = $r;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Courses — SMS</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/nav.php'; ?>
<main class="page-content">
  <div class="page-header">
    <h1 class="page-title">Courses</h1>
    <button class="btn btn-primary" onclick="toggleForm()">+ Add Course</button>
  </div>

  <?php if ($msg): [$type,$text] = explode('|',$msg,2); ?>
  <div class="alert alert-<?= $type ?>"><?= $text ?></div><?php endif; ?>

  <div class="card form-card <?= $edit_data ? 'open' : '' ?>" id="courseForm">
    <h2 class="card-title"><?= $edit_data ? 'Edit Course' : 'Add New Course' ?></h2>
    <form method="POST">
      <input type="hidden" name="action" value="<?= $edit_data ? 'update' : 'insert' ?>">
      <?php if ($edit_data): ?>
      <input type="hidden" name="course_id" value="<?= $edit_data['course_id'] ?>">
      <?php endif; ?>
      <div class="form-grid">
        <div class="form-group">
          <label>Course Code *</label>
          <input type="text" name="course_code" required placeholder="e.g. CSE301"
                 value="<?= htmlspecialchars($edit_data['course_code'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Course Name *</label>
          <input type="text" name="course_name" required placeholder="e.g. Database Management System"
                 value="<?= htmlspecialchars($edit_data['course_name'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Credit Hours</label>
          <input type="number" name="credit_hour" step="0.5" min="1" max="6"
                 value="<?= $edit_data['credit_hour'] ?? '3.0' ?>">
        </div>
        <div class="form-group">
          <label>Department</label>
          <select name="dept_id">
            <option value="">— Select —</option>
            <?php foreach ($dept_list as $d): ?>
            <option value="<?= $d['dept_id'] ?>" <?= ($edit_data['dept_id'] ?? '') == $d['dept_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($d['dept_name']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Assigned Teacher</label>
          <select name="teacher_id">
            <option value="">— Select —</option>
            <?php foreach ($teacher_list as $t): ?>
            <option value="<?= $t['teacher_id'] ?>" <?= ($edit_data['teacher_id'] ?? '') == $t['teacher_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($t['name']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $edit_data ? 'Update' : 'Save Course' ?></button>
        <a href="courses.php" class="btn btn-ghost">Cancel</a>
      </div>
    </form>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Code</th><th>Course Name</th><th>Credits</th><th>Department</th><th>Teacher</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php while ($c = mysqli_fetch_assoc($courses)): ?>
          <tr>
            <td><span class="badge badge-blue"><?= htmlspecialchars($c['course_code']) ?></span></td>
            <td><?= htmlspecialchars($c['course_name']) ?></td>
            <td><?= $c['credit_hour'] ?></td>
            <td><?= htmlspecialchars($c['dept_name'] ?? '—') ?></td>
            <td><?= htmlspecialchars($c['teacher_name'] ?? '—') ?></td>
            <td class="actions">
              <a href="?edit=<?= $c['course_id'] ?>" class="btn btn-sm btn-edit">Edit</a>
              <a href="?delete=<?= $c['course_id'] ?>" class="btn btn-sm btn-delete"
                 onclick="return confirm('Delete this course?')">Delete</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<script>function toggleForm(){document.getElementById('courseForm').classList.toggle('open');}</script>
</body>
</html>
