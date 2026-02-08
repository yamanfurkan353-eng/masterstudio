#!/bin/bash

# MasterStudio Hotel - Veritabanı Backup Scripti
# Bu script, MySQL veritabanınızı yedekler

# Yapılandırma
DB_HOST="localhost"
DB_USER="hotel_user"
DB_PASSWORD="hotel_password"
DB_NAME="masterstudio_hotel"

# Yedek dizini
BACKUP_DIR="./backups"
BACKUP_DATE=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="$BACKUP_DIR/masterstudio_hotel_$BACKUP_DATE.sql"

# Renkli çıktı
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
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

# Yedek dizinin olup olmadığını kontrol et
if [ ! -d "$BACKUP_DIR" ]; then
    echo_info "Yedek dizini oluşturuluyor: $BACKUP_DIR"
    mkdir -p "$BACKUP_DIR"
fi

# Yedekleme işlemi
echo_info "Yedekleme başlanıyor..."
echo_info "Veritabanı: $DB_NAME"
echo_info "Dosya: $BACKUP_FILE"
echo ""

# mysqldump kullan
mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" > "$BACKUP_FILE" 2>/dev/null

# kontrol et
if [ $? -eq 0 ]; then
    FILE_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    echo_success "Yedekleme başarılı!"
    echo_success "Dosya boyutu: $FILE_SIZE"
    
    # 30 günden eski dosyaları sil
    echo_info "Eski yedekler temizleniyor (30 günden eski)..."
    find "$BACKUP_DIR" -name "masterstudio_hotel_*.sql" -mtime +30 -delete
    echo_success "Temizleme tamamlandı"
else
    echo_error "Yedekleme başarısız oldu!"
    echo_error "Veritabanı bağlantısını kontrol edin"
    exit 1
fi

# Yazma süresi
READ_TIME=$(stat -f%Sm -t%Y-%m-%d "$BACKUP_FILE" 2>/dev/null || stat -c%y "$BACKUP_FILE" 2>/dev/null | cut -d' ' -f1-2)
echo ""
echo_success "Yedekleme tamamlandı: $READ_TIME"
echo ""

# Hızlı istatistikler
echo "Yedek Dosyaları:"
ls -lh "$BACKUP_DIR" | grep "masterstudio_hotel_" | tail -5
