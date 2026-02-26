<?php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/auth/guards.php';
require_once __DIR__ . '/../src/support/flash.php';

require_auth();

require_once __DIR__ . '/../src/security/csrf.php';
csrf_validate_or_die();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: tickets.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header("Location: tickets.php");
    exit;
}

$userId = (int)$_SESSION['user_id'];
$role   = $_SESSION['role'] ?? 'user';

// Leer ticket
$stmt = $pdo->prepare("SELECT user_id, status FROM tickets WHERE id = ?");
$stmt->execute([$id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    flash_set('error', 'Ticket not found');
    header("Location: tickets.php");
    exit;
}

// Permisos
if ($role !== 'admin' && (int)$ticket['user_id'] !== $userId) {
    http_response_code(403);
    exit("403 Forbidden");
}

// Si ya está cerrado
if ($ticket['status'] === 'closed') {
    flash_set('error', 'Ticket is already closed');
    header("Location: ticket_view.php?id=" . $id);
    exit;
}

// Cerrar
$stmt = $pdo->prepare("UPDATE tickets SET status = 'closed', updated_at = NOW() WHERE id = ?");
$stmt->execute([$id]);

flash_set('success', 'Ticket closed');
header("Location: ticket_view.php?id=" . $id);
exit;