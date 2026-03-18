#!/bin/bash
#
# MasterStudio Hotel - Otomatik VPS/cPanel Kurulum Scripti
# Kullanım: chmod +x install.sh && sudo ./install.sh
#
# Desteklenen sistemler:
#   - Ubuntu 20.04/22.04/24.04
#   - Debian 11/12
#   - CentOS 8/9 / AlmaLinux / Rocky Linux
#   - cPanel/WHM sunucuları
#

set -e

# --- Renkli Çıktı ---
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'
BOLD='\033[1m'

print_header() {
    echo ""
    echo -e "${CYAN}╔══════════════════════════════════════════════╗${NC}"
    echo -e "${CYAN}║   ${BOLD}MasterStudio Hotel - Kurulum Sihirbazı${NC}${CYAN}    ║${NC}"
    echo -e "${CYAN}║              Versiyon 2.0                    ║${NC}"
    echo -e "${CYAN}╚══════════════════════════════════════════════╝${NC}"
    echo ""
}

success() { echo -e "${GREEN}[OK]${NC} $1"; }
error()   { echo -e "${RED}[HATA]${NC} $1"; }
info()    { echo -e "${BLUE}[BİLGİ]${NC} $1"; }
warn()    { echo -e "${YELLOW}[UYARI]${NC} $1"; }

# --- Sistem Algılama ---
detect_os() {
    if [ -f /etc/os-release ]; then
        . /etc/os-release
        OS=$ID
        OS_VERSION=$VERSION_ID
    elif [ -f /etc/redhat-release ]; then
        OS="centos"
    else
        OS="unknown"
    fi

    # cPanel kontrolü
    if [ -d "/usr/local/cpanel" ]; then
        IS_CPANEL=true
    else
        IS_CPANEL=false
    fi

    info "İşletim Sistemi: $OS $OS_VERSION"
    if [ "$IS_CPANEL" = true ]; then
        info "cPanel/WHM algılandı"
    fi
}

# --- Gereksinim Kontrolü ---
check_root() {
    if [ "$EUID" -ne 0 ] && [ "$IS_CPANEL" = false ]; then
        error "Bu script root yetkisi ile çalıştırılmalıdır."
        echo "  sudo ./install.sh"
        exit 1
    fi
}

# --- Paket Kurulumu ---
install_packages_debian() {
    info "Paketler güncelleniyor..."
    apt-get update -qq

    info "Apache, PHP, MySQL kuruluyor..."
    DEBIAN_FRONTEND=noninteractive apt-get install -y -qq \
        apache2 \
        mysql-server \
        php php-mysqli php-mbstring php-json php-gd php-curl php-xml php-zip \
        libapache2-mod-php \
        unzip curl git > /dev/null 2>&1

    a2enmod rewrite > /dev/null 2>&1
    a2enmod headers > /dev/null 2>&1
    systemctl restart apache2
    systemctl enable apache2 mysql
    success "Paketler kuruldu (Apache + PHP + MySQL)"
}

install_packages_rhel() {
    info "Paketler kuruluyor..."
    if command -v dnf &> /dev/null; then
        PKG_MGR="dnf"
    else
        PKG_MGR="yum"
    fi

    $PKG_MGR install -y -q \
        httpd \
        mysql-server \
        php php-mysqlnd php-mbstring php-json php-gd php-curl php-xml php-zip \
        unzip curl git > /dev/null 2>&1

    systemctl start httpd mysqld
    systemctl enable httpd mysqld
    success "Paketler kuruldu (HTTPD + PHP + MySQL)"
}

# --- Veritabanı Kurulumu ---
setup_database() {
    info "Veritabanı ayarlanıyor..."

    echo -e "${YELLOW}"
    read -p "  MySQL root şifresi (yeni kurulumda boş bırakın): " -s MYSQL_ROOT_PASS
    echo ""
    read -p "  Otel DB adı [masterstudio_hotel]: " DB_NAME
    DB_NAME=${DB_NAME:-masterstudio_hotel}
    read -p "  Otel DB kullanıcısı [hotel_user]: " DB_USER
    DB_USER=${DB_USER:-hotel_user}
    read -p "  Otel DB şifresi: " -s DB_PASS
    echo ""
    echo -e "${NC}"

    if [ -z "$DB_PASS" ]; then
        DB_PASS=$(openssl rand -base64 16 | tr -dc 'a-zA-Z0-9' | head -c 16)
        warn "Otomatik şifre oluşturuldu: $DB_PASS"
    fi

    # MySQL komutu
    if [ -n "$MYSQL_ROOT_PASS" ]; then
        MYSQL_CMD="mysql -u root -p$MYSQL_ROOT_PASS"
    else
        MYSQL_CMD="mysql -u root"
    fi

    $MYSQL_CMD -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
    $MYSQL_CMD -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';" 2>/dev/null
    $MYSQL_CMD -e "GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';" 2>/dev/null
    $MYSQL_CMD -e "FLUSH PRIVILEGES;" 2>/dev/null

    # SQL dosyasını içe aktar
    $MYSQL_CMD "$DB_NAME" < "$INSTALL_DIR/sql/database.sql" 2>/dev/null

    success "Veritabanı '$DB_NAME' oluşturuldu"
}

# --- Admin Hesabı ---
setup_admin() {
    echo ""
    info "Admin hesabı oluşturuluyor..."
    echo -e "${YELLOW}"
    read -p "  Admin kullanıcı adı [admin]: " ADMIN_USER
    ADMIN_USER=${ADMIN_USER:-admin}
    read -p "  Admin e-posta: " ADMIN_EMAIL
    read -p "  Admin şifresi (min 8 karakter): " -s ADMIN_PASS
    echo ""
    echo -e "${NC}"

    if [ ${#ADMIN_PASS} -lt 8 ]; then
        error "Şifre en az 8 karakter olmalıdır!"
        exit 1
    fi

    HASHED_PASS=$(php -r "echo password_hash('$ADMIN_PASS', PASSWORD_BCRYPT);")

    if [ -n "$MYSQL_ROOT_PASS" ]; then
        MYSQL_CMD="mysql -u root -p$MYSQL_ROOT_PASS"
    else
        MYSQL_CMD="mysql -u root"
    fi

    $MYSQL_CMD "$DB_NAME" -e "DELETE FROM users WHERE username = 'admin';" 2>/dev/null
    $MYSQL_CMD "$DB_NAME" -e "INSERT INTO users (username, password, email, role) VALUES ('$ADMIN_USER', '$HASHED_PASS', '$ADMIN_EMAIL', 'admin');" 2>/dev/null

    success "Admin hesabı oluşturuldu: $ADMIN_USER"
}

# --- Dosya Kurulumu ---
setup_files() {
    info "Dosyalar ayarlanıyor..."

    # Web dizinini belirle
    if [ "$IS_CPANEL" = true ]; then
        read -p "  cPanel kullanıcı adı: " CPANEL_USER
        INSTALL_DIR="/home/$CPANEL_USER/public_html"
    else
        INSTALL_DIR="/var/www/html/hotel"
        read -p "  Kurulum dizini [$INSTALL_DIR]: " CUSTOM_DIR
        INSTALL_DIR=${CUSTOM_DIR:-$INSTALL_DIR}
    fi

    # Dizin oluştur
    mkdir -p "$INSTALL_DIR"

    # Dosyaları kopyala
    SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
    if [ "$SCRIPT_DIR" != "$INSTALL_DIR" ]; then
        cp -r "$SCRIPT_DIR"/* "$INSTALL_DIR/" 2>/dev/null || true
        cp "$SCRIPT_DIR"/.htaccess "$INSTALL_DIR/" 2>/dev/null || true
    fi

    # .env oluştur
    cat > "$INSTALL_DIR/.env" << ENVEOF
DB_HOST=localhost
DB_USER=$DB_USER
DB_PASS=$DB_PASS
DB_NAME=$DB_NAME
PHP_ENV=production
ENVEOF
    chmod 600 "$INSTALL_DIR/.env"

    # Dosya izinleri
    if [ "$IS_CPANEL" = true ]; then
        chown -R "$CPANEL_USER:$CPANEL_USER" "$INSTALL_DIR"
    else
        chown -R www-data:www-data "$INSTALL_DIR" 2>/dev/null || chown -R apache:apache "$INSTALL_DIR" 2>/dev/null
    fi
    chmod -R 755 "$INSTALL_DIR"
    chmod 600 "$INSTALL_DIR/.env"

    # install.php'yi sil
    rm -f "$INSTALL_DIR/install.php"

    success "Dosyalar $INSTALL_DIR dizinine kuruldu"
}

# --- Apache Konfigürasyonu ---
setup_apache() {
    if [ "$IS_CPANEL" = true ]; then
        info "cPanel Apache otomatik yönetir, ek ayar gerekmez."
        return
    fi

    info "Apache konfigüre ediliyor..."

    # VirtualHost
    if [ "$OS" = "ubuntu" ] || [ "$OS" = "debian" ]; then
        cat > /etc/apache2/sites-available/masterstudio.conf << VHEOF
<VirtualHost *:80>
    ServerAdmin admin@masterstudio.com
    DocumentRoot $INSTALL_DIR

    <Directory $INSTALL_DIR>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <IfModule mod_headers.c>
        Header set X-Content-Type-Options "nosniff"
        Header set X-Frame-Options "SAMEORIGIN"
        Header set X-XSS-Protection "1; mode=block"
    </IfModule>

    ErrorLog \${APACHE_LOG_DIR}/masterstudio_error.log
    CustomLog \${APACHE_LOG_DIR}/masterstudio_access.log combined
</VirtualHost>
VHEOF
        a2dissite 000-default.conf > /dev/null 2>&1 || true
        a2ensite masterstudio.conf > /dev/null 2>&1
        systemctl reload apache2
    fi

    success "Apache konfigüre edildi"
}

# --- Backup Cron ---
setup_cron() {
    info "Otomatik yedekleme ayarlanıyor..."

    CRON_CMD="0 3 * * * cd $INSTALL_DIR && /bin/bash scripts/backup.sh >> /var/log/masterstudio_backup.log 2>&1"
    (crontab -l 2>/dev/null | grep -v "masterstudio"; echo "$CRON_CMD") | crontab -

    success "Günlük yedekleme (03:00) ayarlandı"
}

# --- Ana Akış ---
print_header
detect_os
check_root

echo -e "${BOLD}Kurulum Modu Seçin:${NC}"
echo "  1) Tam Kurulum (Apache + PHP + MySQL + Uygulama)"
echo "  2) Sadece Uygulama (PHP ve MySQL zaten kurulu)"
echo "  3) Docker ile Kurulum"
echo ""
read -p "Seçiminiz [1]: " INSTALL_MODE
INSTALL_MODE=${INSTALL_MODE:-1}

case $INSTALL_MODE in
    1)
        case $OS in
            ubuntu|debian) install_packages_debian ;;
            centos|rhel|almalinux|rocky) install_packages_rhel ;;
            *) error "Desteklenmeyen işletim sistemi: $OS"; exit 1 ;;
        esac
        setup_files
        setup_database
        setup_admin
        setup_apache
        setup_cron
        ;;
    2)
        setup_files
        setup_database
        setup_admin
        if [ "$IS_CPANEL" = false ]; then
            setup_apache
        fi
        setup_cron
        ;;
    3)
        info "Docker kurulumu başlatılıyor..."
        if ! command -v docker &> /dev/null; then
            error "Docker yüklü değil. Önce Docker kurun."
            exit 1
        fi
        if [ ! -f ".env" ]; then
            cp .env.example .env 2>/dev/null || true
            info ".env dosyasını düzenleyin ve docker-compose up -d çalıştırın."
        fi
        docker compose up -d --build
        success "Docker konteynerları başlatıldı"
        echo ""
        info "Uygulama: http://localhost"
        info "phpMyAdmin: http://localhost:8080"
        exit 0
        ;;
    *)
        error "Geçersiz seçim."
        exit 1
        ;;
esac

# --- Özet ---
echo ""
echo -e "${CYAN}╔══════════════════════════════════════════════╗${NC}"
echo -e "${CYAN}║         ${BOLD}KURULUM TAMAMLANDI!${NC}${CYAN}                  ║${NC}"
echo -e "${CYAN}╚══════════════════════════════════════════════╝${NC}"
echo ""
echo -e "  ${GREEN}Ana Sayfa:${NC}    http://sunucu-ip/"
echo -e "  ${GREEN}Admin Panel:${NC}  http://sunucu-ip/admin/auth/login.php"
echo -e "  ${GREEN}Admin Kullanıcı:${NC} $ADMIN_USER"
echo -e "  ${GREEN}Kurulum Dizini:${NC} $INSTALL_DIR"
echo ""
echo -e "  ${YELLOW}ÖNEMLİ:${NC}"
echo -e "  - install.php ve install.sh dosyalarını sunucudan silin"
echo -e "  - .env dosyasındaki şifreleri güvenli tutun"
echo -e "  - SSL sertifikası kurun (Let's Encrypt önerilir)"
echo ""
