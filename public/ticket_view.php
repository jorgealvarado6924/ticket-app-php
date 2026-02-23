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

// Security: user just can see their tickets
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
        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert--error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
        <div style="height: 10px;"></div>
        <?php endif; ?>
    <h3 style="margin-top:0;"><?php echo htmlspecialchars($ticket['title']); ?></h3>
    <p style="white-space:pre-wrap;"><?php echo htmlspecialchars($ticket['description']); ?></p>

    <div style="height: 14px;"></div>

<?php if ($ticket['status'] === 'open'): ?>
  <a class="btn btn--ghost"
     href="ticket_edit.php?id=<?php echo (int)$ticket['id']; ?>"
     style="text-decoration:none; display:inline-block;">
     Edit
  </a>

  <form action="ticket_close.php" method="POST" style="display:inline-block;">
    <input type="hidden" name="id" value="<?php echo (int)$ticket['id']; ?>">
    <button class="btn" type="submit">Close ticket</button>
  </form>
<?php else: ?>
  <div class="alert">This ticket is closed.</div>
<?php endif; ?>

<a class="btn btn--ghost" href="tickets.php" style="text-decoration:none; display:inline-block;">Back</a>
</div>

<?php require_once __DIR__ . '/../views/layout_bottom.php'; ?>