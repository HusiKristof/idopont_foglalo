<?php
$host = '127.0.0.1';  // localhost helyett 127.0.0.1
$port = 8889;         // MAMP MySQL alapértelmezett portja
$dbName = 'vizsga';
$user = 'root';
$pass = 'root';

try {
    // PDO objektum létrehozása
    $db = new PDO("mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4", $user, $pass);

    // HIBA: $db helyett $pdo-t kellene használni
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Kapcsolódás sikertelen: " . $e->getMessage();
    exit();
}
?>
