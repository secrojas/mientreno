#!/bin/bash
set -e

# ================================
# CONFIG
# ================================
export HOME="/home/srojasw1"
export COMPOSER_HOME="$HOME/.composer"

APP_DIR="/home/srojasw1/public_html/mientreno/app"
PUBLIC_DIR="/home/srojasw1/public_html/mientreno/public"
PHP_BIN="/opt/cpanel/ea-php84/root/usr/bin/php"
COMPOSER_BIN="/home/srojasw1/bin/composer"

echo "======================================"
echo "🚀 Deploy MiEntreno iniciado"
echo "======================================"

# ================================
# 1. UPDATE CODE (CLAVE)
# ================================
echo ">> Actualizando código desde Git"

cd "$APP_DIR"

git fetch origin
git reset --hard origin/main
git clean -fd

echo "✅ Código actualizado"

# ================================
# 2. DEPENDENCIAS PHP
# ================================
echo ">> Instalando dependencias PHP"

$COMPOSER_BIN install --no-dev --optimize-autoloader --no-interaction

echo "✅ Composer OK"

# ================================
# 3. VALIDAR BUILD (VITE)
# ================================
echo ">> Verificando assets compilados"

if [ ! -d "$APP_DIR/public/build" ] || [ ! -f "$APP_DIR/public/build/manifest.json" ]; then
  echo "❌ ERROR: Faltan assets compilados"
  echo "👉 Ejecutá 'npm run build' en local y hacé commit"
  exit 1
fi

echo "✅ Assets detectados"

# ================================
# 4. SINCRONIZAR PUBLIC (DOCROOT)
# ================================
echo ">> Sincronizando carpeta public"

mkdir -p "$PUBLIC_DIR"

rsync -av --delete \
  --exclude="storage" \
  --exclude="index.php" \
  "$APP_DIR/public/" "$PUBLIC_DIR/"

echo "✅ Public sincronizado"

# ================================
# 5. LIMPIAR Y RECACHEAR LARAVEL
# ================================
echo ">> Limpiando caches"

$PHP_BIN artisan optimize:clear

echo ">> Cacheando configuración"

$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache

echo "✅ Laravel optimizado"

# ================================
# 6. PERMISOS
# ================================
echo ">> Ajustando permisos"

chmod -R 775 "$APP_DIR/storage"
chmod -R 775 "$APP_DIR/bootstrap/cache"

echo "✅ Permisos OK"

# ================================
# DONE
# ================================
echo ""
echo "======================================"
echo "✅ Deploy completado correctamente"
echo "======================================"
echo ""

echo "Si hay migraciones pendientes ejecutar:"
echo "cd $APP_DIR && $PHP_BIN artisan migrate --force"