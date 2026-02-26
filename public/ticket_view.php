<?php
$pageTitle = "View Ticket";
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

$sql = "SELECT t.id, t.user_id, t.title, t.description, t.status, t.created_at, u.username
        FROM tickets t
        JOIN users u ON u.id = t.user_id
        WHERE t.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    http_response_code(404);
    exit("Not found");
}

if ($role !== 'admin' && (int)$ticket['user_id'] !== $userId) {
    http_response_code(403);
    exit("403 Forbidden");
}
?>

<div class="card" style="width:min(820px,100%);">
  <div class="card__top">
    <h1 class="title">Ticket #<?php echo (int)$ticket['id']; ?></h1>
    <p class="subtitle">
      Status: <strong><?php echo htmlspecialchars($ticket['status']); ?></strong>
      <?php if ($role === 'admin'): ?>
        · User: <strong><?php echo htmlspecialchars($ticket['username']); ?></strong>
      <?php endif; ?>
    </p>
  </div>

  <div class="card__body">
    <h3 style="margin-top:0;"><?php echo htmlspecialchars($ticket['title']); ?></h3>

    <p style="white-space:pre-wrap;">
      <?php echo htmlspecialchars($ticket['description']); ?>
    </p>

    <div style="height: 14px;"></div>

    <?php if ($ticket['status'] === 'open'): ?>
      <!-- Form de close totalmente separado -->
      <form id="closeForm" action="ticket_close.php" method="POST"></form>
    <?php endif; ?>

    <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
      <?php if ($ticket['status'] === 'open'): ?>
    <?php require_once __DIR__ . '/../src/security/csrf.php'; ?>

    <form action="ticket_close.php" method="POST" style="display:inline-block; margin:0;">
      <?php echo csrf_input(); ?>
      <input type="hidden" name="id" value="<?php echo (int)$ticket['id']; ?>">
      <button class="btn" type="submit">Close ticket</button>
    </form>

  <a class="btn btn--ghost"
     href="ticket_edit.php?id=<?php echo (int)$ticket['id']; ?>"
     style="text-decoration:none; display:inline-block; margin-left:8px;">
    Edit
  </a>
<?php else: ?>
  <div class="alert" style="margin:0;">This ticket is closed.</div>
<?php endif; ?>


      
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../views/layout_bottom.php'; ?>