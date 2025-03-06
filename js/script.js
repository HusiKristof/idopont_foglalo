document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const formTitle = document.getElementById('form-title');
    const socialLoginText = document.getElementById('social-login-text');
    
    const showRegister = document.getElementById('show-register');
    const showLogin = document.getElementById('show-login');
    const phoneInput = document.getElementById('register-phone');

    showRegister.addEventListener('click', (e) => {
        e.preventDefault();
        loginForm.classList.add('hidden');
        registerForm.classList.remove('hidden');
        formTitle.textContent = "Regisztráció";
        socialLoginText.textContent = "Vagy regisztrálj az E-mail címeddel";
    });

    showLogin.addEventListener('click', (e) => {
        e.preventDefault();
        registerForm.classList.add('hidden');
        loginForm.classList.remove('hidden');
        formTitle.textContent = "Bejelentkezés";
        socialLoginText.textContent = "Vagy jelentkezz be az E-mail címeddel";
    });

    phoneInput.addEventListener('input', (e) => {
        let value = phoneInput.value.replace(/[^0-9+]/g, '');
        if (!value.startsWith('+')) {
            value = '+' + value;
        }
        value = value.substring(0, 12); 
        value = value.replace(/(\+\d{2})(\d{2})(\d{3})(\d{4})/, '$1 $2-$3-$4').trim();
        phoneInput.value = value;
    });
});