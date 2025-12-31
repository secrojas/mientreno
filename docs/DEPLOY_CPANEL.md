# Deploy en cPanel - MiEntreno

Documentación completa para hacer deploy de la aplicación Laravel en hosting compartido con cPanel.

## Métodos de Deploy

### Deploy Automático (Recomendado)

**El método principal de deploy es automático mediante GitHub Actions.**

Cada vez que haces `git push origin main`:
1. GitHub Actions detecta el push
2. Ejecuta el workflow de deploy
3. Llama al webhook en producción
4. El servidor ejecuta el script de deploy automáticamente
5. Los cambios se reflejan en https://mientreno.srojasweb.dev

**No necesitas hacer deploy manual** a menos que:
- Estés configurando el proyecto por primera vez
- El auto-deploy falle y necesites intervenir manualmente
- Necesites ejecutar comandos específicos (migraciones, seeders, etc.)

Ver [AUTO_DEPLOY.md](AUTO_DEPLOY.md) para detalles de configuración del auto-deploy.

### Deploy Manual

Para casos especiales donde necesitas hacer deploy manual, tienes dos opciones:

#### Opción 1: Terminal de cPanel (Web)

**Ventajas:**
- ✅ No requiere configuración adicional
- ✅ Acceso inmediato desde el navegador
- ✅ No necesitas gestionar claves SSH localmente

**Desventajas:**
- ❌ La sesión puede cerrarse por inactividad
- ❌ No puedes copiar archivos fácilmente con SCP/SFTP
- ❌ Más lento que SSH directo

**Cómo acceder:**
1. Acceder a cPanel del hosting
2. Buscar "Terminal" en las herramientas
3. Click en "Terminal" para abrir la consola web
4. Ejecutar el script de deploy:

```bash
bash /home/srojasw1/deploy_mientreno.sh
```

#### Opción 2: SSH desde tu PC (Local)

**Ventajas:**
- ✅ Más rápido y eficiente
- ✅ Puedes usar SCP/SFTP para copiar archivos
- ✅ Mejor experiencia de terminal (autocompletado, historial, etc.)
- ✅ Puedes usar herramientas como tmux/screen

**Desventajas:**
- ❌ Requiere configuración inicial de claves SSH

**Configuración SSH (Solo primera vez):**

1. **Generar clave SSH sin passphrase (ya configurado):**

   La clave ya está generada y configurada:
   - Ubicación: `~/.ssh/deploy-mientreno-new`
   - Tipo: RSA 4096 bits
   - Sin passphrase (necesario para deploy automatizado)

2. **Configuración SSH (ya está en `~/.ssh/config`):**

   ```
   Host mientreno-prod
     HostName srojasweb.dev
     User srojasw1
     Port 22278
     IdentityFile ~/.ssh/deploy-mientreno-new
     IdentitiesOnly yes
   ```

3. **Conectar desde tu PC:**

   ```bash
   # Conexión simple con alias
   ssh mientreno-prod

   # O conexión completa
   ssh -p 22278 -i ~/.ssh/deploy-mientreno-new srojasw1@srojasweb.dev
   ```

4. **Ejecutar deploy manual por SSH:**

   ```bash
   # Desde tu PC local, ejecutar deploy remoto
   ssh mientreno-prod "bash /home/srojasw1/deploy_mientreno.sh"

   # O conectarte y ejecutar
   ssh mientreno-prod
   bash /home/srojasw1/deploy_mientreno.sh
   ```

**Nota importante sobre el puerto SSH:**
- El hosting usa el puerto **22278** (no el estándar 22)
- Esto es por seguridad contra ataques de fuerza bruta
- La autenticación es **solo con claves SSH** (no contraseñas)

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
├── public_html/
│   └── mientreno/
│       ├── app/                      # Laravel application (destino del deploy)
│       │   ├── artisan
│       │   ├── app/
│       │   ├── bootstrap/
│       │   ├── config/
│       │   ├── database/
│       │   ├── routes/
│       │   ├── resources/
│       │   ├── storage/              # Persistente (NO se sobrescribe)
│       │   ├── vendor/
│       │   ├── .env                  # Persistente (NO se sobrescribe)
│       │   ├── composer.json
│       │   └── ...
│       │
│       └── public/                   # Document root (accesible por web)
│           ├── index.php             # CUSTOM - NO sobrescribir
│           ├── .htaccess
│           ├── css/
│           ├── js/
│           ├── build/
│           └── storage/              # Symlink a ../app/storage/app/public
│
└── deploy_mientreno.sh               # Script de deploy
```

## Script de Deploy

**Ubicación:** `/home/srojasw1/deploy_mientreno.sh`

El script actualizado (sin npm - los assets se compilan localmente antes del push):

```bash
#!/bin/bash
set -e

# Variables de entorno necesarias para composer
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
```

**Notas importantes:**
- **No usa npm en producción**: Los assets (CSS/JS) se compilan localmente con `npm run build` antes de hacer push
- **Validación de assets**: El script verifica que `public/build/manifest.json` exista, y falla si no encuentra los assets compilados
- **Doble copia de assets**: Los assets se copian a **dos destinos**:
  1. `PUBLIC_DEST/build/` - Docroot público accesible por web
  2. `APP_DEST/public/build/` - Laravel app (donde busca Vite los manifests)
- **Rutas absolutas**: Usa rutas absolutas para `composer` y `php` porque el webhook no tiene el PATH configurado
- **Variables de entorno**: Exporta `HOME` y `COMPOSER_HOME` para que composer funcione vía webhook

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
APP_URL=https://mientreno.srojasweb.dev

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=srojasw1_mientreno
DB_USERNAME=srojasw1_user
DB_PASSWORD=tu_password_seguro

CACHE_DRIVER=database
SESSION_DRIVER=database
QUEUE_CONNECTION=sync

# Deploy webhook token (para auto-deploy)
DEPLOY_TOKEN=tu_token_generado

MAIL_MAILER=smtp
# ... resto de configuración
```

### 5. Generar APP_KEY

```bash
cd /home/srojasw1/public_html/mientreno/app
/opt/cpanel/ea-php84/root/usr/bin/php artisan key:generate
```

### 6. Crear Storage Symlink

```bash
cd /home/srojasw1/public_html/mientreno/app
/opt/cpanel/ea-php84/root/usr/bin/php artisan storage:link
```

### 7. Ejecutar Migraciones

```bash
cd /home/srojasw1/public_html/mientreno/app
/opt/cpanel/ea-php84/root/usr/bin/php artisan migrate --force
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

### 10. Configurar Auto-Deploy

Ver [AUTO_DEPLOY.md](AUTO_DEPLOY.md) para configurar GitHub Actions y el webhook.

## Flujo de Trabajo Completo

### Desarrollo Local → Producción

1. **Desarrollar localmente:**
   ```bash
   # Hacer cambios en el código
   npm run dev  # Para desarrollo
   ```

2. **Compilar assets antes de commit:**
   ```bash
   npm run build  # Compila para producción
   ```

3. **Commit y push:**
   ```bash
   git add .
   git commit -m "feat: nueva funcionalidad"
   git push origin main
   ```

4. **Auto-deploy se ejecuta automáticamente:**
   - GitHub Actions detecta el push
   - Llama al webhook de producción
   - El script de deploy se ejecuta
   - Los cambios están en producción en ~1-2 minutos

5. **Verificar:**
   - Revisar que el workflow de GitHub Actions pasó (salió verde ✅)
   - Verificar en https://mientreno.srojasweb.dev que los cambios estén aplicados

### Si hay Migraciones Nuevas

Después del auto-deploy, ejecutar manualmente:

```bash
# Por SSH
ssh mientreno-prod
cd /home/srojasw1/public_html/mientreno/app
/opt/cpanel/ea-php84/root/usr/bin/php artisan migrate --force

# O en una sola línea desde tu PC
ssh mientreno-prod "cd /home/srojasw1/public_html/mientreno/app && /opt/cpanel/ea-php84/root/usr/bin/php artisan migrate --force"
```

## Comandos Útiles

### Deploy Manual (Casos de Emergencia)

```bash
# Por Terminal de cPanel
bash /home/srojasw1/deploy_mientreno.sh

# Por SSH desde tu PC
ssh mientreno-prod "bash /home/srojasw1/deploy_mientreno.sh"
```

### Limpiar Cachés

```bash
cd /home/srojasw1/public_html/mientreno/app

/opt/cpanel/ea-php84/root/usr/bin/php artisan cache:clear
/opt/cpanel/ea-php84/root/usr/bin/php artisan config:clear
/opt/cpanel/ea-php84/root/usr/bin/php artisan route:clear
/opt/cpanel/ea-php84/root/usr/bin/php artisan view:clear
```

### Re-cachear (Para mejor performance)

```bash
cd /home/srojasw1/public_html/mientreno/app

/opt/cpanel/ea-php84/root/usr/bin/php artisan config:cache
/opt/cpanel/ea-php84/root/usr/bin/php artisan route:cache
/opt/cpanel/ea-php84/root/usr/bin/php artisan view:cache
```

### Ver Logs

```bash
# Ver errores recientes
tail -50 /home/srojasw1/public_html/mientreno/app/storage/logs/laravel.log

# Ver errores de hoy
grep "$(date +%Y-%m-%d)" /home/srojasw1/public_html/mientreno/app/storage/logs/laravel.log

# Seguir logs en tiempo real
tail -f /home/srojasw1/public_html/mientreno/app/storage/logs/laravel.log
```

### Verificar Configuración

```bash
cd /home/srojasw1/public_html/mientreno/app

# Ver rutas
/opt/cpanel/ea-php84/root/usr/bin/php artisan route:list

# Ver configuración
/opt/cpanel/ea-php84/root/usr/bin/php artisan config:show

# Verificar conexión a BD
/opt/cpanel/ea-php84/root/usr/bin/php artisan tinker
>>> DB::connection()->getPdo();
>>> exit
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

**Causa:** No compilaste los assets antes de hacer push.

**Solución:**
```bash
# En local
npm run build
git add public/build
git commit -m "build: compilar assets"
git push origin main
```

Luego Ctrl+Shift+R en el navegador (hard refresh).

**Nota:** Desde la actualización del script (Dic 2025), si olvidás compilar los assets, el deploy **fallará automáticamente** con:
```
❌ ERROR: Assets no compilados. Ejecutá 'npm run build' localmente antes de hacer push.
```

Esto **previene el error 500** por assets faltantes que experimentaste anteriormente.

### Imágenes/Avatares desaparecieron

**Causa:** Se eliminó el symlink de storage.

**Solución:**
```bash
cd /home/srojasw1/public_html/mientreno/app
/opt/cpanel/ea-php84/root/usr/bin/php artisan storage:link
```

### Error de permisos (Permission Denied)

**Solución:**
```bash
cd /home/srojasw1/public_html/mientreno/app
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Auto-deploy no funciona

1. **Verificar GitHub Actions:**
   - Ve a la pestaña "Actions" en GitHub
   - Revisa el log del workflow que falló
   - Busca el error específico

2. **Verificar endpoint del webhook:**
   ```bash
   # Probar desde terminal de cPanel o SSH
   curl -X POST \
     -H "Content-Type: application/json" \
     -H "X-Deploy-Token: tu_token" \
     -d '{"source":"test"}' \
     https://mientreno.srojasweb.dev/deploy/webhook

   # Debe responder: {"success":true,"message":"Deploy completed successfully"}
   ```

3. **Verificar logs del webhook:**
   ```bash
   grep "Deploy:" /home/srojasw1/public_html/mientreno/app/storage/logs/laravel.log | tail -20
   ```

### SSH Connection Refused

**Si ves "Connection refused" al conectar por SSH:**

1. **Verifica que estás usando el puerto correcto:**
   ```bash
   ssh -p 22278 srojasw1@srojasweb.dev
   # NO el puerto 22 (estándar)
   ```

2. **Verifica que la clave esté autorizada en cPanel:**
   - Ve a cPanel → "Acceso SSH" → "Manage SSH Keys"
   - La clave debe estar en estado "Authorized"

3. **Verifica permisos de la clave local:**
   ```bash
   # En Windows (PowerShell como Admin)
   icacls $HOME\.ssh\deploy-mientreno-new /inheritance:r
   icacls $HOME\.ssh\deploy-mientreno-new /grant:r "$env:USERNAME`:R"

   # En Linux/Mac
   chmod 600 ~/.ssh/deploy-mientreno-new
   ```

## Seguridad

### Archivos Sensibles

Asegurarse que estos archivos NO estén en el repositorio:
- `.env`
- `storage/` (contenido)
- `vendor/`
- `node_modules/`
- Archivos de backup con extensión `.sql`
- Claves SSH privadas

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
.phpunit.result.cache
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

# Backup de .env
cp /home/srojasw1/public_html/mientreno/app/.env \
   /home/srojasw1/backups/env-backup-$(date +%Y%m%d)
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

### Automático (Normal)
- [ ] Assets compilados: `npm run build`
- [ ] Código commiteado y pusheado: `git push origin main`
- [ ] Verificar que GitHub Actions pasó (verde ✅)
- [ ] Verificar que la app funcione en el navegador
- [ ] Si hay migraciones, ejecutarlas manualmente
- [ ] Revisar logs por errores

### Manual (Emergencia)
- [ ] Conectar por SSH o Terminal de cPanel
- [ ] Ejecutar: `bash /home/srojasw1/deploy_mientreno.sh`
- [ ] Ejecutar migraciones si hay nuevas
- [ ] Verificar que la app funcione
- [ ] Revisar logs por errores

## Resumen de Comandos Rápidos

```bash
# CONEXIÓN
ssh mientreno-prod                    # Conectar por SSH

# DEPLOY
bash /home/srojasw1/deploy_mientreno.sh  # Deploy manual

# MIGRACIONES
cd /home/srojasw1/public_html/mientreno/app
/opt/cpanel/ea-php84/root/usr/bin/php artisan migrate --force

# CACHÉS
/opt/cpanel/ea-php84/root/usr/bin/php artisan config:cache
/opt/cpanel/ea-php84/root/usr/bin/php artisan route:cache
/opt/cpanel/ea-php84/root/usr/bin/php artisan view:cache

# LOGS
tail -50 /home/srojasw1/public_html/mientreno/app/storage/logs/laravel.log

# VERIFICACIÓN
/opt/cpanel/ea-php84/root/usr/bin/php artisan route:list
```

## Contacto y Soporte

Para problemas de hosting de cPanel, contactar al proveedor del hosting.

Para problemas de la aplicación Laravel, revisar:
- Logs de Laravel: `storage/logs/laravel.log`
- Documentación del proyecto: `docs/`
- GitHub Issues
