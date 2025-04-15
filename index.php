<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés & Regisztráció</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2 id="form-title">Bejelentkezés</h2>

            <div class="social-login">
                <a href="https://accounts.google.com/signin" class="btn-social btn-google"><i class="fab fa-google"></i></a>
                <a href="https://www.facebook.com/login" class="btn-social btn-facebook"><i class="fab fa-facebook-f"></i></a>
                <p id="social-login-text">Vagy jelentkezz be az E-mail címeddel</p>
            </div>

            <?php
            session_start();
            if (isset($_SESSION['error'])): ?>
                <div class="error-message">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <form id="login-form" method="POST" action="controller/UserController.php?action=login">
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="login-email" name="email" required>
                    <label for="login-email">Email</label>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="login-password" name="password" required>
                    <label for="login-password">Jelszó</label>
                </div>
                <button type="submit" class="btn">Bejelentkezés</button>
                <p class="toggle-text">Nincs még fiókod? <a href="#" id="show-register">Regisztrálj</a></p>
            </form>

            <form id="register-form" class="hidden" method="POST" action="controller/UserController.php?action=register">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="register-name" name="name" required>
                    <label for="register-name">Teljes név</label>
                </div>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="register-email" name="email" required>
                    <label for="register-email">Email</label>
                </div>
                <div class="input-group">
                    <i class="fas fa-phone"></i>
                    <input type="text" id="register-phone" name="phone" required>
                    <label for="register-phone">Telefonszám</label>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="register-password" name="password" required>
                    <label for="register-password">Jelszó</label>
                </div>
                <button type="submit" class="btn">Regisztráció</button>
                <p class="toggle-text">Már van fiókod? <a href="#" id="show-login">Jelentkezz be</a></p>
            </form>
        </div>
    </div>

    <script src="../js/script.js"></script>
</body>
</html>