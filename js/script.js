const container = document.getElementById('container');
const registerButton = document.getElementById('register');
const loginButton = document.getElementById('login');

//login es regisztracio oldal switchelese
registerButton.addEventListener('click', (e) => {
    e.preventDefault();
    container.classList.add("active");
    document.title = 'Regisztráció';
});

loginButton.addEventListener('click', (e) => {
    e.preventDefault();
    container.classList.remove("active");
    document.title = 'Bejelentkezés';
});

//telefon input automatikus formázása
const phoneInput = document.getElementById('phone');
phoneInput.addEventListener('focus', () => {
    if (phoneInput.value === '') {
        phoneInput.value = '+36 ';
    }
});

phoneInput.addEventListener('blur', () => {
    if (phoneInput.value === '+36 ') {
        phoneInput.value = '';
    }
});

phoneInput.addEventListener('input', (e) => {
    let value = phoneInput.value;
    if (!value.startsWith('+36 ')) {
        value = '+36 ' + value.replace(/[^0-9]/g, '');
    } else {
        value = '+36 ' + value.slice(4).replace(/[^0-9]/g, '');
    }
    const digits = value.replace(/[^0-9]/g, '').slice(2);
    if (digits.length > 2 && digits.length <= 5) {
        value = `+36 ${digits.slice(0, 2)}-${digits.slice(2)}`;
    } else if (digits.length > 5) {
        value = `+36 ${digits.slice(0, 2)}-${digits.slice(2, 5)}-${digits.slice(5, 9)}`;
    }
    phoneInput.value = value;
});

document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        //input validáció
        const inputs = this.querySelectorAll('input[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Kérem töltsön ki minden kötelező mezőt!');
        }
        
    });
});

//default link viselkedés letiltása
document.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        
        //kulso linkek megnyitása új lapon
        if (href && href.startsWith('http')) return;
        
        e.preventDefault();
        
        if (href === '#') return;
        
        if (href) {
            window.location.href = href;
        }
    });
});

$(function() {
    const $password = $('#register-password');
    const $bar = $('#password-strength-bar');
    const $hints = $('#password-hints');

    function checkStrength(pw) {
        let score = 0;
        let hints = [];

        if (pw.length >= 8) score++; else hints.push('Legalább 8 karakter hosszú legyen');
        if (/[A-Z]/.test(pw)) score++; else hints.push('Tartalmazzon nagybetűt');
        if (/[0-9]/.test(pw)) score++; else hints.push('Tartalmazzon számot');
        if (/[^A-Za-z0-9]/.test(pw)) score++; else hints.push('Tartalmazzon speciális karaktert (pl. !@#$%)');

        return {score, hints};
    }

    $password.on('input', function() {
        const val = $(this).val();
        const {score, hints} = checkStrength(val);

        //progress csik
        let color = '#ff4d4d', width = '25%';
        if (score === 2) { color = '#ffc107'; width = '50%'; }
        if (score === 3) { color = '#ffe066'; width = '75%'; }
        if (score === 4) { color = '#28a745'; width = '100%'; }

        $bar.css({background: color, width: width});

        //hintek megjelenítése
        if (val.length > 0 && hints.length > 0) {
            $hints.html(hints.map(h => `<li>${h}</li>`).join(''));
        } else {
            $hints.html('');
        }
    });

    //ha jelszó gyenge, akkor a regisztráció letiltása
    $('#registerForm').on('submit', function(e) {
        const val = $password.val();
        const {score, hints} = checkStrength(val);
        if (score < 4) {
            e.preventDefault();
            $bar.css({background: '#ff4d4d', width: '25%'});
            $hints.html('<li>A jelszónak legalább 8 karakterből kell állnia, tartalmaznia kell nagybetűt, számot és speciális karaktert.</li>');
            $password.focus();
        }
    });
});