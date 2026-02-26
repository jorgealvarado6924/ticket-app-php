<?php
$pageTitle = "Create Ticket";
require_once __DIR__ . '/../views/layout_top.php';

require_once __DIR__ . '/../src/auth/guards.php';
require_auth();
?>

<div class="card">
  <div class="card__top">
    <h1 class="title">Create a ticket</h1>
    <p class="subtitle">Describe your issue and submit it.</p>
  </div>

  <div class="card__body">
    <form class="form" action="ticket_store.php" method="POST" novalidate>
      <div class="row">
        <label class="label" for="title">Title</label>
        <input class="input" id="title" name="title" type="text" required maxlength="120">
      </div>

      <div class="row">
        <label class="label" for="description">Description</label>
        <textarea class="input" id="description" name="description" rows="6" required></textarea>
      </div>

      <button class="btn" type="submit">Create</button>

      <a class="btn btn--ghost" href="tickets.php"
         style="text-decoration:none; display:block; text-align:center;">
        Back to tickets
      </a>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../views/layout_bottom.php'; ?>