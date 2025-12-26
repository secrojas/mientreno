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
                                                                          │
                                                                          ▼
                                                                   ┌──────────────┐
                                                                   │              │
                                                                   │  Aplicación  │
                                                                   │  Actualizada │
                                                                   │              │
                                                                   └──────────────┘
```

## Configuración Inicial (Solo una vez)

### Paso 1: Generar Token de Deploy

Desde tu PC local, genera un token seguro:

```bash
# En Windows PowerShell
-join ((48..57) + (65..90) + (97..122) | Get-Random -Count 64 | % {[char]$_})

# O en Linux/Mac
openssl rand -hex 32
```

Guarda el token generado (ejemplo: `a1b2c3d4e5f6...`). Lo necesitarás en los siguientes pasos.

### Paso 2: Configurar Token en Producción

Desde la Terminal de cPanel en producción:

```bash
# Editar .env
cd /home/srojasw1/public_html/mientreno/app
nano .env

# Agregar esta línea (reemplazar con tu token):
DEPLOY_TOKEN=a1b2c3d4e5f6...

# Guardar: Ctrl+X, Y, Enter

# Limpiar caché de configuración
php artisan config:clear
```

### Paso 3: Configurar Secrets en GitHub

1. Ve a tu repositorio en GitHub
2. Click en **Settings** → **Secrets and variables** → **Actions**
3. Click en **New repository secret**
4. Crear dos secrets:

**Secret 1:**
- Name: `DEPLOY_TOKEN`
- Value: El token que generaste en el Paso 1

**Secret 2:**
- Name: `DEPLOY_URL`
- Value: `https://tudominio.com/deploy/webhook`
  - Reemplaza `tudominio.com` con tu dominio real
  - Ejemplo: `https://srojasweb.dev/deploy/webhook`

5. Click en **Add secret** para cada uno

### Paso 4: Verificar que el Endpoint Funciona

Desde tu PC local, probar el endpoint con curl:

```bash
# Reemplazar con tus valores
curl -X GET \
  -H "X-Deploy-Token: TU_TOKEN_AQUI" \
  https://tudominio.com/deploy/ping
```

Deberías recibir una respuesta como:
```json
{
  "success": true,
  "message": "Deploy webhook is working",
  "server_time": "2025-12-26T15:30:00.000000Z",
  "script_exists": true
}
```

Si `script_exists` es `false`, asegúrate que el script de deploy existe en `/home/srojasw1/deploy_mientreno.sh`.

## Uso Diario

### Deploy Automático

Simplemente trabaja normal en tu código y haz push:

```bash
git add .
git commit -m "feat: nueva funcionalidad"
git push origin main
```

**Automáticamente:**
1. ✅ GitHub detecta el push a `main`
2. ✅ GitHub Actions se ejecuta
3. ✅ Llama al webhook de tu servidor
4. ✅ El webhook ejecuta el script de deploy
5. ✅ Tu aplicación se actualiza en producción

### Monitorear el Deploy

**Desde GitHub:**
1. Ve a tu repositorio
2. Click en la pestaña **Actions**
3. Verás el workflow "Deploy to Production" ejecutándose
4. Click en el workflow para ver el progreso en tiempo real

**Desde el Servidor:**

```bash
# Ver logs de deploy en Laravel
tail -f /home/srojasw1/public_html/mientreno/app/storage/logs/laravel.log | grep "Deploy:"

# Ver si el script se está ejecutando
ps aux | grep deploy_mientreno.sh
```

### Verificar Deploy Completado

```bash
# Desde la Terminal de cPanel
cd /home/srojasw1/public_html/mientreno/app

# Ver último commit desplegado
git log -1 --oneline

# Verificar que la app está funcionando
php artisan --version
```

## Archivos del Sistema

### 1. Endpoint de Deploy: `app/Http/Controllers/DeployController.php`

Controlador Laravel que:
- Valida el token de autenticación
- Verifica que el push sea a la rama `main`
- Ejecuta el script de deploy
- Registra todo en los logs

### 2. GitHub Action: `.github/workflows/deploy.yml`

Workflow que:
- Se activa en cada `push` a `main`
- Llama al endpoint de deploy con el token
- Notifica si el deploy fue exitoso o falló

### 3. Script de Deploy: `/home/srojasw1/deploy_mientreno.sh`

Script bash que:
- Hace `git pull` del repositorio
- Copia archivos a producción
- Instala dependencias de Composer
- Compila assets con npm
- Cachea configuración de Laravel

## Troubleshooting

### El workflow falla con "Unauthorized"

**Causa:** El token no coincide entre GitHub y el servidor.

**Solución:**
1. Verifica que el `DEPLOY_TOKEN` en GitHub Secrets sea el mismo que en `.env` de producción
2. Limpia la caché: `php artisan config:clear`

### El workflow falla con "Connection refused" o timeout

**Causa:** La URL del webhook es incorrecta o el servidor no es accesible.

**Solución:**
1. Verifica que `DEPLOY_URL` en GitHub Secrets sea correcta
2. Prueba acceder a `https://tudominio.com/deploy/ping` desde el navegador
3. Asegúrate que el dominio apunta al servidor correcto

### El deploy se ejecuta pero no actualiza el código

**Causa:** Hay un error en el script de deploy.

**Solución:**
```bash
# Ver logs de Laravel
tail -100 /home/srojasw1/public_html/mientreno/app/storage/logs/laravel.log

# Ejecutar el script manualmente para ver errores
cd /home/srojasw1
./deploy_mientreno.sh
```

### "script_exists": false en /deploy/ping

**Causa:** El script de deploy no existe o está en la ruta incorrecta.

**Solución:**
```bash
# Verificar que existe
ls -la /home/srojasw1/deploy_mientreno.sh

# Si no existe, crearlo desde el repo
cd /home/srojasw1/repositories/mientreno
git pull origin main
cp deploy_cpanel.sh /home/srojasw1/deploy_mientreno.sh
chmod +x /home/srojasw1/deploy_mientreno.sh
```

### El deploy se ejecuta en commits que no quiero

**Solución:** Crear una rama de desarrollo:

```bash
# Trabajar en rama dev
git checkout -b dev
git add .
git commit -m "work in progress"
git push origin dev

# Cuando esté listo para producción, merge a main
git checkout main
git merge dev
git push origin main  # ← Solo esto ejecutará el deploy
```

## Seguridad

### Token de Deploy

- ✅ **Usa un token fuerte** (mínimo 32 caracteres)
- ✅ **Nunca lo commits al repositorio** (está en `.env` que está en `.gitignore`)
- ✅ **Solo está en GitHub Secrets** (encriptado)
- ✅ El endpoint valida el token en cada request

### Logs de Deploy

Los deploys quedan registrados en `storage/logs/laravel.log` con:
- Timestamp
- IP desde donde se llamó
- User agent
- Resultado del deploy
- Output del script

```bash
# Ver historial de deploys
grep "Deploy:" /home/srojasw1/public_html/mientreno/app/storage/logs/laravel.log
```

## Deshabilitar Auto-Deploy

Si necesitas deshabilitar el auto-deploy temporalmente:

### Opción 1: Deshabilitar el Workflow (Recomendado)

1. Ve a GitHub → **Actions**
2. Click en el workflow "Deploy to Production"
3. Click en los tres puntos (⋮) → **Disable workflow**

### Opción 2: Eliminar el Token

Desde producción:
```bash
nano /home/srojasw1/public_html/mientreno/app/.env
# Comentar o eliminar la línea DEPLOY_TOKEN
php artisan config:clear
```

Esto hará que el webhook rechace todas las peticiones.

## Deploy Manual

Si prefieres hacer un deploy manual en algún momento:

```bash
# Desde la Terminal de cPanel
cd /home/srojasw1
./deploy_mientreno.sh
```

El auto-deploy y el manual pueden coexistir sin problemas.

## Ventajas del Auto-Deploy

✅ **Más rápido:** No necesitas conectarte al servidor
✅ **Menos errores:** El proceso es siempre el mismo
✅ **Trazabilidad:** Todos los deploys quedan registrados en GitHub Actions
✅ **Rollback fácil:** Puedes revertir commits y volver a pushear
✅ **Workflow moderno:** Similar a Vercel, Netlify, etc.

## Mejoras Futuras

Posibles mejoras al sistema:

1. **Notificaciones:** Enviar email o Slack cuando hay un deploy
2. **Tests automáticos:** Ejecutar tests antes de deployar
3. **Deploy por ambiente:** Tener staging y production separados
4. **Rollback automático:** Si el deploy falla, volver a la versión anterior

## Referencia Rápida

```bash
# Generar token
openssl rand -hex 32

# Probar endpoint
curl -X GET -H "X-Deploy-Token: TOKEN" https://tudominio.com/deploy/ping

# Ver logs
tail -f storage/logs/laravel.log | grep "Deploy:"

# Deploy manual
./deploy_mientreno.sh

# Ver historial de GitHub Actions
# GitHub → Actions → Deploy to Production
```

## Soporte

Si tienes problemas:
1. Revisa los logs de Laravel: `storage/logs/laravel.log`
2. Revisa el workflow en GitHub Actions
3. Ejecuta el script manualmente para debuggear
4. Verifica que el token sea correcto en ambos lados
