<?php
$pageTitle = "Register";
require_once __DIR__ . '/../views/layout_top.php';
?>

<div class="card">
  <div class="card__top">
    <h1 class="title">Create your account</h1>
    <p class="subtitle">Secure registration with hashed passwords.</p>
  </div>

  <div class="card__body">
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert--error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
      <?php unset($_SESSION['error']); ?>
      <div style="height: 10px;"></div>
    <?php endif; ?>

    <form class="form" action="register_process.php" method="POST" novalidate>
      <div class="row">
        <label class="label" for="username">Username</label>
        <input class="input" id="username" type="text" name="username" required>
      </div>

      <div class="row">
        <label class="label" for="email">Email</label>
        <input class="input" id="email" type="email" name="email" required>
      </div>

      <div class="row">
        <label class="label" for="password">Password</label>
        <input class="input" id="password" type="password" name="password" required minlength="6">
      </div>

      <button class="btn" type="submit">Register</button>
      <a class="btn btn--ghost" href="login.php" style="text-align:center; display:block; text-decoration:none;">
        I already have an account
      </a>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../views/layout_bottom.php'; ?>