<?php
$pageTitle = "Login";
require_once __DIR__ . '/../views/layout_top.php';

// Si ya estás logueado, te mando al dashboard (mejor UX)
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<div class="card">
  <div class="card__top">
    <h1 class="title">Welcome back</h1>
    <p class="subtitle">Log in to access your dashboard.</p>
  </div>

  <div class="card__body">
    <form class="form" action="login_process.php" method="POST" novalidate>
      <div class="row">
        <label class="label" for="email">Email</label>
        <input class="input" id="email" type="email" name="email" required>
      </div>

      <div class="row">
        <label class="label" for="password">Password</label>
        <input class="input" id="password" type="password" name="password" required>
      </div>

      <button class="btn" type="submit">Login</button>

      <a class="btn btn--ghost" href="register.php"
         style="text-align:center; display:block; text-decoration:none;">
        Create a new account
      </a>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../views/layout_bottom.php'; ?>