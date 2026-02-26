<?php
$pageTitle = "Dashboard";
require_once __DIR__ . '/../views/layout_top.php';

require_once __DIR__ . '/../src/auth/guards.php';
require_auth();

require_once __DIR__ . '/../config/db.php';

$userId = (int)$_SESSION['user_id'];
$role   = $_SESSION['role'] ?? 'user';

if ($role === 'admin') {
    // Totales
    $total = (int)$pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn();
    $open  = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status='open'")->fetchColumn();
    $closed= (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status='closed'")->fetchColumn();

    // Últimos tickets
    $stmt = $pdo->query(
        "SELECT t.id, t.title, t.status, t.created_at, u.username
         FROM tickets t
         JOIN users u ON u.id = t.user_id
         ORDER BY t.id DESC
         LIMIT 5"
    );
    $recent = $stmt->fetchAll();

} else {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE user_id = ?");
    $stmt->execute([$userId]);
    $total = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE user_id = ? AND status='open'");
    $stmt->execute([$userId]);
    $open = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE user_id = ? AND status='closed'");
    $stmt->execute([$userId]);
    $closed = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare(
        "SELECT id, title, status, created_at
         FROM tickets
         WHERE user_id = ?
         ORDER BY id DESC
         LIMIT 5"
    );
    $stmt->execute([$userId]);
    $recent = $stmt->fetchAll();
}
?>

<div class="card" style="width:min(920px,100%);">
  <div class="card__top">
    <h1 class="title">Dashboard</h1>
    <p class="subtitle">
      <?php echo $role === 'admin' ? 'Admin overview (all tickets)' : 'Your overview'; ?>
    </p>
  </div>

  <div class="card__body">
    <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
      <a class="btn" href="ticket_create.php" style="text-decoration:none;">+ New Ticket</a>
      <a class="btn btn--ghost" href="tickets.php" style="text-decoration:none;">View Tickets</a>
    </div>

    <div style="height: 16px;"></div>

    <!-- KPI cards -->
    <div style="display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:10px;">
      <div class="alert">
        <div class="muted" style="font-size:12px;">Total</div>
        <div style="font-size:22px; font-weight:800;"><?php echo $total; ?></div>
      </div>
      <div class="alert">
        <div class="muted" style="font-size:12px;">Open</div>
        <div style="font-size:22px; font-weight:800;"><?php echo $open; ?></div>
      </div>
      <div class="alert">
        <div class="muted" style="font-size:12px;">Closed</div>
        <div style="font-size:22px; font-weight:800;"><?php echo $closed; ?></div>
      </div>
    </div>

    <div style="height: 16px;"></div>

    <h3 style="margin:0 0 10px;">Recent tickets</h3>

    <?php if (count($recent) === 0): ?>
      <div class="alert">No tickets yet.</div>
    <?php else: ?>
      <div style="display:grid; gap:10px;">
        <?php foreach ($recent as $t): ?>
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