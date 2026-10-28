<script>
    const themeToggleButtons = document.querySelectorAll('[data-theme-toggle]');

    function updateThemeToggleButtons() {
        const isDark = document.documentElement.dataset.theme === 'dark';

        themeToggleButtons.forEach((button) => {
            const label = isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap';
            button.setAttribute('aria-label', label);
            button.setAttribute('aria-pressed', String(isDark));
            button.setAttribute('title', label);
        });
    }

    themeToggleButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            document.documentElement.dataset.theme = nextTheme;
            localStorage.setItem('kaskelas-theme', nextTheme);
            updateThemeToggleButtons();
        });
    });

    updateThemeToggleButtons();
</script>
