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

$sort = $_GET['sort'] ?? 'recent';
$allowedSort = ['recent', 'oldest', 'title_asc', 'title_desc'];
if (!in_array($sort, $allowedSort, true)) {
  $sort = 'recent';
}

switch ($sort) {
  case 'oldest':
    $orderBy = "t.id ASC";
    break;
  case 'title_asc':
    $orderBy = "t.title ASC, t.id DESC";
    break;
  case 'title_desc':
    $orderBy = "t.title DESC, t.id DESC";
    break;
  case 'recent':
  default:
    $orderBy = "t.id DESC";
    break;
}

// --- Pagination (GET) ---
$page = (int)($_GET['page'] ?? 1);
if ($page < 1) $page = 1;

$perPage = 10; // cambia a 8/12 si prefieres
$offset = ($page - 1) * $perPage;

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

/**
 * 1) COUNT total results (para saber páginas)
 */
$countSql = "SELECT COUNT(*) FROM tickets t $whereSql";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();

$totalPages = (int)ceil($totalRows / $perPage);
if ($totalPages < 1) $totalPages = 1;
if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;

/**
 * 2) Query paginada
 */
if ($role === 'admin') {
  $sql = "SELECT t.id, t.title, t.status, t.created_at, u.username
        FROM tickets t
        JOIN users u ON u.id = t.user_id
        $whereSql
        ORDER BY $orderBy
        LIMIT $perPage OFFSET $offset";
} else {
  $sql = "SELECT t.id, t.title, t.status, t.created_at
        FROM tickets t
        $whereSql
        ORDER BY $orderBy
        LIMIT $perPage OFFSET $offset";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll();

function h(string $s): string
{
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function build_query(array $overrides = []): string
{
  $base = [
    'q' => $_GET['q'] ?? '',
    'status' => $_GET['status'] ?? 'all',
    'sort' => $_GET['sort'] ?? 'recent',
    'page' => $_GET['page'] ?? 1,
  ];
  $merged = array_merge($base, $overrides);

  // limpia params vacíos
  if ($merged['q'] === '') unset($merged['q']);
  if ($merged['status'] === 'all') unset($merged['status']);
  if (($merged['sort'] ?? 'recent') === 'recent') unset($merged['sort']);
  return http_build_query($merged);
}

$from = $totalRows === 0 ? 0 : ($offset + 1);
$to   = min($offset + $perPage, $totalRows);
?>

<div class="card" style="width:min(920px,100%);">
  <div class="card__top">
    <h1 class="title">Tickets</h1>
    <p class="subtitle">
      <?php echo ($role === 'admin') ? "Admin view: all tickets" : "Your tickets only"; ?>
      · Showing <?php echo $from; ?>–<?php echo $to; ?> of <?php echo $totalRows; ?>
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

        <select class="input" name="sort" style="min-width:170px;">
          <option value="recent" <?php echo $sort === 'recent' ? 'selected' : ''; ?>>Newest first</option>
          <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest first</option>
          <option value="title_asc" <?php echo $sort === 'title_asc' ? 'selected' : ''; ?>>Title A → Z</option>
          <option value="title_desc" <?php echo $sort === 'title_desc' ? 'selected' : ''; ?>>Title Z → A</option>
        </select>

        <!-- al aplicar filtros, vuelve a page 1 -->
        <input type="hidden" name="page" value="1">

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

      <div style="height: 14px;"></div>

      <!-- Pagination controls -->
      <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
        <div class="muted" style="font-size:13px;">
          Page <strong><?php echo $page; ?></strong> of <strong><?php echo $totalPages; ?></strong>
        </div>

        <div style="display:flex; gap:8px; flex-wrap:wrap;">
          <?php if ($page > 1): ?>
            <a class="btn btn--ghost" style="text-decoration:none;"
              href="tickets.php?<?php echo h(build_query(['page' => $page - 1])); ?>">
              ← Prev
            </a>
          <?php else: ?>
            <span class="btn btn--ghost" style="opacity:.5; cursor:not-allowed;">← Prev</span>
          <?php endif; ?>

          <?php if ($page < $totalPages): ?>
            <a class="btn btn--ghost" style="text-decoration:none;"
              href="tickets.php?<?php echo h(build_query(['page' => $page + 1])); ?>">
              Next →
            </a>
          <?php else: ?>
            <span class="btn btn--ghost" style="opacity:.5; cursor:not-allowed;">Next →</span>
          <?php endif; ?>
        </div>
      </div>

    <?php endif; ?>

  </div>
</div>

<?php require_once __DIR__ . '/../views/layout_bottom.php'; ?>