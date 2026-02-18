<?php

//Keeps User Logged In
session_start();

//Load the file where the DB is located
require_once __DIR__ . '/../config/db.php';

//Verify if the form has been send by POST
//Avoid incorrect access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header ("Location: register.php");
    exit;
}

//trim -> removes spaces at the beginning and the end
//Gets form data
$username = trim($_POST['username']);
$email = trim ($_POST['email']);
$password = trim ($_POST['password']);


//If there is a empty field saves an error message
if (!$username || !$email || !$password) {
    $_SESSION ['error'] = "All fields are required";
    header("Location: register.php");
    exit;
}

//Encrypt password
//Saves in a safe hash
//Obligatory in real systems
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

//Prepare a query
//Avoid SQL injection
$sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
$stmt = $pdo -> prepare($sql);

try {
    //Inserts user in the DB
    $stmt -> execute ([$username, $email, $hashedPassword]);
    header("Location: register.php");
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = "Ya existe un usuario con el mismo e-mail";
    header("Location: register.php");
    exit;
}

?>