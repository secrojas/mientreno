#!/bin/bash
set -e

# Script de deploy para cPanel
# Ubicación en servidor: /home/srojasw1/deploy_mientreno.sh
# Uso: ./deploy_mientreno.sh

REPO="/home/srojasw1/repositories/mientreno"
APP_DEST="/home/srojasw1/public_html/mientreno/app"
PUBLIC_DEST="/home/srojasw1/public_html/mientreno/public"

echo ">> Pull del repo"
cd "$REPO"
git pull origin main

echo ">> Deploy APP (Laravel core)"
APP_ITEMS=(artisan app bootstrap config database routes resources composer.json composer.lock package.json vite.config.*)

for item in "${APP_ITEMS[@]}"; do
  matches=($REPO/$item)
  if [ -e "${matches[0]}" ]; then
    # borramos solo si existe en destino
    [ -e "$APP_DEST/$item" ] && rm -rf "$APP_DEST/$item"
    cp -a ${matches[@]} "$APP_DEST/"
  fi
done

echo ">> Deploy PUBLIC (docroot, sin tocar storage ni index.php)"
mkdir -p "$PUBLIC_DEST"

if [ -d "$REPO/public" ]; then
  for item in "$REPO/public/"*; do
    name=$(basename "$item")

    # JAMÁS tocar storage (symlink / uploads)
    if [ "$name" = "storage" ]; then
      continue
    fi

    # NO tocar index.php (tiene rutas custom para producción)
    if [ "$name" = "index.php" ]; then
      continue
    fi

    # borramos solo lo versionado
    [ -e "$PUBLIC_DEST/$name" ] && rm -rf "$PUBLIC_DEST/$name"
    cp -a "$item" "$PUBLIC_DEST/"
  done
fi

echo ">> Composer install"
cd "$APP_DEST"
composer install --no-dev --optimize-autoloader --no-interaction

echo ">> Build assets (npm)"
npm ci --production=false
npm run build

echo ">> Optimizaciones Laravel"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ">> Permisos"
chmod -R 775 storage bootstrap/cache

echo "✅ Deploy completado"
echo ""
echo "Siguiente paso: Si hay migraciones nuevas, ejecutar:"
echo "  cd $APP_DEST && php artisan migrate --force"
