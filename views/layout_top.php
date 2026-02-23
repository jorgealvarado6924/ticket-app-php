<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
          <?php
            $brandLink = isset($_SESSION['user_id']) ? 'dashboard.php' : 'login.php';
          ?>

          <a class="brand" href="<?php echo $brandLink; ?>">
            <span>Ticket App</span>
            <span class="badge">PHP + MySQL</span>
          </a>
      </div>
    </header>

    <main class="main">
      <div class="container">
