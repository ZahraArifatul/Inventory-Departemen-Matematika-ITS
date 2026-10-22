#!/usr/bin/env bash
# Pemakaian: bash setup.sh nama-folder-proyek
set -e
NAMA="${1:-servis-aset}"
SRC="$(cd "$(dirname "$0")" && pwd)"

composer create-project laravel/laravel "$NAMA"
cp -R "$SRC/app" "$SRC/database" "$SRC/routes" "$SRC/resources" "$NAMA/"
cd "$NAMA"

# Zona waktu Indonesia
echo "APP_TIMEZONE=Asia/Jakarta" >> .env

php artisan migrate --seed --force
echo ""
echo "Selesai. Jalankan:  cd $NAMA && php artisan serve"
