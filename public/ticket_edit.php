<?php
$pageTitle = "Edit Ticket";
require_once __DIR__ . '/../views/layout_top.php';

require_once __DIR__ . '/../src/auth/guards.php';
require_auth();

require_once __DIR__ . '/../config/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    exit("Bad request");
}

$userId = (int)$_SESSION['user_id'];
$role   = $_SESSION['role'] ?? 'user';

$stmt = $pdo->prepare("SELECT id, user_id, title, description, status FROM tickets WHERE id = ?");
$stmt->execute([$id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    http_response_code(404);
    exit("Not found");
}

// Permisos: user solo sus tickets
if ($role !== 'admin' && (int)$ticket['user_id'] !== $userId) {
    http_response_code(403);
    exit("403 Forbidden");
}

// Regla: CLOSED no se edita (nadie)
if ($ticket['status'] !== 'open') {
    require_once __DIR__ . '/../src/support/flash.php';
    flash_set('error', 'Closed tickets cannot be edited');
    header("Location: ticket_view.php?id=" . $id);
    exit;
}
?>

<div class="card" style="width:min(820px,100%);">
  <div class="card__top">
    <h1 class="title">Edit Ticket #<?php echo (int)$ticket['id']; ?></h1>
    <p class="subtitle">Update title and description.</p>
  </div>

  <div class="card__body">
    <form class="form" action="ticket_update.php" method="POST" novalidate>
      <input type="hidden" name="id" value="<?php echo (int)$ticket['id']; ?>">

      <div class="row">
        <label class="label" for="title">Title</label>
        <input class="input" id="title" name="title" type="text" required maxlength="120"
               value="<?php echo htmlspecialchars($ticket['title']); ?>">
      </div>

      <div class="row">
        <label class="label" for="description">Description</label>
        <textarea class="input" id="description" name="description" rows="7" required><?php
          echo htmlspecialchars($ticket['description']);
        ?></textarea>
      </div>

      <button class="btn" type="submit">Save changes</button>
      <a class="btn btn--ghost"
         href="ticket_view.php?id=<?php echo (int)$ticket['id']; ?>"
         style="text-decoration:none; display:inline-block;">
        Cancel
      </a>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../views/layout_bottom.php'; ?>