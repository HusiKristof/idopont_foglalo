$(document).ready(function() {
    // Login form submission
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Login form submitted');
        
        const formData = {
            email: $('#login-email').val(),
            password: $('#login-password').val()
        };
        console.log('Sending data:', formData);

        $.ajax({
            url: 'controller/UserController.php?action=login',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response) {
                console.log('Server response:', response);
                if (response.status === 'success') {
                    window.location.href = 'views/mainpage.php';
                } else {
                    $('#error-message').text('Hibás bejelentkezés!').show();
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajax error:', {xhr, status, error});
                alert('Error: ' + error);
            }
        });
    });

    // Registration form submission
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();
        $('#register-error-message').hide();

        const name = $('#reg-name').val();
        const email = $('#reg-email').val();
        const phone = $('#reg-phone').val();
        const password = $('#reg-password').val();

        // Email regex: must be something@something.domain (domain at least 2 chars)
        const emailPattern = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;
        if (!emailPattern.test(email)) {
            showRegisterError('Érvénytelen email cím formátum');
            return;
        }

        // Phone number format
        const phonePattern = /^\+36 \d{2}-\d{3}-\d{4}$/;
        if (!phonePattern.test(phone)) {
            showRegisterError('Érvénytelen telefonszám formátum');
            return;
        }

        const passwordCheck = checkPasswordStrength(password);
        if (!passwordCheck.isValid) {
            showRegisterError('A jelszó nem felel meg a minimum követelményeknek');
            return;
        }

        $.ajax({
            url: 'controller/UserController.php?action=register',
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                name: name,
                email: email,
                phone: phone,
                password: password
            }),
            success: function(response) {
                if (response.status === 'success') {
                    $('#register-error-message').css('color', '#28a745').text('Sikeres regisztráció! Átirányítás...').show();
                    setTimeout(function() {
                        window.location.reload(); // or window.location.href = 'index.php';
                    }, 1200);
                } else {
                    showRegisterError(response.message || 'Sikertelen regisztráció');
                }
            },
            error: function(xhr) {
                showRegisterError('Hiba történt a regisztráció során.');
            }
        });
    });

    $('#reg-password').on('input', function() {
        const val = $(this).val();
        const {score, hints} = checkPasswordStrength(val);

        // Progress bar color and width
        let color = '#ff4d4d', width = '25%';
        if (score === 2) { color = '#ffc107'; width = '50%'; }
        if (score === 3) { color = '#ffe066'; width = '75%'; }
        if (score === 4) { color = '#28a745'; width = '100%'; }

        $('#password-strength-bar').css({background: color, width: width});

        // Show hints
        if (val.length > 0 && hints.length > 0) {
            $('#password-hints').html(hints.map(h => `<li>${h}</li>`).join(''));
        } else {
            $('#password-hints').html('');
        }
    });

    $('#reg-phone').on('input', function() {
        let val = $(this).val().replace(/\D/g, '');
        if (val.startsWith('36')) val = '+' + val;
        else if (val.startsWith('06')) val = '+36' + val.slice(2);
        else if (!val.startsWith('+36')) val = '+36' + val;

        // Format: +36 12-345-6789
        if (val.length > 3) val = val.slice(0, 3) + ' ' + val.slice(3);
        if (val.length > 6) val = val.slice(0, 6) + '-' + val.slice(6);
        if (val.length > 10) val = val.slice(0, 10) + '-' + val.slice(10, 14);
        $(this).val(val.slice(0, 15));
    });
});

function showRegisterError(msg) {
    $('#register-error-message').text(msg).show();
}

function checkPasswordStrength(password) {
    let score = 0;
    let hints = [];

    if (password.length >= 8) score++; else hints.push('Legalább 8 karakter hosszú legyen');
    if (/[A-Z]/.test(password)) score++; else hints.push('Tartalmazzon nagybetűt');
    if (/[0-9]/.test(password)) score++; else hints.push('Tartalmazzon számot');
    if (/[^A-Za-z0-9]/.test(password)) score++; else hints.push('Tartalmazzon speciális karaktert (pl. !@#$%)');

    return {
        score,
        hints,
        isValid: score === 4
    };
}