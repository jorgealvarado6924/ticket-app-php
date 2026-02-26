<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../src/support/flash.php';

$brandLink = isset($_SESSION['user_id']) ? 'dashboard.php' : 'login.php';

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
      <div class="container">
        <a class="brand" href="<?php echo $brandLink; ?>">
          <span>Ticket App</span>
          <span class="badge">PHP + MySQL</span>
        </a>
      </div>
    </header>

    <main class="main">
      <div class="container">

        <?php if ($flashError): ?>
          <div class="alert alert--error">
            <?php echo htmlspecialchars($flashError); ?>
          </div>
          <div style="height: 10px;"></div>
        <?php endif; ?>

        <?php if ($flashSuccess): ?>
          <div class="alert alert--success">
            <?php echo htmlspecialchars($flashSuccess); ?>
          </div>
          <div style="height: 10px;"></div>
        <?php endif; ?>

        