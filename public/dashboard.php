<?php
$pageTitle = "Dashboard";
require_once __DIR__ . '/../views/layout_top.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<div class="card">
  <div class="card__top">
    <h1 class="title">Dashboard</h1>
    <p class="subtitle">You are logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>.</p>
  </div>

  <div class="card__body">
    <div class="alert alert--success">
      ✅ Access granted. Session is working.
    </div>

    <div style="height: 14px;"></div>

    <p class="muted">
      Role: <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong>
    </p>

    <div style="height: 14px;"></div>

    <a class="btn btn--ghost" href="logout.php" style="text-decoration:none; display:inline-block;">
      Logout
    </a>
  </div>
</div>

<?php require_once __DIR__ . '/../views/layout_bottom.php'; ?>