<?php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/auth/guards.php';
require_once __DIR__ . '/../src/support/flash.php';

require_auth();

/**
 * Solo permitir POST
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: tickets.php");
    exit;
}

/**
 * Recoger datos
 */
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($id <= 0) {
    header("Location: tickets.php");
    exit;
}

/**
 * Validación de campos (solo cuando vienes del formulario de edición)
 */
if ($title === '' || $description === '') {
    flash_set('error', 'Title and description are required');
    header("Location: ticket_edit.php?id=" . $id);
    exit;
}

if (mb_strlen($title) > 120) {
    flash_set('error', 'Title is too long (max 120 chars)');
    header("Location: ticket_edit.php?id=" . $id);
    exit;
}

/**
 * Validar permisos y estado
 */
$userId = (int)$_SESSION['user_id'];
$role   = $_SESSION['role'] ?? 'user';

$stmt = $pdo->prepare("SELECT user_id, status FROM tickets WHERE id = ?");
$stmt->execute([$id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    header("Location: tickets.php");
    exit;
}

/**
 * Permisos:
 * - admin puede editar cualquiera
 * - user solo sus tickets
 */
if ($role !== 'admin' && (int)$ticket['user_id'] !== $userId) {
    http_response_code(403);
    exit("403 Forbidden");
}

/**
 * Regla de negocio: CLOSED = NO EDITABLE
 */
if ($ticket['status'] !== 'open') {
    flash_set('error', 'Closed tickets cannot be edited');
    header("Location: ticket_view.php?id=" . $id);
    exit;
}

/**
 * Actualizar ticket
 */
$stmt = $pdo->prepare(
    "UPDATE tickets
     SET title = ?, description = ?, updated_at = NOW()
     WHERE id = ?"
);
$stmt->execute([$title, $description, $id]);

flash_set('success', 'Ticket updated');
header("Location: ticket_view.php?id=" . $id);
exit;