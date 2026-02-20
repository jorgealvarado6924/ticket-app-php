<?php
$pageTitle = "Login";
require_once __DIR__ . '/../views/layout_top.php';
?>

<div class="card">
  <div class="card__top">
    <h1 class="title">Welcome back</h1>
    <p class="subtitle">Log in to access your dashboard.</p>
  </div>

  <div class="card__body">
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert--error">
        <?php echo htmlspecialchars($_SESSION['error']); ?>
      </div>
      <?php unset($_SESSION['error']); ?>
      <div style="height: 10px;"></div>
    <?php endif; ?>

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

      <a class="btn btn--ghost" href="register.php" style="text-align:center; display:block; text-decoration:none;">
        Create a new account
      </a>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../views/layout_bottom.php'; ?>