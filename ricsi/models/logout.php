<?php
// Session indítása
session_start();

// Ellenőrzés, hogy a kijelentkezési művelet kérésre történik-e
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Minden munkamenet-adat törlése
    session_unset();
    session_destroy();

    // Átirányítás a főoldalra vagy bejelentkezési oldalra
    header('Location: ../index.php');
    exit();
} else {
    // Hibakezelés, ha nem GET metódusú a kérés
    echo 'Érvénytelen kérés.';
    exit();
}
?>
