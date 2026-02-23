<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/auth/guards.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: tickets.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($id <= 0) {
    header("Location: tickets.php");
    exit;
}

if ($title === '' || $description === '') {
    $_SESSION['error'] = "Title and description are required";
    header("Location: ticket_edit.php?id=" . $id);
    exit;
}

if (mb_strlen($title) > 120) {
    $_SESSION['error'] = "Title is too long (max 120 chars)";
    header("Location: ticket_edit.php?id=" . $id);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$role   = $_SESSION['role'] ?? 'user';

/**
 * Check permissions + status open
 */
$stmt = $pdo->prepare("SELECT user_id, status FROM tickets WHERE id = ?");
$stmt->execute([$id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    header("Location: tickets.php");
    exit;
}

if ($role !== 'admin' && (int)$ticket['user_id'] !== $userId) {
    http_response_code(403);
    exit("403 Forbidden");
}

if ($ticket['status'] !== 'open') {
    $_SESSION['error'] = "Closed tickets cannot be edited";
    header("Location: ticket_view.php?id=" . $id);
    exit;
}

/**
 * Updated
 */
$stmt = $pdo->prepare("UPDATE tickets SET title = ?, description = ?, updated_at = NOW() WHERE id = ?");
$stmt->execute([$title, $description, $id]);

header("Location: ticket_view.php?id=" . $id);
exit;