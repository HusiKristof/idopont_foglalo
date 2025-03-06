<?php
session_start();

// Check if this is a GET request to the root/default endpoint
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/') {
    if (!isset($_SESSION['user'])) {
        // If not logged in, stay on the homepage
        // No redirection needed since this is the default page
    } else {
        // If logged in, redirect to main page
        header('Location: /mainpage');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/homestyle.css">
    <title>Időpontfoglalás</title>
</head>
<body>
    <header class="hero-section">
        <div class="hero-content">
            <h1>Üdvözlünk az Időpontfoglaló Rendszerünkben!</h1>
            <p>Egyszerű és gyors időpontfoglalás különféle szolgáltatásokhoz.</p>
            <a href="../index.php">
                <i class="fa fa-compass"></i>
                <span>A Felfedezéshez!</span>
            </a>
        </div>
    </header>
    <section class="features-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 feature">
                    <i class="fas fa-calendar-check fa-3x"></i>
                    <h3>Könnyű időpontfoglalás</h3>
                    <p>Válaszd ki a neked megfelelő időpontot néhány kattintással.</p>
                </div>
                <div class="col-md-4 feature">
                    <i class="fas fa-users fa-3x"></i>
                    <h3>Széleskörű szolgáltatások</h3>
                    <p>Egészségügy, szépségipar, ügyintézés és sok más lehetőség közül választhatsz.</p>
                </div>
                <div class="col-md-4 feature">
                    <i class="fas fa-bell fa-3x"></i>
                    <h3>Emlékeztetők</h3>
                    <p>Időben értesítünk, hogy ne felejtsd el a foglalásodat.</p>
                </div>
            </div>
        </div>
    </section>
</body>
</html>