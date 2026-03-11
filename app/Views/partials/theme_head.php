<script>
  (() => {
    try {
      const storedTheme = window.localStorage.getItem('library-theme');
      const theme = storedTheme === 'dark' || storedTheme === 'light' ? storedTheme : 'light';
      const themeColorMeta = document.querySelector('meta[name="theme-color"]');
      document.documentElement.setAttribute('data-theme', theme);

      if (themeColorMeta) {
        themeColorMeta.setAttribute('content', theme === 'dark' ? '#0f172a' : '#4c7a5e');
      }
    } catch (error) {
      document.documentElement.setAttribute('data-theme', 'light');
    }
  })();
</script>
