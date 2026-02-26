<?php
require_once __DIR__ . '/../src/auth/guards.php';
require_auth();
require_once __DIR__ . '/../config/db.php';

$userId = (int)$_SESSION['user_id'];
$role   = $_SESSION['role'] ?? 'user';

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

$where = [];
$params = [];

// Scope por rol
if ($role !== 'admin') {
    $where[] = "t.user_id = ?";
    $params[] = $userId;
}

// Filtro status
if ($status !== 'all') {
    $where[] = "t.status = ?";
    $params[] = $status;
}

// Búsqueda texto (LIKE, 1+ caracteres)
if ($q !== '') {
    $where[] = "(t.title LIKE ? OR t.description LIKE ?)";
    $like = "%" . $q . "%";
    $params[] = $like;
    $params[] = $like;
}

$whereSql = count($where) ? ("WHERE " . implode(" AND ", $where)) : "";

if ($role === 'admin') {
    $sql = "SELECT t.id, t.title, t.status, t.created_at, u.username
            FROM tickets t
            JOIN users u ON u.id = t.user_id
            $whereSql
            ORDER BY $orderBy
            LIMIT 10";
} else {
    $sql = "SELECT t.id, t.title, t.status, t.created_at
            FROM tickets t
            $whereSql
            ORDER BY $orderBy
            LIMIT 10";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll();

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

if (!$tickets) {
    echo '<div class="alert">No results</div>';
    exit;
}

foreach ($tickets as $t) {
    echo '<div class="alert" style="display:flex; justify-content:space-between; gap:10px; align-items:center;">';
    echo '<div>';
    echo '<strong>#' . (int)$t['id'] . '</strong> ' . h($t['title']);
    echo '<div class="muted" style="font-size:13px;">';
    echo 'Status: <strong>' . h($t['status']) . '</strong>';
    echo ' · Created: ' . h($t['created_at']);
    if ($role === 'admin') {
        echo ' · User: <strong>' . h($t['username']) . '</strong>';
    }
    echo '</div></div>';

    echo '<div><a class="btn btn--ghost" href="ticket_view.php?id=' . (int)$t['id'] . '" style="text-decoration:none;">View</a></div>';
    echo '</div>';
}