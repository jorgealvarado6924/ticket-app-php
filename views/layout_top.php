<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../src/support/flash.php';
require_once __DIR__ . '/../src/support/auth.php';

$user = current_user();
$brandLink = $user ? 'dashboard.php' : 'login.php';
$current = basename($_SERVER['PHP_SELF']);
$flashError = flash_get('error');
$flashSuccess = flash_get('success');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($pageTitle ?? 'Ticket App'); ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="page">

    <header class="header">
      <div class="container" style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
        <a class="brand" href="<?php echo $brandLink; ?>">
          <span>Ticket App</span>
          <span class="badge">PHP + MySQL</span>
        </a>

        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <?php if ($user): ?>
            <span class="muted" style="font-size:13px;">
              Hi, <strong><?php echo htmlspecialchars($user['username']); ?></strong>
              <span class="badge" style="margin-left:6px;"><?php echo htmlspecialchars($user['role']); ?></span>
            </span>

            <a class="btn btn--ghost" href="dashboard.php" style="text-decoration:none; padding:10px 12px;">Dashboard</a>
            <a class="btn btn--ghost <?php echo $current === 'tickets.php' ? 'active' : ''; ?>" href="tickets.php">Tickets</a>

            <?php if (is_admin()): ?>
              <a class="btn btn--ghost" href="admin.php" style="text-decoration:none; padding:10px 12px;">Admin</a>
            <?php endif; ?>

            <a class="btn" href="logout.php" style="text-decoration:none; padding:10px 12px;">Logout</a>

          <?php else: ?>
            <a class="btn btn--ghost" href="login.php" style="text-decoration:none; padding:10px 12px;">Login</a>
            <a class="btn" href="register.php" style="text-decoration:none; padding:10px 12px;">Register</a>
          <?php endif; ?>
        </div>
      </div>
    </header>

    <main class="main">
      <div class="container">

        <?php if ($flashError): ?>
          <div class="alert alert--error"><?php echo htmlspecialchars($flashError); ?></div>
          <div style="height: 10px;"></div>
        <?php endif; ?>

        <?php if ($flashSuccess): ?>
          <div class="alert alert--success"><?php echo htmlspecialchars($flashSuccess); ?></div>
          <div style="height: 10px;"></div>
        <?php endif; ?>