<?php

// Database connection
$host = 'localhost';
$dbname = 'librarymtp';
$username = 'root';
$password = 'PatateD0uce*';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}