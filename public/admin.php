<?php
$pageTitle = "Admin";
require_once __DIR__ . '/../views/layout_top.php';

require_once __DIR__ . '/../src/auth/guards.php';
require_role('admin');
?>

<div class="card">
  <div class="card__top">
    <h1 class="title">Admin Area</h1>
    <p class="subtitle">Only admins can see this page.</p>
  </div>

  <div class="card__body">
    <div class="alert alert--success">✅ You have admin access.</div>

    <div style="height: 14px;"></div>

    <a class="btn btn--ghost" href="dashboard.php" style="text-decoration:none; display:inline-block;">Back</a>
  </div>
</div>

<?php require_once __DIR__ . '/../views/layout_bottom.php'; ?>