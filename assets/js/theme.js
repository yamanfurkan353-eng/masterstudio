// Theme Toggle - Açık/Karanlık Mod

document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const htmlElement = document.documentElement;
    const body = document.body;

    // Tema tercihini localStorage'dan al
    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);

    // Tema değişim butonu
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = localStorage.getItem('theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            setTheme(newTheme);
        });
    }

    function setTheme(theme) {
        localStorage.setItem('theme', theme);
        
        if (theme === 'dark') {
            body.classList.add('dark-mode');
            htmlElement.setAttribute('data-theme', 'dark');
        } else {
            body.classList.remove('dark-mode');
            htmlElement.setAttribute('data-theme', 'light');
        }

        // Tema CSS dosyasını güncelle
        let themeStyle = document.getElementById('theme-style');
        if (themeStyle) {
            themeStyle.href = theme === 'dark' ? 
                'assets/css/dark.css' : 
                'assets/css/style.css';
        }
    }
});
