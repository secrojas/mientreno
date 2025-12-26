# Deploy en cPanel

Documentación completa para hacer deploy de la aplicación Laravel en hosting compartido con cPanel.

## Acceso a la Terminal

### Opción 1: Terminal de cPanel (Recomendado)

La forma más sencilla de acceder a la terminal es a través del panel de control de cPanel:

1. Acceder a cPanel del hosting
2. Buscar "Terminal" en las herramientas
3. Click en "Terminal" para abrir la consola web
4. Ya estás conectado y puedes ejecutar comandos

**Ventajas:**
- ✅ No requiere configuración adicional
- ✅ Acceso inmediato desde el navegador
- ✅ No necesitas gestionar claves SSH

**Desventajas:**
- ❌ La sesión puede cerrarse por inactividad
- ❌ No puedes copiar archivos fácilmente con SCP/SFTP

### Opción 2: SSH desde tu PC (Opcional)

Si prefieres conectarte desde tu PC local usando SSH:

**Requisitos:**
- Tener acceso SSH habilitado en tu hosting
- Generar y configurar claves SSH
- Cliente SSH instalado (PuTTY en Windows, ssh nativo en Linux/Mac)

**Pasos para configurar:**

1. **Generar par de claves en cPanel:**
   - Ir a cPanel → "Acceso SSH" o "SSH Access"
   - Click en "Gestionar claves SSH" o "Manage SSH Keys"
   - Generar nueva clave o importar existente
   - Autorizar la clave pública

2. **Descargar clave privada:**
   - Descargar el archivo de clave privada
   - Guardar en `C:\Users\TU_USUARIO\.ssh\` (Windows)
   - O en `~/.ssh/` (Linux/Mac)

3. **Configurar permisos (importante):**
   ```bash
   # En Linux/Mac
   chmod 600 ~/.ssh/deploy-mientreno

   # En Windows (PowerShell como Administrador)
   icacls C:\Users\sroja\.ssh\deploy-mientreno /inheritance:r
   icacls C:\Users\sroja\.ssh\deploy-mientreno /grant:r "%USERNAME%:R"
   ```

4. **Conectar:**
   ```bash
   ssh -i C:\Users\sroja\.ssh\deploy-mientreno srojasw1@tu-servidor.com

   # O agregar al archivo ~/.ssh/config:
   Host mientreno
       HostName tu-servidor.com
       User srojasw1
       IdentityFile C:\Users\sroja\.ssh\deploy-mientreno

   # Luego conectar con:
   ssh mientreno
   ```

**Troubleshooting SSH:**

Si no puedes conectarte, verifica:

```bash
# 1. Ver errores detallados de conexión
ssh -vvv -i C:\Users\sroja\.ssh\deploy-mientreno srojasw1@servidor

# Errores comunes:
# - "Permission denied (publickey)": La clave pública no está autorizada en el servidor
# - "Bad permissions": Los permisos del archivo de clave son muy abiertos
# - "Connection refused": El puerto SSH está bloqueado o el servidor no acepta SSH
```

Desde la terminal de cPanel, verifica:
```bash
# Ver claves autorizadas
cat ~/.ssh/authorized_keys

# Ver permisos (deben ser 700 para .ssh y 600 para authorized_keys)
ls -la ~/.ssh/
```

Si la clave pública no está en `authorized_keys`, agrégala:
```bash
# Copiar contenido de deploy-mientreno.pub y agregarlo a:
nano ~/.ssh/authorized_keys
# Pegar la clave pública en una nueva línea
```

**Para esta documentación, asumiremos que usas la Terminal de cPanel**, pero todos los comandos funcionan igual por SSH.

## Estructura de Directorios en Producción

```
/home/srojasw1/
├── repositories/
│   └── mientreno/                    # Repositorio Git (clone)
│       ├── app/
│       ├── public/
│       ├── composer.json
│       └── ...
│
└── public_html/
    └── mientreno/
        ├── app/                      # Laravel application (destino del deploy)
        │   ├── artisan
        │   ├── app/
        │   ├── bootstrap/
        │   ├── config/
        │   ├── database/
        │   ├── routes/
        │   ├── resources/
        │   ├── storage/              # Persistente (NO se sobrescribe)
        │   ├── vendor/
        │   ├── .env                  # Persistente (NO se sobrescribe)
        │   ├── composer.json
        │   └── ...
        │
        └── public/                   # Document root (accesible por web)
            ├── index.php             # CUSTOM - NO sobrescribir
            ├── .htaccess
            ├── css/
            ├── js/
            ├── build/
            └── storage/              # Symlink a ../app/storage/app/public
```

## Script de Deploy

Ubicación: `/home/srojasw1/deploy_mientreno.sh`

```bash
#!/bin/bash
set -e

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
```

## Archivos que NO se Deben Sobrescribir

### 1. `public/index.php`

**Por qué:** El `index.php` en producción tiene rutas modificadas para la estructura de cPanel.

**Repo (estándar):**
```php
require __DIR__.'/../bootstrap/app.php';
```

**Producción (custom):**
```php
require __DIR__.'/../app/bootstrap/app.php';
```

### 2. `public/storage`

**Por qué:** Es un symlink al storage real. Si se sobrescribe, se pierden los uploads.

```bash
# El symlink apunta a:
public/storage -> ../app/storage/app/public
```

### 3. `app/.env`

**Por qué:** Contiene configuración específica de producción (credenciales de BD, API keys, etc.)

**Importante:** El `.env` NUNCA debe estar en el repositorio Git.

### 4. `app/storage/*`

**Por qué:** Contiene:
- Uploads de usuarios (avatares, archivos)
- Logs de la aplicación
- Caché compilado
- Sesiones

**Estos datos son persistentes y no deben borrarse en deploy.**

## Configuración Inicial (Solo Primera Vez)

### 1. Clonar Repositorio

```bash
cd /home/srojasw1/repositories
git clone git@github.com:secrojas/mientreno.git
cd mientreno
```

### 2. Crear Estructura de Directorios

```bash
mkdir -p /home/srojasw1/public_html/mientreno/app
mkdir -p /home/srojasw1/public_html/mientreno/public
```

### 3. Copiar y Configurar index.php

```bash
# Copiar index.php
cp /home/srojasw1/repositories/mientreno/public/index.php \
   /home/srojasw1/public_html/mientreno/public/index.php

# Editar y cambiar las rutas
nano /home/srojasw1/public_html/mientreno/public/index.php
```

Modificar:
```php
// Cambiar esto:
require __DIR__.'/../bootstrap/app.php';

// Por esto:
require __DIR__.'/../app/bootstrap/app.php';
```

### 4. Crear .env en Producción

```bash
cd /home/srojasw1/public_html/mientreno/app
nano .env
```

Configuración mínima requerida:
```env
APP_NAME="MiEntreno"
APP_ENV=production
APP_KEY=base64:TU_APP_KEY_GENERADA
APP_DEBUG=false
APP_URL=https://tudominio.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=srojasw1_mientreno
DB_USERNAME=srojasw1_user
DB_PASSWORD=tu_password_seguro

CACHE_DRIVER=database
SESSION_DRIVER=database
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
# ... resto de configuración
```

### 5. Generar APP_KEY

```bash
cd /home/srojasw1/public_html/mientreno/app
php artisan key:generate
```

### 6. Crear Storage Symlink

```bash
cd /home/srojasw1/public_html/mientreno/app
php artisan storage:link
```

### 7. Ejecutar Migraciones

```bash
cd /home/srojasw1/public_html/mientreno/app
php artisan migrate --force
```

### 8. Configurar Permisos

```bash
cd /home/srojasw1/public_html/mientreno/app
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 9. Crear Script de Deploy

```bash
nano /home/srojasw1/deploy_mientreno.sh
# Pegar el contenido del script (ver arriba)

# Darle permisos de ejecución
chmod +x /home/srojasw1/deploy_mientreno.sh
```

## Proceso de Deploy (Cada Actualización)

### 1. Desde Local - Push a GitHub

```bash
git add .
git commit -m "feat: nueva funcionalidad"
git push origin main
```

### 2. En Producción - Ejecutar Script de Deploy

**Desde la Terminal de cPanel:**

1. Acceder a cPanel → Terminal
2. Ejecutar el script:

```bash
cd /home/srojasw1
./deploy_mientreno.sh
```

El script automáticamente:
1. ✅ Hace pull del repositorio
2. ✅ Copia archivos de la app (sin tocar storage ni .env)
3. ✅ Copia archivos públicos (sin tocar storage ni index.php)
4. ✅ Instala dependencias de Composer
5. ✅ Compila assets con Vite/npm
6. ✅ Cachea configuración, rutas y vistas
7. ✅ Ajusta permisos

### 3. Ejecutar Migraciones (si hay nuevas)

```bash
cd /home/srojasw1/public_html/mientreno/app
php artisan migrate --force
```

### 4. Verificar

Acceder a la aplicación en el navegador y verificar que todo funcione correctamente.

## Comandos Útiles Post-Deploy

### Limpiar Cachés

```bash
cd /home/srojasw1/public_html/mientreno/app

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Re-cachear (Para mejor performance)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Ver Logs

```bash
# Ver errores recientes
tail -50 /home/srojasw1/public_html/mientreno/app/storage/logs/laravel.log

# Ver errores de hoy
grep "$(date +%Y-%m-%d)" /home/srojasw1/public_html/mientreno/app/storage/logs/laravel.log
```

### Verificar Configuración

```bash
cd /home/srojasw1/public_html/mientreno/app

# Ver configuración cargada
php artisan config:show

# Ver rutas
php artisan route:list

# Verificar conexión a BD
php artisan tinker
>>> DB::connection()->getPdo();
```

## Troubleshooting

### Error 500 después de Deploy

**Causa común:** Se sobrescribió el `index.php` y las rutas están mal.

**Solución:**
```bash
nano /home/srojasw1/public_html/mientreno/public/index.php
# Verificar que diga: require __DIR__.'/../app/bootstrap/app.php';
```

### Archivos CSS/JS no se actualizan

**Causa:** Caché del navegador o assets no compilados.

**Solución:**
```bash
cd /home/srojasw1/public_html/mientreno/app
npm run build
php artisan view:clear
```

Luego Ctrl+Shift+R en el navegador (hard refresh).

### Imágenes/Avatares desaparecieron

**Causa:** Se eliminó el symlink de storage.

**Solución:**
```bash
cd /home/srojasw1/public_html/mientreno/app
php artisan storage:link
```

### Error de permisos (Permission Denied)

**Solución:**
```bash
cd /home/srojasw1/public_html/mientreno/app
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Migraciones fallan

**Verificar que:**
1. Las credenciales de BD en `.env` sean correctas
2. El usuario de BD tenga permisos
3. La base de datos exista

```bash
cd /home/srojasw1/public_html/mientreno/app
php artisan migrate:status
```

### Composer no instala dependencias

**Verificar versión de PHP:**
```bash
php -v  # Debe ser PHP 8.x
```

**Si usa PHP 7.x por defecto:**
```bash
# Usar PHP 8.4 explícitamente (ajustar según versión disponible)
/opt/cpanel/ea-php84/root/usr/bin/php composer install
```

## Seguridad

### Archivos Sensibles

Asegurarse que estos archivos NO estén en el repositorio:
- `.env`
- `storage/` (contenido)
- `vendor/`
- `node_modules/`
- Archivos de backup con extensión `.sql`

### Verificar .gitignore

```bash
cat .gitignore
```

Debe incluir:
```
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
```

### Backups

Crear backups regulares de:
1. Base de datos
2. Archivos de `storage/app/public` (avatares, uploads)
3. Archivo `.env`

```bash
# Backup de BD
mysqldump -u usuario -p srojasw1_mientreno > backup-$(date +%Y%m%d).sql

# Backup de storage
tar -czf storage-backup-$(date +%Y%m%d).tar.gz \
  /home/srojasw1/public_html/mientreno/app/storage/app/public
```

## Monitoreo

### Ver uso de recursos

```bash
# Espacio en disco
du -sh /home/srojasw1/public_html/mientreno/app

# Logs más grandes
du -sh /home/srojasw1/public_html/mientreno/app/storage/logs/*
```

### Limpiar logs antiguos

```bash
cd /home/srojasw1/public_html/mientreno/app/storage/logs
# Eliminar logs de más de 30 días
find . -name "*.log" -mtime +30 -delete
```

## Checklist de Deploy

- [ ] Código commiteado y pusheado a GitHub
- [ ] Acceder a la Terminal de cPanel
- [ ] Ejecutar `./deploy_mientreno.sh`
- [ ] Ejecutar migraciones si hay nuevas: `php artisan migrate --force`
- [ ] Verificar que la app funcione en el navegador
- [ ] Revisar logs por errores: `tail -50 storage/logs/laravel.log`
- [ ] Limpiar caché si es necesario
- [ ] Verificar que assets (CSS/JS) estén actualizados

## Contacto y Soporte

Para problemas de hosting de cPanel, contactar al proveedor del hosting.

Para problemas de la aplicación Laravel, revisar:
- Logs de Laravel: `storage/logs/laravel.log`
- Documentación del proyecto: `docs/`
- GitHub Issues
