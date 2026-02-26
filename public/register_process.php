<?php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/support/flash.php';
require_once __DIR__ . '/../src/security/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit;
}

csrf_validate_or_die();

$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $email === '' || $password === '') {
    flash_set('error', 'All fields are required');
    header("Location: register.php");
    exit;
}

if (mb_strlen($username) < 3) {
    flash_set('error', 'Username must be at least 3 characters');
    header("Location: register.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash_set('error', 'Invalid email');
    header("Location: register.php");
    exit;
}

if (mb_strlen($password) < 6) {
    flash_set('error', 'Password must be at least 6 characters');
    header("Location: register.php");
    exit;
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

// Por defecto, rol user
$role = 'user';

try {
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $hashed, $role]);

    flash_set('success', 'Account created. You can now log in.');
    header("Location: login.php");
    exit;

} catch (PDOException $e) {
    flash_set('error', 'Email already exists');
    header("Location: register.php");
    exit;
}