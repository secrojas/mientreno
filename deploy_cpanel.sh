#!/bin/bash
set -e

# Script de deploy para cPanel
# Ubicación en servidor: /home/srojasw1/deploy_mientreno.sh
# Uso: ./deploy_mientreno.sh

# Variables de entorno necesarias
export HOME="/home/srojasw1"
export COMPOSER_HOME="$HOME/.composer"

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
/home/srojasw1/bin/composer install --no-dev --optimize-autoloader --no-interaction

echo ">> Verificar assets compilados"
# IMPORTANTE: npm no está disponible en este hosting
# Los assets DEBEN compilarse localmente ANTES de hacer push:
#   1. Ejecutar en local: npm run build
#   2. Verificar que public/build/ contiene los archivos
#   3. Hacer commit (están versionados gracias a .gitignore líneas 36-38)
#   4. Push y deploy
# El directorio public/build se copia automáticamente en el paso de deploy PUBLIC

if [ ! -d "$REPO/public/build" ] || [ ! -f "$REPO/public/build/manifest.json" ]; then
  echo "❌ ERROR: Assets no compilados. Ejecutá 'npm run build' localmente antes de hacer push."
  exit 1
fi

echo "✅ Assets encontrados: $(ls -1 $REPO/public/build/assets/ | wc -l) archivos"

echo ">> Copiar assets compilados a APP/public/build"
# Laravel busca los assets en APP_DEST/public/build/, no solo en PUBLIC_DEST
mkdir -p "$APP_DEST/public"
rm -rf "$APP_DEST/public/build"
cp -a "$REPO/public/build" "$APP_DEST/public/"
echo "✅ Assets copiados a $APP_DEST/public/build/"

echo ">> Optimizaciones Laravel"
/opt/cpanel/ea-php84/root/usr/bin/php artisan config:cache
/opt/cpanel/ea-php84/root/usr/bin/php artisan route:cache
/opt/cpanel/ea-php84/root/usr/bin/php artisan view:cache

echo ">> Permisos"
chmod -R 775 storage bootstrap/cache

echo "✅ Deploy completado"
echo ""
echo "Siguiente paso: Si hay migraciones nuevas, ejecutar:"
echo "  cd $APP_DEST && /opt/cpanel/ea-php84/root/usr/bin/php artisan migrate --force"
