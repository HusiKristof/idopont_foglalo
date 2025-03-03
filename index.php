<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Bejelentkezés</title>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container signUp">
            <form action="controller/UserController.php?action=register" method="POST">

                <h1>Regisztráció</h1>
                <div class="icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                </div>

                <span>Vagy regisztrálj az E-mail címeddel</span>

                <div class="input-container">
                    <i class="fas fa-user"></i>
                    <input type="text" placeholder="Teljes név" name="name" id="name" >
                    <label for="name">Teljes név</label>
                </div>

                <div class="input-container">
                    <i class="fas fa-envelope"></i>
                    <input type="email" placeholder="E-mail" name="email" id="email">
                    <label for="email">E-mail</label>
                </div>

                <div class="input-container">
                    <i class="fa fa-phone"></i>
                    <input type="tel" placeholder="Telefonszám" name="phone" id="phone" pattern="\+36\s[0-9]{2}-[0-9]{3}-[0-9]{4}" required>
                    <label for="phone">Telefonszám</label>
                </div>

                <div class="input-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" placeholder="Jelszó" name="password" id="password">
                    <label for="password">Jelszó</label>
                </div>
                <button type="submit" class="btn" value="signUp" name="signUp" id="signUp">Regisztráció</button>
            </form>
        </div>

        <div class="form-container sign-in">
            <form action="../controller/UserController.php?action=login" method="POST">

                <h1>Bejelentkezés</h1>
                <div class="icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                </div>

                <span>Vagy jelentkezz be az E-mail címeddel</span>

                <div class="input-container">
                    <i class="fas fa-envelope"></i>
                    <input type="email" placeholder="E-mail" name="email" id="email">
                    <label for="email">E-mail</label>
                </div>

                <div class="input-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" placeholder="Jelszó" name="password" id="password">
                    <label for="password">Jelszó</label>
                </div>

                <a href="#">Elfelejtetted a jelszavad?</a>
                <button type="submit" class="btn" value="signIn" name="signIn">Bejelentkezés</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">

                <div class="toggle-panel toggle-left">
                    <h1>Üdvözlünk az oldalunkon!</h1>
                    <p>Regisztrálj be az adataiddal hogy hozzáférj a szolgáltatásokhoz.</p>
                    <button class="hidden" id="login">Bejelentkezés</button>
                </div>

                <div class="toggle-panel toggle-right">
                    <h1>Üdvözlünk újra!</h1>
                    <p>Ird be az adataid hogy hozzáférj a szolgáltatásokhoz.</p>
                    <button class="hidden" id="register">Regisztráció</button>
                    
                </div>
            </div>
        </div>
    </div>
    <script src="../js/script.js"></script>
</body>
</html>