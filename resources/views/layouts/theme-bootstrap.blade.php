<script>
    (() => {
        const savedTheme = localStorage.getItem('kaskelas-theme');
        document.documentElement.dataset.theme = savedTheme === 'dark' ? 'dark' : 'light';
    })();
</script>
