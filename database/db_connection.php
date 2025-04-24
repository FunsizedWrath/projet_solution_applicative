<?php

// Database connection
// $host = 'localhost';
// $dbname = 'librarymtp';
// $username = 'root';
// $password = 'PatateD0uce*';

try {
    // creating sqlite pdo connection
    $pdo = new PDO('sqlite:' . __DIR__ . '/librarymtp.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}