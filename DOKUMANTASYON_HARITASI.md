# 📖 Dokümantasyon Haritası

Bu dosya, tüm dokümantasyon dosyalarının neyi kapsadığını ve kimin okumacağını göstermektedir.

---

## 🎯 Hızlı Navigasyon

### Kurulum Yapmak İstiyorsanız
1. 🚀 [README.md](README.md) - Projeyi tanı (5 dk)
2. 🐳 [INSTALL.md](INSTALL.md) - Hızlı kurulum (10 dk)
3. 🔧 [KURULUM_TALIMAT.md](KURULUM_TALIMAT.md) - Detaylı rehber (OS spesifik)

### Kod İçeren Değişiklik Yapılacaksa
1. 🤝 [CONTRIBUTING.md](CONTRIBUTING.md) - Katkı kuralları
2. 📋 [CODE_OF_CONDUCT.md](CODE_OF_CONDUCT.md) - Davranış kuralları
3. 💻 Kod yazma (Kod standartlarını oku)
4. ✅ Test ve PR gönderme

### İleri Seviye Kurulum
1. 🔧 [CONFIG.md](CONFIG.md) - Yapılandırma detayları
2. 🚀 [DEPLOYMENT.md](DEPLOYMENT.md) - Deployment kontrol listesi
3. 🔒 [SECURITY.md](SECURITY.md) - Güvenlik yapılandırması

### Güvenlik Endişesi Varsa
1. 🔒 [SECURITY.md](SECURITY.md) - Güvenlik politikası
2. 📧 Report: security@masterstudio.local

---

## 📊 Dokümantasyon Özeti

| Dokuman | Amaç | Okuyuş Süresi | Kimin İçin |
|---------|------|--------|-|
| **README.md** | Proje tanıtımı, özellikler, hızlı başlangıç | 5-10 dk | Herkes |
| **INSTALL.md** | Temel kurulum rehberi | 10-15 dk | Yeni Kullanıcılar |
| **KURULUM_TALIMAT.md** | Detaylı, OS spesifik rehber | 30-60 dk | Sistem Yöneticileri |
| **CONTRIBUTING.md** | Katkı kuralları, kod standartları | 15-20 dk | Geliştiriciler |
| **CODE_OF_CONDUCT.md** | Davranış kuralları, sorumluluklar | 10-15 dk | Tüm Katılımcılar |
| **SECURITY.md** | Güvenlik politikası, best practices | 20-30 dk | Security Engineers |
| **CONFIG.md** | Yapılandırma rehberi, optimization | 30-45 dk | DevOps Engineers |
| **DEPLOYMENT.md** | Deployment kontrol listesi | 30-60 dk | DevOps / SysAdmin |
| **CHANGELOG.md** | Sürüm geçmişi, değişiklikler | 10 dk | Tüm Kullanıcılar |
| **LICENSE.md** | MIT Lisansı | 5 dk | Legal / Koruma |
| **AÇIK_KAYNAK_REHBERI.md** | Açık kaynak yapısı | 15-20 dk | Geliştiriciler |

---

## 👥 Yazılıma Göre Önerilen Okuma Yolu

### 🧑‍💼 Otel Yöneticisi
```
1. README.md (Features kısmı)
2. KURULUM_TALIMAT.md (İlgili OS bölümü)
3. Admin panolini incele
4. İagerik ve ayarları kustomize et
```

### 👨‍💻 Web Geliştirici
```
1. README.md (tam olarak oku)
2. CONTRIBUTING.md
3. CODE_OF_CONDUCT.md
4. Kodu keşfet
5. SECURITY.md (Güvenlik kontrol listesi)
6. Bir issue/PR hazırla
```

### 🔧 Sistem Yöneticisi
```
1. README.md
2. KURULUM_TALIMAT.md
3. CONFIG.md
4. DEPLOYMENT.md
5. SECURITY.md
6. Backup/Restore scriptleri (scripts/)
```

### 🏢 Kurumsal Deployment
```
1. README.md
2. KURULUM_TALIMAT.md (VDS/VPS bölümü)
3. CONFIG.md
4. DEPLOYMENT.md (tüm kontrol listesi)
5. SECURITY.md
6. Monitoring araçları kurmak
```

### 🔒 Güvenlik Uzmanı
```
1. README.md
2. SECURITY.md
3. CODE_OF_CONDUCT.md
4. Kod (XSS, SQLi, CSRF kontrolleri)
5. Deployment kontrol listesi (DEPLOYMENT.md)
```

---

## 📂 Dosya Yapısı Açıklaması

### Root Dosyalar
```
masterstudio/
├── README.md              ← Başla buradan!
├── INSTALL.md             ← Hızlı kurulum
├── KURULUM_TALIMAT.md     ← Detaylı (TR)
├── CONTRIBUTING.md        ← Katkı yapmak istiyorsanız
├── CODE_OF_CONDUCT.md     ← Davranış kuralları
├── SECURITY.md            ← Güvenlik
├── CONFIG.md              ← Yapılandırma
├── DEPLOYMENT.md          ← Deployment kontrol listesi
├── CHANGELOG.md           ← Sürüm geçmişi
├── LICENSE.md             ← Lisans
├── AÇIK_KAYNAK_REHBERI.md ← Açık kaynak yapısı
└── .github/               ← GitHub yapılandırması
    ├── ISSUE_TEMPLATE/    ← Issue şablonları
    └── workflows/         ← GitHub Actions
```

---

## 📖 Köprüler (Cross-References)

### README → INSTALL → KURULUM_TALIMAT
- README hızlı başlangıç sağlar
- INSTALL genel kurulum adımları
- KURULUM_TALIMAT detaylı OS spesifik talimatlar

### CONTRIBUTING → CODE_OF_CONDUCT → SECURITY
- CONTRIBUTING katkı süreci
- CODE_OF_CONDUCT davranış kuralları
- SECURITY güvenlik (kod seviyesi)

### CONFIG → DEPLOYMENT → SECURITY
- CONFIG yapılandırma detayları
- DEPLOYMENT kontrol listesi (production)
- SECURITY güvenlik hardening

---

## 📋 ALTERNATİF OKUMA YOLLARI

Farklı hedeflere göre:

### Hedef: Hızlı Prototip
```
Zaman: 30 dakika
1. README (Özellikler bölümü)
2. INSTALL (Docker bölümü)
3. Docker compose up -d
4. Admin paneline giriş
TAMAM! 🎉
```

### Hedef: Üretim Deployment
```
Zaman: 4-8 saat
1. README (tam)
2. KURULUM_TALIMAT.md
3. CONFIG.md
4. DEPLOYMENT.md (tüm kontrol listesi)
5. SECURITY.md
6. Systemdeki değişiklikleri yap
7. Test ve monitoring kur
8. Go live!
```

### Hedef: Kod Katkısı
```
Zaman: 1-2 saat (ilk seferinde)
1. README
2. CONTRIBUTING.md
3. CODE_OF_CONDUCT.md
4. Fork & Clone
5. Kod yazma vs kontrol listesi
6. PR hazırla
7. Feedback bekleme
```

### Hedef: Güvenlik Denetimi
```
Zaman: 3-4 saat
1. SECURITY.md (tam)
2. Kod inceleme (XSS, SQLi vs)
3. DEPLOYMENT.md (security bölümü)
4. Penetration testing (varsa)
5. Güvenlik raporu
```

---

## 💡 İpuçları

### Dosyaları Hızlı Ararken
- Ctrl+F (veya Cmd+F) - Sayfada ara
- GitHub arama - Tüm repository'de ara
- find komutu - Dosyaları bul

### Dokümantasyon Güncellemeleri
Dokümantasyon güncel mi kontrol et:
- Son güncellenme tarihi dosyanın altında
- CHANGELOG.md'de ne değiştiğini kontrol et

### Dil Seçimi
- Türkçe → KURULUM_TALIMAT.md, AÇIK_KAYNAK_REHBERI.md
- İngilizce → README.md, CONTRIBUTING.md, SECURITY.md
- Kod → Tüm PHP dosyaları (İngilizce)

---

## 📱 Mobil Uyarı

Mobil cihazdan okuyorsanız:
- Tüm markdownlar mobilde okunabilir
- Kod blokları kaydırılabilir
- Başlıklar hiyerarşik (> butonuyla açılabilir)

---

## 📞 Doğru Dokümantasyonu Bulamadıysanız?

| Soru | Cevap Bulabileceğiniz Yer |
|------|------|
| "Nasıl kurabilirim?" | INSTALL.md veya KURULUM_TALIMAT.md |
| "Kod standartları nedir?" | CONTRIBUTING.md |
| "Nasıl konfigüre ederim?" | CONFIG.md |
| "Güvenlik nedir?" | SECURITY.md |
| "Deployment kontrol listesi?" | DEPLOYMENT.md |
| "Lisans nedir?" | LICENSE.md |
| "Sürüm nedir?" | CHANGELOG.md |
| "Davranış kuralları?" | CODE_OF_CONDUCT.md |
| "Bug mu buldum?" | CONTRIBUTING.md → Issue Raporla |
| "Özellik öner?" | CONTRIBUTING.md → Issue Aç |

---

## ✅ Okuma Kontrolü

Bir dokümantasyonu okuduktan sonra:

- [ ] Ana noktaları anladım
- [ ] Hangi adımlar atılacağını biliyorum
- [ ] İlişkili dosyalara baktım
- [ ] Sorularım varsa Issue açtım

---

## 🎯 Hedef Tarafından Başlayacağınız Dokman

```
Prototip Yapmak İstiyorum
          ↓
      README.md
          ↓
      INSTALL.md
          ↓
    Kurulmuş, Bitti!

Üretim Server'a Koymak İstiyorum
          ↓
      README.md
          ↓
    KURULUM_TALIMAT.md
          ↓
      CONFIG.md
          ↓
    DEPLOYMENT.md
          ↓
      SECURITY.md
          ↓
    Deployment Kontrol Listesini Tamamla
          ↓
    Go Live!

Kod Yazmak İstiyorum
          ↓
    CONTRIBUTING.md
          ↓
    CODE_OF_CONDUCT.md
          ↓
    Kod yazma & Testing
          ↓
      PR Gönder
          ↓
    Review & Merge
```

---

## 📚 Harici Kaynaklar

Ek kaynak olarak:
- [PHP Resmi Dokümantasyonu](https://www.php.net)
- [MySQL Öğretici](https://dev.mysql.com/doc/)
- [Docker Tutorial](https://docs.docker.com)
- [Apache Manual](https://httpd.apache.org/docs/)

---

**Son Güncelleme:** Şubat 2026

Doğru dokümantasyonu buldu musunuz? 
- Evet → Başarılı! 🎉
- Hayır → [GitHub Issue açın](https://github.com/yamanfurkan353-eng/masterstudio/issues)
