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
if ($id <= 0) {
    header("Location: tickets.php");
    exit;
}

$userId = (int)$_SESSION['user_id'];
$role   = $_SESSION['role'] ?? 'user';

/**
 * 1) Leer ticket para validar permisos:
 *    - admin puede cerrar cualquiera
 *    - user solo puede cerrar los suyos
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

if ($ticket['status'] === 'closed') {
    header("Location: ticket_view.php?id=" . $id);
    exit;
}

/**
 * 2) Cerrar ticket
 */
$stmt = $pdo->prepare("UPDATE tickets SET status = 'closed', updated_at = NOW() WHERE id = ?");
$stmt->execute([$id]);

header("Location: ticket_view.php?id=" . $id);
exit;