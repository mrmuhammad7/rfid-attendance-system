// Theme management - applies to all pages
(function () {
    const html = document.documentElement;

    // Load saved theme on page load
    const savedTheme = localStorage.getItem("theme") || "dark";
    html.setAttribute("data-theme", savedTheme);
    updateThemeIcon(savedTheme);

    // Listen for theme changes in other tabs/windows
    window.addEventListener("storage", (e) => {
        if (e.key === "theme" && e.newValue) {
            html.setAttribute("data-theme", e.newValue);
            updateThemeIcon(e.newValue);
        }
    });

    // Setup theme toggle button if it exists
    function setupThemeToggle() {
        const themeToggle = document.getElementById("themeToggle");
        if (themeToggle) {
            themeToggle.addEventListener("click", (e) => {
                e.preventDefault();
                const currentTheme = html.getAttribute("data-theme");
                const newTheme = currentTheme === "dark" ? "light" : "dark";
                html.setAttribute("data-theme", newTheme);
                localStorage.setItem("theme", newTheme);
                updateThemeIcon(newTheme);
            });
        }
    }

    function updateThemeIcon(theme) {
        const themeIcon = document.getElementById("themeIcon");
        if (themeIcon) {
            if (theme === "dark") {
                themeIcon.innerHTML = '<i class="fas fa-sun"></i>';
            } else {
                themeIcon.innerHTML = '<i class="fas fa-moon"></i>';
            }
        }
    }

    // Wait for DOM to be ready
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", setupThemeToggle);
    } else {
        setupThemeToggle();
    }
})();
