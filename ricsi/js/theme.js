$(document).ready(function() {
    /* ---------------------------------------------------------------------------darkmode-lightmode */
    const themeToggleBtn = document.getElementById('theme-toggle');
    const body = document.body;

    // Load the saved theme from localStorage
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        body.classList.add(savedTheme);
        themeToggleBtn.textContent = savedTheme === 'dark-mode' ? 'Light Mode' : 'Dark Mode';
    } else {
        body.classList.add('light-mode');
        themeToggleBtn.textContent = 'Dark Mode';
    }

    themeToggleBtn.addEventListener('click', function() {
        if (body.classList.contains('light-mode')) {
            body.classList.remove('light-mode');
            body.classList.add('dark-mode');
            themeToggleBtn.textContent = 'Light Mode';
            localStorage.setItem('theme', 'dark-mode');
        } else {
            body.classList.remove('dark-mode');
            body.classList.add('light-mode');
            themeToggleBtn.textContent = 'Dark Mode';
            localStorage.setItem('theme', 'light-mode');
        }
    });
});