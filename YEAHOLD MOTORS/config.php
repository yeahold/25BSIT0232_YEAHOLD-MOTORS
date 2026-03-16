<?php
// config.php
session_start();

$host     = 'localhost';
$dbname   = 'trisha';
$username = 'root';
$password = '';   // ← Change if you set a MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}
?>