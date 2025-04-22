<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "PatateD0uce*"; // Replace with your MySQL root password

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=$servername", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL to create a database
    $sql = "CREATE DATABASE libraryMtp";

    // Execute the query
    $pdo->exec($sql);
    echo "Database created successfully";
} catch (PDOException $e) {
    echo "Error creating database: " . $e->getMessage();
}

// Close the connection
$pdo = null;
?>