// Dil Seçimi - Turkish / English

const translations = {
    tr: {
        home: 'Anasayfa',
        rooms: 'Odalarımız',
        about: 'Hakkımızda',
        contact: 'İletişim',
        book_now: 'Şimdi Rezervasyon Yap',
        hello: 'Hoş Geldiniz',
        welcome: 'MasterStudio Hotel\'e Hoş Geldiniz'
    },
    en: {
        home: 'Home',
        rooms: 'Rooms',
        about: 'About',
        contact: 'Contact',
        book_now: 'Book Now',
        hello: 'Welcome',
        welcome: 'Welcome to MasterStudio Hotel'
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const langToggle = document.getElementById('lang-toggle');
    
    // Kayıtlı dili al
    const savedLang = localStorage.getItem('lang') || 'tr';
    setLanguage(savedLang);

    // Dil değişim event listeneri
    if (langToggle) {
        langToggle.addEventListener('change', function() {
            setLanguage(this.value);
        });
    }

    function setLanguage(lang) {
        localStorage.setItem('lang', lang);
        
        // Dil metinlerini güncelle
        document.documentElement.lang = lang;
        
        // Tüm data-lang-key attribute'li elementleri güncelle
        document.querySelectorAll('[data-lang-key]').forEach(element => {
            const key = element.getAttribute('data-lang-key');
            if (translations[lang] && translations[lang][key]) {
                element.textContent = translations[lang][key];
            }
        });

        // Sayfa yenile (dinamik içerik için)
        // Sunucu tarafında dil değişimini işle
    }
});
