<?php
$pageTitle = "Tickets";
require_once __DIR__ . '/../views/layout_top.php';

require_once __DIR__ . '/../src/auth/guards.php';
require_auth();

require_once __DIR__ . '/../config/db.php';

$userId = (int)$_SESSION['user_id'];
$role   = $_SESSION['role'] ?? 'user';

// --- Filters (GET) ---
$q = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? 'all';
$allowedStatus = ['all', 'open', 'closed'];
if (!in_array($status, $allowedStatus, true)) {
    $status = 'all';
}

$where = [];
$params = [];

// Role-based scope
if ($role !== 'admin') {
    $where[] = "t.user_id = ?";
    $params[] = $userId;
}

// Status filter
if ($status !== 'all') {
    $where[] = "t.status = ?";
    $params[] = $status;
}

// Search filter
if ($q !== '') {
    $where[] = "(t.title LIKE ? OR t.description LIKE ?)";
    $like = "%" . $q . "%";
    $params[] = $like;
    $params[] = $like;
}

$whereSql = count($where) ? ("WHERE " . implode(" AND ", $where)) : "";

// Select
if ($role === 'admin') {
    $sql = "SELECT t.id, t.title, t.status, t.created_at, u.username
            FROM tickets t
            JOIN users u ON u.id = t.user_id
            $whereSql
            ORDER BY t.id DESC";
} else {
    $sql = "SELECT t.id, t.title, t.status, t.created_at
            FROM tickets t
            $whereSql
            ORDER BY t.id DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll();

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>

<div class="card" style="width:min(920px,100%);">
  <div class="card__top">
    <h1 class="title">Tickets</h1>
    <p class="subtitle">
      <?php echo ($role === 'admin') ? "Admin view: all tickets" : "Your tickets only"; ?>
    </p>
  </div>

  <div class="card__body">

    <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; justify-content:space-between;">
      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <a class="btn" href="ticket_create.php" style="text-decoration:none;">+ New Ticket</a>
      </div>

      <!-- Filters -->
      <form method="GET" action="tickets.php" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin:0;">
        <input class="input" type="text" name="q" placeholder="Search title or description..."
               value="<?php echo h($q); ?>" style="min-width:260px;">

        <select class="input" name="status" style="min-width:160px;">
          <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All status</option>
          <option value="open" <?php echo $status === 'open' ? 'selected' : ''; ?>>Open</option>
          <option value="closed" <?php echo $status === 'closed' ? 'selected' : ''; ?>>Closed</option>
        </select>

        <button class="btn btn--ghost" type="submit">Apply</button>

        <?php if ($q !== '' || $status !== 'all'): ?>
          <a class="btn btn--ghost" href="tickets.php" style="text-decoration:none;">Clear</a>
        <?php endif; ?>
      </form>
    </div>

    <div style="height: 16px;"></div>

    <?php if (count($tickets) === 0): ?>
      <div class="alert">
        No tickets found<?php echo ($q !== '' || $status !== 'all') ? " for the current filters." : "."; ?>
      </div>
    <?php else: ?>
      <div style="display:grid; gap:10px;">
        <?php foreach ($tickets as $t): ?>
          <div class="alert" style="display:flex; justify-content:space-between; gap:10px; align-items:center;">
            <div>
              <strong>#<?php echo (int)$t['id']; ?></strong>
              <?php echo h($t['title']); ?>

              <div class="muted" style="font-size:13px;">
                Status: <strong><?php echo h($t['status']); ?></strong>
                · Created: <?php echo h($t['created_at']); ?>
                <?php if ($role === 'admin'): ?>
                  · User: <strong><?php echo h($t['username']); ?></strong>
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