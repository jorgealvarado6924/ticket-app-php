<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $email === '' || $password === '') {
    $_SESSION['error'] = "All fields are required";
    header("Location: register.php");
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([$username, $email, $hashedPassword]);
    header("Location: login.php");
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = "Email already exists";
    header("Location: register.php");
    exit;
}