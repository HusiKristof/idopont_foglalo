<?php
session_start();
//var_dump($_SESSION); // Ellenőrizd, mit tartalmaz a session

if (!isset($_SESSION['user'])) {
    header('Location: index.php'); // Redirect to login if not logged in
    exit();
}

require '../database.php'; // Include your database connection

$userId = $_SESSION['user']['id'] ?? null; // Ellenőrizd, hogy az id létezik-e

if ($userId === null) {
    echo "Hiba: A felhasználó azonosítója nem található. Session tartalom: ";
    var_dump($_SESSION); // Debugging: mutasd meg a session tartalmát
    exit();
}

$name = $_POST['name'] ?? ''; // Ellenőrizd, hogy a mező létezik
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';

try {
    $stmt = $db->prepare("UPDATE users SET name = :name, email = :email, phone = :phone WHERE id = :id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':id', $userId);
    
    $stmt->execute();

    // Ellenőrizd, hogy hány sor változott
    if ($stmt->rowCount() > 0) {
        // Update session data
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['phone'] = $phone;

        header('Location: ../views/account.php'); // Redirect back to account page
        exit();
    } else {
        echo "Nincs változás az adatbázisban."; // No changes made
    }
} catch (PDOException $e) {
    echo "Hiba történt: " . $e->getMessage();
}
?>