<?php
//gia sindesi sti basi dedomenon 
$host = 'localhost';
$db = 'restaurant';
$user = 'root';
$password = '';

$dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

try {
$pdo = new PDO($dsn, $user, $password);

} catch (PDOException $e) {
die("Connection failed: ".  $e->getMessage());
}
?>