<?php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/support/flash.php';
require_once __DIR__ . '/../src/security/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

csrf_validate_or_die();

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    flash_set('error', 'All fields are required');
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    flash_set('error', 'Invalid credentials');
    header("Location: login.php");
    exit;
}

// 🔒 Seguridad pro: evita session fixation
session_regenerate_id(true);

$_SESSION['user_id']  = (int)$user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role']     = $user['role'];

flash_set('success', 'Welcome back, ' . $user['username'] . '!');
header("Location: dashboard.php");
exit;