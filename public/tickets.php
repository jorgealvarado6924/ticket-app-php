<?php
$pageTitle = "Tickets";
require_once __DIR__ . '/../views/layout_top.php';

require_once __DIR__ . '/../src/auth/guards.php';
require_auth();

require_once __DIR__ . '/../config/db.php';

$userId = (int)$_SESSION['user_id'];
$role   = $_SESSION['role'] ?? 'user';

if ($role === 'admin') {
    $sql = "SELECT t.id, t.title, t.status, t.created_at, u.username
            FROM tickets t
            JOIN users u ON u.id = t.user_id
            ORDER BY t.id DESC";
    $stmt = $pdo->query($sql);
    $tickets = $stmt->fetchAll();
} else {
    $sql = "SELECT id, title, status, created_at
            FROM tickets
            WHERE user_id = ?
            ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $tickets = $stmt->fetchAll();
}
?>

<div class="card" style="width:min(820px,100%);">
  <div class="card__top">
    <h1 class="title">Tickets</h1>
    <p class="subtitle">
      <?php echo ($role === 'admin') ? "Admin view: all tickets" : "Your tickets only"; ?>
    </p>
  </div>

  <div class="card__body">
    <a class="btn" href="ticket_create.php" style="text-decoration:none; display:inline-block;">+ New Ticket</a>
    

    <div style="height: 16px;"></div>

    <?php if (count($tickets) === 0): ?>
      <div class="alert">No tickets yet.</div>
    <?php else: ?>
      <div style="display:grid; gap:10px;">
        <?php foreach ($tickets as $t): ?>
          <div class="alert" style="display:flex; justify-content:space-between; gap:10px; align-items:center;">
            <div>
              <strong>#<?php echo (int)$t['id']; ?></strong>
              <?php echo htmlspecialchars($t['title']); ?>
              <div class="muted" style="font-size:13px;">
                Status: <strong><?php echo htmlspecialchars($t['status']); ?></strong>
                · Created: <?php echo htmlspecialchars($t['created_at']); ?>
                <?php if ($role === 'admin'): ?>
                  · User: <strong><?php echo htmlspecialchars($t['username']); ?></strong>
                <?php endif; ?>
              </div>
            </div>

            <div style="display:flex; gap:8px;">
              <a class="btn btn--ghost"
                 href="ticket_view.php?id=<?php echo (int)$t['id']; ?>"
                 style="text-decoration:none;">
                 View
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../views/layout_bottom.php'; ?>