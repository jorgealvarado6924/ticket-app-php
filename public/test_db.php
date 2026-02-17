<?php

require_once __DIR__ . '/../config/db.php';

//It saves the consult's result in SQL
$stmt = $pdo -> query("SELECT COUNT(*) AS total FROM users");

//fetch gets one row from the result 
$row=$stmt->fetch();

echo "Connection OK. Total users = " . $row['total'];