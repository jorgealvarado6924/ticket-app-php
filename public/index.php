<?php
$pageTitle = "Home";
require_once __DIR__ . '/../views/layout_top.php';
?>

<div class="card">
  <div class="card__top">
    <h1 class="title">Ticket App</h1>
    <p class="subtitle">Secure auth + roles (PHP + MySQL).</p>
  </div>

  <div class="card__body">
    <a class="btn" href="register.php" style="text-decoration:none; display:block; text-align:center;">Register</a>
    <div style="height: 10px;"></div>
    <a class="btn btn--ghost" href="login.php" style="text-decoration:none; display:block; text-align:center;">Login</a>
  </div>
</div>

<?php require_once __DIR__ . '/../views/layout_bottom.php'; ?>