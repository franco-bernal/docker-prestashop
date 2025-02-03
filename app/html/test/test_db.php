<?php
try {
    $dsn = "mysql:host=mysql_db;dbname=prestashop;charset=utf8mb4";
    $pdo = new PDO($dsn, 'laravel', 'secret');
    echo "Connection successful using PDO!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

