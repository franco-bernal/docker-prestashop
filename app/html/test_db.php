<?php
$conn = new mysqli('mysql_db', 'laravel', 'secret', 'prestashop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connection successful using mysqli!";
?>

