<?php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/auth/guards.php';
require_once __DIR__ . '/../src/support/flash.php';

require_auth();

require_once __DIR__ . '/../src/security/csrf.php';
csrf_validate_or_die();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ticket_create.php");
    exit;
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($title === '' || $description === '') {
    flash_set('error', 'Title and description are required');
    header("Location: ticket_create.php");
    exit;
}

if (mb_strlen($title) > 120) {
    flash_set('error', 'Title is too long (max 120 chars)');
    header("Location: ticket_create.php");
    exit;
}

$userId = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare("INSERT INTO tickets (user_id, title, description) VALUES (?, ?, ?)");
$stmt->execute([$userId, $title, $description]);

flash_set('success', 'Ticket created successfully');
header("Location: tickets.php");
exit;