<?php
//Activa modo estrcito de PHP, evita errores de tipos
declare(strict_types=1);


//Defines data with connection

$host = '127.0.0.1';
$db = 'app_db';
$port = '3307';
$user = 'root';
$pass = ''; 
$charset = 'utf8mb4';

//Connect to MySQL in this host, with this port, this database and with this charset 
$dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";

try{
    // The real connection with the database
    $pdo = new PDO($dsn, $user, $pass, [
        //if there is a mistake in SQL, i'll run an exception
        PDO::ATTR_ERRMODE   =>  PDO::ERRMODE_EXCEPTION,

        //Here fetch() returns associative arrays
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit("DB connection failed: ". $e->getMessage());
}

