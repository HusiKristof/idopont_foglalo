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
                    alert(response.message || 'Login failed');
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
        
        const password = $('#reg-password').val();
        const passwordCheck = checkPasswordStrength(password);

        if (!passwordCheck.isValid) {
            showAlert('A jelszó nem felel meg a minimum követelményeknek', 'error');
            return;
        }

        const formData = {
            name: $('#reg-name').val(),
            email: $('#reg-email').val(),
            phone: $('#reg-phone').val(),
            password: password
        };

        // Validate phone number format
        const phonePattern = /^\+36 \d{2}-\d{3}-\d{4}$/;
        if (!phonePattern.test(formData.phone)) {
            showAlert('Érvénytelen telefonszám formátum', 'error');
            return;
        }

        $.ajax({
            url: 'controller/UserController.php?action=register',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response) {
                if (response.status === 'success') {
                    showAlert('Sikeres regisztráció! Kérlek jelentkezz be.', 'success');
                    container.classList.remove("active");
                } else {
                    showAlert(response.message || 'Sikertelen regisztráció', 'error');
                }
            },
            error: function(xhr, status, error) {
                showAlert('Hiba: ' + error, 'error');
            }
        });
    });
});