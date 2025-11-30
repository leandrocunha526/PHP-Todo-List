<script>
    document.addEventListener("DOMContentLoaded", () => {
        const body = document.body;
        const toggleBtn = document.getElementById("themeToggle");

        function applyTheme(theme) {
            body.dataset.theme = theme;
            localStorage.setItem("theme", theme);
            toggleBtn.textContent = theme === "dark" ? "☀️ Tema Claro" : "🌙 Tema Escuro";
        }

        const saved = localStorage.getItem("theme") ||
            (window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light");

        applyTheme(saved);

        toggleBtn.addEventListener("click", () => {
            const nextTheme = body.dataset.theme === "dark" ? "light" : "dark";
            applyTheme(nextTheme);
        });
    });
</script>
</div>

</body>

</html>
