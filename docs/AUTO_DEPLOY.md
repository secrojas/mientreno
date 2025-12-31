# Auto-Deploy Automático con GitHub Actions

Sistema de deploy automático que se ejecuta cada vez que haces `git push` a la rama `main`.

## Cómo Funciona

```
┌──────────────┐      ┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│              │      │              │      │              │      │              │
│  git push    │─────▶│   GitHub     │─────▶│   Webhook    │─────▶│   Script     │
│  origin main │      │   Actions    │      │   Laravel    │      │   Deploy     │
│              │      │              │      │              │      │              │
└──────────────┘      └──────────────┘      └──────────────┘      └──────────────┘
        │                                           │                      │
        │                                           │                      │
        ▼                                           ▼                      ▼
  npm run build                           Valida token             git pull origin main
  (antes del push)                        Ejecuta script           composer install
                                                                   Cachea Laravel
                                                                          │
                                                                          ▼
                                                                   ┌──────────────┐
                                                                   │              │
                                                                   │  Aplicación  │
                                                                   │  Actualizada │
                                                                   │              │
                                                                   └──────────────┘
```

## Resumen Ejecutivo

**Una vez configurado, solo necesitas:**

```bash
npm run build  # Compilar assets
git add .
git commit -m "feat: nueva funcionalidad"
git push origin main  # Deploy automático
```

**GitHub Actions se encarga del resto.** Los cambios estarán en producción en ~1-2 minutos.

## Configuración Inicial (Solo una vez)

### Paso 1: Generar Token de Deploy

Desde tu PC local, genera un token seguro:

```bash
# En Windows PowerShell
-join ((48..57) + (65..90) + (97..122) | Get-Random -Count 64 | % {[char]$_})

# O en Linux/Mac
openssl rand -hex 32
```

Guarda el token generado (ejemplo: `dYesk9JpwXjRPbN3xQ18OUyLurmEihFvC7ZDaGIKWAVMTo5Hq26ngl0t4fczBS`).

### Paso 2: Configurar Token en Producción

Desde la Terminal de cPanel o por SSH:

```bash
# Editar .env
cd /home/srojasw1/public_html/mientreno/app
nano .env

# Agregar esta línea (reemplazar con tu token):
DEPLOY_TOKEN=dYesk9JpwXjRPbN3xQ18OUyLurmEihFvC7ZDaGIKWAVMTo5Hq26ngl0t4fczBS

# Guardar: Ctrl+X, Y, Enter

# Limpiar caché de configuración
/opt/cpanel/ea-php84/root/usr/bin/php artisan config:clear
```

### Paso 3: Configurar Secrets en GitHub

1. Ve a tu repositorio en GitHub
2. Click en **Settings** → **Secrets and variables** → **Actions**
3. Click en **New repository secret**
4. Crear dos secrets:

**Secret 1:**
- Name: `DEPLOY_TOKEN`
- Value: `dYesk9JpwXjRPbN3xQ18OUyLurmEihFvC7ZDaGIKWAVMTo5Hq26ngl0t4fczBS`
  (El token que generaste en el Paso 1)

**Secret 2:**
- Name: `DEPLOY_URL`
- Value: `https://mientreno.srojasweb.dev/deploy/webhook`

  **IMPORTANTE:** La URL debe ser exactamente:
  - `https://mientreno.srojasweb.dev/deploy/webhook`
  - No incluir espacios al principio ni al final
  - Debe terminar en `/deploy/webhook`

5. Click en **Add secret** para cada uno

### Paso 4: Verificar que el Endpoint Funciona

Desde tu PC local, probar el endpoint con curl:

```bash
# Verificar endpoint de ping
curl https://mientreno.srojasweb.dev/deploy/ping?token=dYesk9JpwXjRPbN3xQ18OUyLurmEihFvC7ZDaGIKWAVMTo5Hq26ngl0t4fczBS
```

Deberías recibir una respuesta como:
```json
{
  "success": true,
  "message": "Deploy webhook is working",
  "server_time": "2025-12-29T15:30:00.000000Z",
  "script_exists": true
}
```

Si `script_exists` es `false`, asegúrate que el script de deploy existe en `/home/srojasw1/deploy_mientreno.sh`.

**Probar el endpoint de deploy:**

```bash
curl -X POST \
  -H "Content-Type: application/json" \
  -H "X-Deploy-Token: dYesk9JpwXjRPbN3xQ18OUyLurmEihFvC7ZDaGIKWAVMTo5Hq26ngl0t4fczBS" \
  -d '{"source":"test","branch":"main"}' \
  https://mientreno.srojasweb.dev/deploy/webhook
```

Debería responder:
```json
{
  "success": true,
  "message": "Deploy completed successfully",
  "output": ">> Pull del repo\n..."
}
```

## Uso Diario

### Flujo de Trabajo Completo

1. **Desarrollar localmente:**
   ```bash
   npm run dev  # Servidor de desarrollo
   # Hacer cambios en el código
   ```

2. **Compilar assets para producción:**
   ```bash
   npm run build  # ← IMPORTANTE: Siempre antes de commit
   ```

3. **Commit y push:**
   ```bash
   git add .
   git commit -m "feat: nueva funcionalidad"
   git push origin main
   ```

4. **Automáticamente se ejecuta:**
   - ✅ GitHub detecta el push a `main`
   - ✅ GitHub Actions se ejecuta
   - ✅ Llama al webhook de producción
   - ✅ El webhook ejecuta el script de deploy
   - ✅ El script hace pull, copia archivos, instala composer, cachea Laravel
   - ✅ La aplicación se actualiza en https://mientreno.srojasweb.dev

5. **Verificar en GitHub Actions:**
   - Ve a la pestaña **Actions** en GitHub
   - El workflow "Deploy to cPanel" debe aparecer verde ✅

### Monitorear el Deploy

**Desde GitHub:**
1. Ve a tu repositorio
2. Click en la pestaña **Actions**
3. Verás el workflow "Deploy to cPanel" ejecutándose
4. Click en el workflow para ver el progreso en tiempo real
5. Si sale verde ✅ = Deploy exitoso
6. Si sale rojo ❌ = Deploy falló (click para ver el error)

**Desde el Servidor (por SSH o Terminal cPanel):**

```bash
# Ver logs de deploy en Laravel
tail -f /home/srojasw1/public_html/mientreno/app/storage/logs/laravel.log | grep "Deploy:"

# Ver si el script se está ejecutando
ps aux | grep deploy_mientreno.sh

# Ver último commit desplegado
cd /home/srojasw1/repositories/mientreno
git log -1 --oneline
```

### Si hay Migraciones Nuevas

El auto-deploy **NO ejecuta migraciones automáticamente** por seguridad. Después de un deploy con migraciones, ejecutar manualmente:

```bash
# Por SSH
ssh mientreno-prod
cd /home/srojasw1/public_html/mientreno/app
/opt/cpanel/ea-php84/root/usr/bin/php artisan migrate --force

# O en una línea desde tu PC
ssh mientreno-prod "cd /home/srojasw1/public_html/mientreno/app && /opt/cpanel/ea-php84/root/usr/bin/php artisan migrate --force"
```

## Archivos del Sistema

### 1. Endpoint de Deploy: `app/Http/Controllers/DeployController.php`

Controlador Laravel que:
- Valida el token de autenticación (`X-Deploy-Token` header)
- Ejecuta el script de deploy usando Symfony Process
- Registra todo en los logs de Laravel
- Devuelve el output del script

**Métodos:**
- `POST /deploy/webhook` - Ejecuta el deploy
- `GET /deploy/ping` - Verifica que el endpoint funciona

### 2. Rutas: `routes/web.php`

```php
Route::post('/deploy/webhook', [DeployController::class, 'deploy'])->name('deploy.webhook');
Route::get('/deploy/ping', [DeployController::class, 'ping'])->name('deploy.ping');
```

**CSRF Protection:** Las rutas `/deploy/*` están excluidas de CSRF en `bootstrap/app.php`:

```php
$middleware->validateCsrfTokens(except: [
    'deploy/*',
]);
```

### 3. GitHub Action: `.github/workflows/deploy.yml`

Workflow que:
- Se activa en cada `push` a `main`
- Llama al endpoint `/deploy/webhook` con el token
- Usa los secrets `DEPLOY_TOKEN` y `DEPLOY_URL`
- Muestra el resultado del deploy

```yaml
name: Deploy to cPanel

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Trigger deploy webhook
        run: |
          response=$(curl -s -w "\n%{http_code}" -X POST \
            -H "Content-Type: application/json" \
            -H "X-Deploy-Token: ${{ secrets.DEPLOY_TOKEN }}" \
            -d '{"source":"github-actions","branch":"main"}' \
            "${{ secrets.DEPLOY_URL }}")

          http_code=$(echo "$response" | tail -n1)
          body=$(echo "$response" | sed '$d')

          echo "HTTP Status: $http_code"
          echo "Response: $body"

          if [ "$http_code" != "200" ]; then
            echo "Deploy failed with status code $http_code"
            exit 1
          fi
```

### 4. Script de Deploy: `/home/srojasw1/deploy_mientreno.sh`

Script bash que:
- Hace `git pull origin main` del repositorio
- Copia archivos de Laravel a producción (sin tocar `.env`, `storage`, `index.php`)
- Instala dependencias de Composer con rutas absolutas
- **NO ejecuta npm** (los assets se compilan localmente)
- Cachea configuración, rutas y vistas de Laravel
- Ajusta permisos de `storage` y `bootstrap/cache`

**Características importantes:**
- Usa rutas absolutas para `composer` y `php` (compatibilidad con webhook)
- Exporta variables de entorno `HOME` y `COMPOSER_HOME`
- No requiere npm en producción (assets vienen compilados desde local)

## Troubleshooting

### Workflow falla con "Unauthorized" (401)

**Causa:** El token no coincide entre GitHub y el servidor.

**Solución:**
1. Verifica que el `DEPLOY_TOKEN` en GitHub Secrets sea exactamente el mismo que en `.env` de producción
2. En producción, limpia la caché:
   ```bash
   cd /home/srojasw1/public_html/mientreno/app
   /opt/cpanel/ea-php84/root/usr/bin/php artisan config:clear
   ```
3. Verifica que no haya espacios al principio o final del token

### Workflow falla con "404 Not Found"

**Causa:** La URL del webhook es incorrecta.

**Solución:**
1. Verifica que `DEPLOY_URL` en GitHub Secrets sea exactamente:
   ```
   https://mientreno.srojasweb.dev/deploy/webhook
   ```
2. Asegúrate que las rutas estén cacheadas en producción:
   ```bash
   cd /home/srojasw1/public_html/mientreno/app
   /opt/cpanel/ea-php84/root/usr/bin/php artisan route:clear
   /opt/cpanel/ea-php84/root/usr/bin/php artisan route:cache
   ```
3. Verifica que el `DeployController` esté en el servidor:
   ```bash
   ls -la /home/srojasw1/public_html/mientreno/app/app/Http/Controllers/DeployController.php
   ```

### Workflow falla con "415 Unsupported Media Type"

**Causa:** El servidor no acepta el formato de la request.

**Solución:**
1. Verifica que el workflow incluya el header `Content-Type: application/json`
2. Verifica que el `DeployController` no valide estrictamente el payload
3. El DeployController actual es tolerante - solo valida el token

### El deploy se ejecuta pero no actualiza el código

**Causa:** Hay un error en el script de deploy.

**Solución:**
```bash
# Ver logs de Laravel
tail -100 /home/srojasw1/public_html/mientreno/app/storage/logs/laravel.log

# Ejecutar el script manualmente para ver errores
ssh mientreno-prod
bash /home/srojasw1/deploy_mientreno.sh
```

**Errores comunes:**
- **"composer: command not found"** → El script debe usar ruta absoluta: `/home/srojasw1/bin/composer`
- **"php: command not found"** → Usar ruta absoluta: `/opt/cpanel/ea-php84/root/usr/bin/php`
- **"HOME environment variable must be set"** → El script debe exportar `HOME` y `COMPOSER_HOME`
- **"ERROR: Assets no compilados"** → Olvidaste ejecutar `npm run build` antes de hacer push (ver solución abajo)

### CSS/JS no se actualizan después del deploy

**Causa:** Olvidaste compilar los assets antes de hacer push.

**Solución:**
```bash
# SIEMPRE antes de hacer push:
npm run build
git add public/build
git commit -m "build: compilar assets"
git push origin main
```

**Importante:** El servidor de producción NO ejecuta `npm run build`. Los assets deben venir compilados desde tu PC local.

**Nota:** Desde la actualización del script, si olvidás compilar los assets, el deploy **fallará automáticamente** con el error:
```
❌ ERROR: Assets no compilados. Ejecutá 'npm run build' localmente antes de hacer push.
```

Esto previene que el sitio quede en error 500 por assets faltantes.

### "script_exists": false en /deploy/ping

**Causa:** El script de deploy no existe o está en la ruta incorrecta.

**Solución:**
```bash
# Verificar que existe
ls -la /home/srojasw1/deploy_mientreno.sh

# Si no existe, crearlo
ssh mientreno-prod
nano /home/srojasw1/deploy_mientreno.sh
# Pegar el contenido del script (ver DEPLOY_CPANEL.md)

# Dar permisos de ejecución
chmod +x /home/srojasw1/deploy_mientreno.sh
```

### El deploy se ejecuta en commits que no quiero

**Solución:** Trabajar en una rama de desarrollo:

```bash
# Crear y trabajar en rama dev
git checkout -b dev
git add .
git commit -m "work in progress"
git push origin dev  # No ejecuta deploy

# Cuando esté listo para producción
git checkout main
git merge dev
git push origin main  # ← Solo esto ejecuta el deploy
```

## Seguridad

### Token de Deploy

- ✅ **Usa un token fuerte** (mínimo 32 caracteres, idealmente 64)
- ✅ **Nunca lo commits al repositorio** (está en `.env` que está en `.gitignore`)
- ✅ **Solo está en GitHub Secrets** (encriptado por GitHub)
- ✅ El endpoint valida el token en cada request
- ✅ Si el token es incorrecto, se registra en logs con la IP del atacante

### Logs de Deploy

Los deploys quedan registrados en `storage/logs/laravel.log` con:
- Timestamp
- IP desde donde se llamó
- User agent
- Resultado del deploy (éxito o error)
- Output completo del script

```bash
# Ver historial de deploys
grep "Deploy:" /home/srojasw1/public_html/mientreno/app/storage/logs/laravel.log

# Ver último deploy
grep "Deploy:" /home/srojasw1/public_html/mientreno/app/storage/logs/laravel.log | tail -20
```

### Protección CSRF

Las rutas `/deploy/*` están excluidas de la protección CSRF porque:
- El webhook viene desde GitHub (dominio externo)
- La autenticación se hace mediante el token `X-Deploy-Token`
- No hay sesión de usuario involucrada

## Deshabilitar Auto-Deploy Temporalmente

### Opción 1: Deshabilitar el Workflow (Recomendado)

1. Ve a GitHub → **Actions**
2. Click en el workflow "Deploy to cPanel"
3. Click en los tres puntos (⋮) → **Disable workflow**

Para reactivar:
1. Ve a GitHub → **Actions**
2. Click en el workflow "Deploy to cPanel"
3. Click en **Enable workflow**

### Opción 2: Eliminar el Token en Producción

```bash
ssh mientreno-prod
cd /home/srojasw1/public_html/mientreno/app
nano .env
# Comentar o eliminar la línea DEPLOY_TOKEN
# Guardar: Ctrl+X, Y, Enter

/opt/cpanel/ea-php84/root/usr/bin/php artisan config:clear
```

Esto hará que el webhook rechace todas las peticiones con error 401 Unauthorized.

## Deploy Manual

El auto-deploy y el deploy manual pueden coexistir sin problemas. Si necesitas hacer un deploy manual:

```bash
# Por Terminal de cPanel
bash /home/srojasw1/deploy_mientreno.sh

# Por SSH desde tu PC
ssh mientreno-prod "bash /home/srojasw1/deploy_mientreno.sh"
```

Ver [DEPLOY_CPANEL.md](DEPLOY_CPANEL.md) para más detalles sobre deploy manual.

## Ventajas del Auto-Deploy

✅ **Más rápido:** No necesitas conectarte al servidor
✅ **Menos errores:** El proceso es siempre el mismo
✅ **Trazabilidad:** Todos los deploys quedan registrados en GitHub Actions
✅ **Rollback fácil:** Puedes revertir commits y volver a pushear
✅ **Workflow moderno:** Similar a Vercel, Netlify, Railway, etc.
✅ **Colaboración:** Todo el equipo puede deployar sin necesitar acceso SSH

## Diferencias con Otros Servicios

**vs. Vercel/Netlify:**
- En Vercel/Netlify, ellos compilan los assets y ejecutan el build
- En nuestro auto-deploy, compilamos localmente y subimos al repo
- Ventaja: Mayor control sobre el proceso de build
- Desventaja: Debemos recordar hacer `npm run build` antes de push

**vs. GitHub Pages:**
- GitHub Pages es solo para sitios estáticos
- Nuestro auto-deploy soporta PHP/Laravel con bases de datos

**vs. Deploy manual:**
- Deploy manual requiere SSH o Terminal de cPanel
- Auto-deploy solo requiere `git push`
- Deploy manual es útil para casos de emergencia

## Mejoras Futuras

Posibles mejoras al sistema:

1. **Pre-commit hook local:** Hook que ejecute automáticamente `npm run build` antes de cada commit
2. **Notificaciones:** Enviar email o Slack cuando hay un deploy exitoso o fallido
3. **Tests automáticos:** Ejecutar PHPUnit antes de deployar, y solo deployar si pasan los tests
4. **Deploy por ambiente:** Tener staging y production separados (rama `dev` → staging, rama `main` → production)
5. **Rollback automático:** Si el deploy falla, volver a la versión anterior automáticamente
6. **Health check:** Después del deploy, verificar que la app responda correctamente

## Checklist de Deploy

### Antes de Push
- [ ] Código funciona en local (`npm run dev`, probar en navegador)
- [ ] Tests pasan (si hay): `php artisan test`
- [ ] Assets compilados: `npm run build`
- [ ] Archivos añadidos: `git add .`
- [ ] Commit con mensaje descriptivo: `git commit -m "..."`

### Después de Push
- [ ] Verificar GitHub Actions (verde ✅)
- [ ] Verificar app en producción (https://mientreno.srojasweb.dev)
- [ ] Si hay migraciones, ejecutarlas manualmente
- [ ] Revisar logs por errores: `tail -50 storage/logs/laravel.log`
- [ ] Probar funcionalidad nueva

## Referencia Rápida

```bash
# ========================================
# DESARROLLO LOCAL
# ========================================

# Desarrollo
npm run dev

# Compilar para producción (ANTES DE PUSH)
npm run build

# Commit y push
git add .
git commit -m "feat: nueva funcionalidad"
git push origin main  # ← Deploy automático

# ========================================
# VERIFICACIÓN
# ========================================

# Ver GitHub Actions
# GitHub → Actions → Deploy to cPanel

# Probar endpoint de ping
curl https://mientreno.srojasweb.dev/deploy/ping?token=TU_TOKEN

# Probar endpoint de deploy manualmente
curl -X POST \
  -H "Content-Type: application/json" \
  -H "X-Deploy-Token: TU_TOKEN" \
  -d '{"source":"test"}' \
  https://mientreno.srojasweb.dev/deploy/webhook

# ========================================
# LOGS Y MONITOREO
# ========================================

# Ver logs de deploy (por SSH)
ssh mientreno-prod
tail -f /home/srojasw1/public_html/mientreno/app/storage/logs/laravel.log | grep "Deploy:"

# Ver historial de deploys
grep "Deploy:" /home/srojasw1/public_html/mientreno/app/storage/logs/laravel.log

# ========================================
# DEPLOY MANUAL (EMERGENCIA)
# ========================================

# Por SSH
ssh mientreno-prod "bash /home/srojasw1/deploy_mientreno.sh"

# ========================================
# MIGRACIONES (DESPUÉS DE AUTO-DEPLOY)
# ========================================

# Ejecutar migraciones
ssh mientreno-prod "cd /home/srojasw1/public_html/mientreno/app && /opt/cpanel/ea-php84/root/usr/bin/php artisan migrate --force"
```

## Soporte

Si tienes problemas:

1. **Revisa GitHub Actions:**
   - Ve a la pestaña Actions
   - Click en el workflow que falló
   - Lee el error en el log

2. **Revisa logs de Laravel:**
   ```bash
   ssh mientreno-prod
   tail -100 /home/srojasw1/public_html/mientreno/app/storage/logs/laravel.log
   ```

3. **Ejecuta el script manualmente:**
   ```bash
   ssh mientreno-prod
   bash /home/srojasw1/deploy_mientreno.sh
   ```

4. **Verifica configuración:**
   - Token en GitHub Secrets = Token en `.env` de producción
   - URL en GitHub Secrets = `https://mientreno.srojasweb.dev/deploy/webhook`
   - Rutas cacheadas: `php artisan route:cache`

5. **Consulta la documentación:**
   - [DEPLOY_CPANEL.md](DEPLOY_CPANEL.md) - Deploy manual y configuración SSH
   - [AUTO_DEPLOY.md](AUTO_DEPLOY.md) - Este archivo
