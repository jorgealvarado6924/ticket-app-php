<?php
$pageTitle = "Profile";
require_once __DIR__ . '/../views/layout_top.php';

require_once __DIR__ . '/../src/auth/guards.php';
require_auth();
?>

<div class="card">
  <div class="card__top">
    <h1 class="title">Profile</h1>
    <p class="subtitle">Your session data.</p>
  </div>

  <div class="card__body">
    <p><strong>ID:</strong> <?php echo (int)$_SESSION['user_id']; ?></p>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    <p><strong>Role:</strong> <?php echo htmlspecialchars($_SESSION['role']); ?></p>

    <div style="height: 14px;"></div>

    <a class="btn btn--ghost" href="dashboard.php" style="text-decoration:none; display:inline-block;">Back</a>
  </div>
</div>

<?php require_once __DIR__ . '/../views/layout_bottom.php'; ?>