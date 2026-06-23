<?php
require_once '../includes/db.php';

$msg = "";
$edit_data = null;

// ── INSERT ────────────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'insert') {
    $id       = mysqli_real_escape_string($conn, trim($_POST['student_id']));
    $name     = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone    = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $dept_id  = intval($_POST['dept_id']);
    $semester = intval($_POST['semester']);

    $sql = "INSERT INTO Students (student_id, name, email, phone, dept_id, semester)
            VALUES ('$id','$name','$email','$phone',$dept_id,$semester)";
    if (mysqli_query($conn, $sql))
        $msg = "success|Student <strong>$name</strong> added successfully!";
    else
        $msg = "error|Error: " . mysqli_error($conn);
}

// ── UPDATE ────────────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id       = mysqli_real_escape_string($conn, trim($_POST['student_id']));
    $name     = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone    = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $dept_id  = intval($_POST['dept_id']);
    $semester = intval($_POST['semester']);

    $sql = "UPDATE Students SET name='$name', email='$email', phone='$phone',
            dept_id=$dept_id, semester=$semester WHERE student_id='$id'";
    if (mysqli_query($conn, $sql))
        $msg = "success|Student <strong>$name</strong> updated successfully!";
    else
        $msg = "error|Error: " . mysqli_error($conn);
}

// ── DELETE ────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id  = mysqli_real_escape_string($conn, $_GET['delete']);
    $sql = "DELETE FROM Students WHERE student_id='$id'";
    if (mysqli_query($conn, $sql))
        $msg = "success|Student deleted successfully.";
    else
        $msg = "error|Error: " . mysqli_error($conn);
}

// ── LOAD FOR EDIT ─────────────────────────────────────────────
if (isset($_GET['edit'])) {
    $id       = mysqli_real_escape_string($conn, $_GET['edit']);
    $res      = mysqli_query($conn, "SELECT * FROM Students WHERE student_id='$id'");
    $edit_data = mysqli_fetch_assoc($res);
}

// ── FETCH ALL ─────────────────────────────────────────────────
$students = mysqli_query($conn,
    "SELECT s.*, d.dept_name FROM Students s
     LEFT JOIN Departments d ON s.dept_id = d.dept_id
     ORDER BY s.created_at DESC");

$departments = mysqli_query($conn, "SELECT * FROM Departments ORDER BY dept_name");
$dept_list   = [];
while ($row = mysqli_fetch_assoc($departments)) $dept_list[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Students — SMS</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/nav.php'; ?>

<main class="page-content">
  <div class="page-header">
    <h1 class="page-title">Students</h1>
    <button class="btn btn-primary" onclick="toggleForm()">+ Add Student</button>
  </div>

  <?php if ($msg):
    [$type, $text] = explode('|', $msg, 2); ?>
  <div class="alert alert-<?= $type ?>"><?= $text ?></div>
  <?php endif; ?>

  <!-- ── FORM ── -->
  <div class="card form-card <?= ($edit_data || str_contains($msg ?? '', 'Error')) ? 'open' : '' ?>" id="studentForm">
    <h2 class="card-title"><?= $edit_data ? 'Edit Student' : 'Add New Student' ?></h2>
    <form method="POST">
      <input type="hidden" name="action" value="<?= $edit_data ? 'update' : 'insert' ?>">
      <div class="form-grid">
        <div class="form-group">
          <label>Student ID *</label>
          <input type="text" name="student_id" required placeholder="e.g. 05624205101062"
                 value="<?= $edit_data['student_id'] ?? '' ?>"
                 <?= $edit_data ? 'readonly' : '' ?>>
        </div>
        <div class="form-group">
          <label>Full Name *</label>
          <input type="text" name="name" required placeholder="e.g. Hridoy Paul"
                 value="<?= htmlspecialchars($edit_data['name'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" placeholder="student@neub.edu.bd"
                 value="<?= htmlspecialchars($edit_data['email'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" name="phone" placeholder="017XXXXXXXX"
                 value="<?= htmlspecialchars($edit_data['phone'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Department</label>
          <select name="dept_id">
            <option value="">— Select Department —</option>
            <?php foreach ($dept_list as $d): ?>
            <option value="<?= $d['dept_id'] ?>"
              <?= ($edit_data['dept_id'] ?? '') == $d['dept_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($d['dept_name']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Semester</label>
          <select name="semester">
            <?php for ($i = 1; $i <= 8; $i++): ?>
            <option value="<?= $i ?>" <?= ($edit_data['semester'] ?? '') == $i ? 'selected' : '' ?>>
              <?= $i ?>th Semester
            </option>
            <?php endfor; ?>
          </select>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $edit_data ? 'Update Student' : 'Save Student' ?></button>
        <a href="students.php" class="btn btn-ghost">Cancel</a>
      </div>
    </form>
  </div>

  <!-- ── TABLE ── -->
  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Student ID</th><th>Name</th><th>Email</th>
            <th>Phone</th><th>Department</th><th>Semester</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($s = mysqli_fetch_assoc($students)): ?>
          <tr>
            <td><span class="badge"><?= htmlspecialchars($s['student_id']) ?></span></td>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td><?= htmlspecialchars($s['email'] ?? '—') ?></td>
            <td><?= htmlspecialchars($s['phone'] ?? '—') ?></td>
            <td><?= htmlspecialchars($s['dept_name'] ?? '—') ?></td>
            <td>Sem <?= $s['semester'] ?></td>
            <td class="actions">
              <a href="?edit=<?= urlencode($s['student_id']) ?>" class="btn btn-sm btn-edit">Edit</a>
              <a href="?delete=<?= urlencode($s['student_id']) ?>"
                 class="btn btn-sm btn-delete"
                 onclick="return confirm('Delete student <?= htmlspecialchars(addslashes($s['name'])) ?>?')">Delete</a>
            </td>
          </tr>
          <?php endwhile; ?>
          <?php if (mysqli_num_rows($students) === 0): ?>
          <tr><td colspan="7" class="empty">No students found. Add one above.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<script>
function toggleForm() {
  document.getElementById('studentForm').classList.toggle('open');
}
</script>
</body>
</html>
