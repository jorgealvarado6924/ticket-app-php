<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/auth/guards.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ticket_create.php");
    exit;
}

//  ** Trim() ** eliminates spaces at the beginning and at the end of the word 

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($title === '' || $description === '') {
    $_SESSION['error'] = "Title and description are required";
    header("Location: ticket_create.php");
    exit;
}

if (mb_strlen($title) > 120) {
    $_SESSION['error'] = "Title is too long (max 120 chars)";
    header("Location: ticket_create.php");
    exit;
}

$userId = (int)$_SESSION['user_id'];

$sql = "INSERT INTO tickets (user_id, title, description) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, $title, $description]);

header("Location: tickets.php");
exit;