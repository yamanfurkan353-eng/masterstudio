#!/bin/bash

# MasterStudio Hotel - Veritabanı Restore Scripti
# Kullanım: ./restore.sh backups/masterstudio_hotel_20260215_143022.sql

# Yapılandırma
DB_HOST="localhost"
DB_USER="hotel_user"
DB_PASSWORD="hotel_password"
DB_NAME="masterstudio_hotel"

# Renkli çıktı
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonksiyonlar
echo_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

echo_error() {
    echo -e "${RED}✗ $1${NC}"
}

echo_info() {
    echo -e "${YELLOW}ℹ $1${NC}"
}

echo_title() {
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
}

# Argument kontrol
if [ -z "$1" ]; then
    echo_title "MasterStudio Hotel - Veritabanı Restore"
    echo ""
    echo "Kullanım: $0 <backup-file>"
    echo ""
    echo "Örnekler:"
    echo "  $0 backups/masterstudio_hotel_20260215_143022.sql"
    echo "  $0 backups/masterstudio_hotel_latest.sql"
    echo ""
    echo "Mevcut Yedekler:"
    ls -lh backups/masterstudio_hotel_*.sql 2>/dev/null | tail -10
    exit 1
fi

BACKUP_FILE="$1"

# Dosya var mı kontrol et
if [ ! -f "$BACKUP_FILE" ]; then
    echo_error "Dosya bulunamadı: $BACKUP_FILE"
    exit 1
fi

# Onay isteme
echo_title "Restore İşlemi - Onay Gerekli"
echo ""
echo_info "Bu işlem mevcut veritabanını SİLECEK ve yedeğin içeriğini yükleyecektir."
echo ""
echo "  Veritabanı: $DB_NAME"
echo "  Yedek Dosyası: $BACKUP_FILE"
echo "  Dosya Boyutu: $(du -h "$BACKUP_FILE" | cut -f1)"
echo ""
echo -n "Devam etmek istiyor musunuz? (evet/hayır): "
read -r CONFIRM

if [ "$CONFIRM" != "evet" ] && [ "$CONFIRM" != "yes" ]; then
    echo_info "İşlem iptal edildi"
    exit 0
fi

echo ""
echo_info "Restore işlemi başlanıyor..."
echo ""

# Veritabanını sil ve yenisini oluştur
echo_info "Mevcut veritabanı siliniyor..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -e "DROP DATABASE IF EXISTS $DB_NAME;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo_success "Veritabanı silindi"
else
    echo_error "Veritabanı silinirken hata oluştu"
    exit 1
fi

# Yeni veritabanı oluştur
echo_info "Yeni veritabanı oluşturuluyor..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -e "CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo_success "Yeni veritabanı oluşturuldu"
else
    echo_error "Veritabanı oluşturulurken hata oluştu"
    exit 1
fi

# Yedek dosyasını geri yükle
echo_info "Yedek dosyası yükleniyor..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < "$BACKUP_FILE" 2>/dev/null

if [ $? -eq 0 ]; then
    echo_success "Yedek başarıyla yüklendi"
else
    echo_error "Yedek yüklenirken hata oluştu"
    exit 1
fi

# İstatistikleri göster
echo ""
echo_title "Restore Tamamlandı"
echo ""
echo_success "Veritabanı geri yüklendi"
echo ""

# Tablo sayısı
TABLE_COUNT=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" -e "SHOW TABLES;" 2>/dev/null | wc -l)
echo "  Tablolar: $TABLE_COUNT"

# Kayıt sayıları
echo ""
echo "Veri İstatistikleri:"
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" <<EOF 2>/dev/null
SELECT 
    (SELECT COUNT(*) FROM users) as 'Kullanıcılar',
    (SELECT COUNT(*) FROM rooms) as 'Odalar',
    (SELECT COUNT(*) FROM reservations) as 'Rezervasyonlar',
    (SELECT COUNT(*) FROM pages) as 'Sayfalar';
EOF

echo ""
echo_success "Admin Sayfası: http://$(hostname -I | awk '{print $1}')/admin"
echo ""
