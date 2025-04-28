<?php session_start(); ?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés & Regisztráció</title>
    <link rel="stylesheet" href="../css/style.css">
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

            <div id="error-message" class="error-message" style="display:none;"></div>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form id="loginForm" method="POST" style="display:block;">
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

            <!-- Registration Form -->
            <form id="registerForm" method="POST" style="display:none;">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="reg-name" name="name" required>
                    <label for="reg-name">Teljes név</label>
                </div>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="reg-email" name="email" required>
                    <label for="reg-email">Email</label>
                </div>
                <div class="input-group">
                    <i class="fas fa-phone"></i>
                    <input type="text" id="reg-phone" name="phone" required placeholder="+36 12-345-6789">
                    <label for="reg-phone">Telefonszám</label>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="reg-password" name="password" required>
                    <label for="reg-password">Jelszó</label>
                </div>
                <div id="password-strength-bar" style="height:8px; border-radius:4px; background:#eee; margin-bottom:6px; width:100%;"></div>
                <ul id="password-hints" style="text-align:left; font-size:13px; margin:0 0 10px 0; padding-left:18px; color:#888;"></ul>
                <div id="register-error-message" class="error-message" style="display:none;"></div>
                <button type="submit" class="btn">Regisztráció</button>
                <p class="toggle-text">Már van fiókod? <a href="#" id="show-login">Jelentkezz be</a></p>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/auth.js"></script>
    <script src="../js/script.js"></script>
    <script>
    // Toggle forms
    $('#show-register').on('click', function(e) {
        e.preventDefault();
        $('#loginForm').hide();
        $('#registerForm').show();
        $('#form-title').text('Regisztráció');
        $('#social-login-text').text('Vagy regisztrálj az E-mail címeddel');
    });
    $('#show-login').on('click', function(e) {
        e.preventDefault();
        $('#registerForm').hide();
        $('#loginForm').show();
        $('#form-title').text('Bejelentkezés');
        $('#social-login-text').text('Vagy jelentkezz be az E-mail címeddel');
    });

    // Show error from AJAX
    function showError(msg) {
        $('#error-message').text(msg).show();
    }
    </script>
</body>
</html>