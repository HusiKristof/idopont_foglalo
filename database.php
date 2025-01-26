<?php
    $host = 'localhost';
    $dbName = 'vizsga';
    $user = 'root'; // Your database username
    $pass = 'root'; // Your database password

    try {
        // Új PDO példány létrehozása
        $db = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Kapcsolódás sikertelen: " . $e->getMessage();
        exit();
    }

    try {
        // Új PDO példány létrehozása
        $dbConnection = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $user, $pass);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Kapcsolódás sikertelen: " . $e->getMessage();
        exit();
    }
?>