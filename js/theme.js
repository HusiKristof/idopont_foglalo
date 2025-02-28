document.addEventListener("DOMContentLoaded", function () {
    const themeToggleInput = document.getElementById('darkmode-toggle'); // Az input checkbox
    const body = document.body;

    // Mentett téma betöltése
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark-mode') {
        body.classList.add('dark-mode');
        themeToggleInput.checked = true; // Ha dark mode, akkor a kapcsoló legyen bekapcsolva
    } else {
        body.classList.add('light-mode');
        themeToggleInput.checked = false;
    }

    // Kapcsoló eseménykezelő
    themeToggleInput.addEventListener('change', function () {
        if (themeToggleInput.checked) {
            body.classList.remove('light-mode');
            body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark-mode');
        } else {
            body.classList.remove('dark-mode');
            body.classList.add('light-mode');
            localStorage.setItem('theme', 'light-mode');
        }
    });

    // Belebegő doboz vezérlés
    const floatingBox = document.getElementById("floating-box");
    const closeButton = document.getElementById("close-btn");
    const toggleArrow = document.getElementById("toggle-arrow");

    setTimeout(() => {
        floatingBox.classList.add("show");
    }, 500);

    closeButton.addEventListener("click", () => {
        floatingBox.classList.remove("show");
        toggleArrow.style.display = "block"; 
    });

    toggleArrow.addEventListener("click", () => {
        floatingBox.classList.add("show");
        toggleArrow.style.display = "none"; 
    });
});
