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
    <link rel="stylesheet" href="../css/mainstyle.css">
    <link rel="stylesheet" href="../css/darkmode.css">
    <title>Fiók</title>
</head>
<body>

<!-- A belebegő doboz -->
<div id="floating-box" class="floating-box">
    <button id="close-btn" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
    <input type="checkbox" id="darkmode-toggle" class="darkmode-toggle-input"/>
    <label for="darkmode-toggle" class="darkmode-toggle-label">
        <i class="fa-solid fa-sun"></i>
        <i class="fa-solid fa-moon"></i>
    </label>
</div>

<!-- A visszahozó nyíl -->
<div id="toggle-arrow" class="toggle-arrow"><i class="fa-solid fa-arrow-left"></i></div>


    <div class="dynamic-navbar">
        <div class="island">
            <input type="text" class="search-input" placeholder="Keresés...">
            <i class="fas fa-search search-icon"></i>

            <a href="landing.php">
                <i class="fa fa-home"></i>
                <span>Főoldal</span>
            </a>

            <a href="mainpage.php">
                <i class="fa fa-compass"></i>
                <span>Felfedezés</span>
            </a>
            
            <a href="appointments.php">
                <i class="fas fa-calendar-alt"></i>
                <span>Időpontjaim</span>
            </a>
            
            <a href="" class="active">
                <i class="fa fa-user"></i>
                <span>Fiók</span>
            </a>

            <a href="../models/logout.php" class="logout-button">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>


    <div class="account-container">
    <a href="javascript:history.back()" class="back-link">
        <i class="fas fa-arrow-left"></i> Vissza
    </a>
    <h1>Fiók Beállítások</h1>
    
    <div id="account-details" style="display: block;">
        <div class="detail-item">
            <label for="username">Felhasználónév:</label>
            <span id="name"><?php echo htmlspecialchars($user['name'] ?? ''); ?></span>
        </div>
        <div class="detail-item">
            <label for="email">Email:</label>
            <span id="email"><?php echo htmlspecialchars($user['email'] ?? ''); ?></span>
        </div>
        <div class="detail-item">
            <label for="phone">Telefonszám:</label>
            <span id="phone"><?php echo htmlspecialchars($user['phone'] ?? ''); ?></span>
        </div>
    </div>
    
    <div class="account-actions">
        <button type="button" class="btn btn-primary" onclick="toggleEditForm()">Adatok Módosítása</button>
        <button type="button" class="btn btn-danger" onclick="confirmDelete()">Fiók Törlése</button>
    </div>

    <div id="edit-form" style="display: none; margin-top: 20px;">
        <h2>Adatok Módosítása</h2>
        <form action="../models/update_user.php" method="POST">
            <div class="detail-item">
                <label for="edit-username">Felhasználónév:</label>
                <input type="text" id="edit-username" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="detail-item">
                <label for="edit-email">Email:</label>
                <input type="email" id="edit-email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="detail-item">
                <label for="edit-phone">Telefonszám:</label>
                <input type="text" id="edit-phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
            <div class="account-actions">
                <button type="submit" class="btn btn-primary" id="save">Mentés</button>
                <button type="button" class="btn btn-secondary" onclick="toggleEditForm()">Mégse</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/ratings.js"></script>
    <script src="../js/booking.js"></script>
    <script src="../js/account.js"></script>
    <script src="../js/theme.js"></script>
    <script src="../js/alert.js"></script>
    <script src="../js/appointments.js"></script>
    <script src="../js/service.js"></script>
</body>
</html>

