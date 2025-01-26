<?php
    session_start();
    if (!isset($_SESSION['user'])) {
        header('Location: index.php'); // Redirect to login if not logged in
        exit();
    }
    
    // Get user information from the session
    $user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/explore.css">
    <title>Fiók</title>
</head>
<body>
    <div class="dynamic-navbar">
        <div class="island">
            <input type="text" class="search-input" placeholder="Keresés...">
            <i class="fas fa-search search-icon"></i>

            <a href="/landing/mainpage.html">
                <i class="fa fa-home"></i>
                <span>Főoldal</span>
            </a>

            <a href="#" class="active">
                <i class="fa fa-compass"></i>
                <span>Felfedezés</span>
            </a>
            
            <a href="/appointments/appointments.html">
                <i class="fas fa-calendar-alt"></i>
                <span>Időpontjaim</span>
            </a>
            
            <a href="/account/account.html">
                <i class="fa fa-user"></i>
                <span>Fiók</span>
            </a>

        </div>
    </div>


    <div class="account-container">
        <a href="javascript:history.back()" class="back-link">
            <i class="fas fa-arrow-left"></i> Vissza
        </a>
        <h1>Fiók Beállítások</h1>
        <div class="account-details">
            <div class="detail-item">
                <label for="username">Felhasználónév:</label>
                <span id="username"><?php echo htmlspecialchars($user['name']); ?></span>
            </div>
            <div class="detail-item">
                <label for="email">Email:</label>
                <span id="email"><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
            <div class="detail-item">
                <label for="phone">Telefonszám:</label>
                <span id="phone"><?php echo htmlspecialchars($user['phone']); ?></span>
            </div>
        </div>
        <div class="account-actions">
            <button class="btn btn-primary">Adatok Módosítása</button>
            <button class="btn btn-danger">Fiók Törlése</button>
        </div>
    </div>
</body>
</html>