<script>
    (function () {
        var storageKey = 'buruan_sae_theme';
        var savedTheme = null;
        try {
            savedTheme = localStorage.getItem(storageKey);
        } catch (e) {}

        var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        var isDark = savedTheme === 'dark' || (savedTheme === null && prefersDark);

        if (isDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        window.toggleBuruanSaeTheme = function () {
            var root = document.documentElement;
            var nowDark = root.classList.toggle('dark');
            try {
                localStorage.setItem(storageKey, nowDark ? 'dark' : 'light');
            } catch (e) {}
        };
    })();
</script>
