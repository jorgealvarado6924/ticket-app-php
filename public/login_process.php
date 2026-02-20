<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    $_SESSION['error'] = "All fields are required";
    header("Location: login.php");
    exit;
}

$sql = "SELECT id, username, email, password, role FROM users WHERE email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);
$user = $stmt->fetch();


if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['error'] = "Invalid credentials";
    header("Location: login.php");
    exit;
}

$_SESSION['user_id']  = (int)$user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role']     = $user['role'];

header("Location: dashboard.php");
exit;