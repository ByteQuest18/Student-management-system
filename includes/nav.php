<?php
$current = basename($_SERVER['PHP_SELF']);
$pages = [
    'index.php'        => ['Dashboard', '🏠'],
    'students.php'     => ['Students',  '🎓'],
    'courses.php'      => ['Courses',   '📚'],
    'enrollments.php'  => ['Enrollments','📋'],
];
?>
<nav class="sidebar">
  <div class="sidebar-logo">
    <span class="logo-icon">📘</span>
    <div>
      <div class="logo-title">SMS</div>
      <div class="logo-sub">NEUB · CSE</div>
    </div>
  </div>
  <ul class="nav-list">
    <?php foreach ($pages as $file => [$label, $icon]): ?>
    <li>
      <a href="<?= $file ?>" class="nav-link <?= $current === $file ? 'active' : '' ?>">
        <span class="nav-icon"><?= $icon ?></span>
        <span><?= $label ?></span>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
  <div class="sidebar-footer">North East University BD</div>
</nav>
