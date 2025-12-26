# Migración de Datos de Usuario

Sistema de exportación e importación de usuarios y sus workouts entre ambientes (local, staging, producción).

## Comandos Disponibles

### 1. Exportar Usuario

Exporta un usuario específico y todos sus workouts asociados a un archivo JSON.

```bash
php artisan user:export {email} [--output=archivo.json]
```

**Parámetros:**
- `email` (requerido): Email del usuario a exportar

**Opciones:**
- `--output`: Ruta del archivo de salida (por defecto: `storage/app/user-export.json`)

**Ejemplo:**
```bash
php artisan user:export sec.rojas@gmail.com
php artisan user:export athlete@example.com --output=backup-athlete.json
```

**Qué se exporta:**
- Datos del usuario (nombre, email, contraseña hasheada, perfil, configuraciones)
- Todos los workouts del usuario
- Metadata (fecha de exportación, estadísticas)

**Importante:**
- La contraseña se exporta ya hasheada (seguro)
- Si el usuario tiene avatar, debes copiar manualmente el archivo de imagen
- El archivo JSON se genera en `storage/app/`

### 2. Importar Usuario

Importa un usuario y sus workouts desde un archivo JSON previamente exportado.

```bash
php artisan user:import [archivo] [opciones]
```

**Parámetros:**
- `file` (opcional): Ruta del archivo JSON a importar (por defecto: `storage/app/user-export.json`)

**Opciones:**
- `--business-id=X`: ID del business al que pertenecerá el usuario
- `--dry-run`: Previsualizar sin insertar datos reales
- `--force`: Sobrescribir workouts duplicados existentes
- `--skip-user`: Omitir creación de usuario (solo importar workouts)

**Ejemplos:**
```bash
# Previsualizar importación
php artisan user:import --dry-run

# Importar usuario y workouts
php artisan user:import

# Importar asignando a un business específico
php artisan user:import --business-id=1

# Importar solo workouts (usuario ya existe)
php artisan user:import --skip-user

# Sobrescribir workouts duplicados
php artisan user:import --force

# Importar desde archivo personalizado
php artisan user:import backup-athlete.json
```

## Proceso de Migración Local → Producción

### Paso 1: Exportar en Local

```bash
php artisan user:export sec.rojas@gmail.com
```

Esto genera el archivo: `storage/app/user-export.json`

### Paso 2: Transferir Archivos a Producción

**Archivo JSON:**
```bash
# Copiar el archivo JSON al servidor de producción
scp storage/app/user-export.json usuario@servidor:/ruta/app/storage/app/
```

**Avatar (si existe):**
```bash
# Si el usuario tiene avatar, copiarlo también
scp storage/app/public/avatars/archivo.jpg usuario@servidor:/ruta/app/storage/app/public/avatars/
```

### Paso 3: Importar en Producción

```bash
# SSH al servidor de producción
ssh usuario@servidor

# Ir al directorio de la aplicación
cd /ruta/app

# Previsualizar primero (recomendado)
php artisan user:import --dry-run

# Si todo se ve bien, importar
php artisan user:import

# O asignar a un business específico
php artisan user:import --business-id=1
```

## Manejo de Casos Especiales

### Usuario Ya Existe en Destino

Si el usuario ya existe (mismo email), el comando:
- No creará un usuario duplicado
- Usará el usuario existente para importar los workouts
- Mostrará un mensaje de advertencia

### Workouts Duplicados

Los workouts se identifican por `user_id` + `date`. Si ya existe un workout para el mismo usuario en la misma fecha:

- **Sin `--force`**: El workout duplicado se omite
- **Con `--force`**: El workout existente se elimina y se reemplaza con el del archivo

### Solo Importar Workouts

Si el usuario ya existe y solo quieres importar workouts nuevos:

```bash
php artisan user:import --skip-user
```

Esto omitirá la creación del usuario y solo importará los workouts.

## Estructura del Archivo JSON

```json
{
  "exported_at": "2025-12-26 10:30:00",
  "user": {
    "name": "Sebastian Rojas",
    "email": "sec.rojas@gmail.com",
    "password": "$2y$12$...", // Ya hasheada
    "role": "athlete",
    "profile": {...},
    "avatar": "avatars/archivo.jpg",
    "birth_date": "1990-01-01",
    "gender": "male",
    "weight": "70.00",
    "height": 175,
    "bio": "...",
    "email_verified_at": "2025-01-01 00:00:00"
  },
  "workouts": [
    {
      "date": "2025-01-15",
      "type": "easy_run",
      "status": "completed",
      "distance": "10.50",
      "duration": 3600,
      "avg_pace": 343,
      "avg_heart_rate": 150,
      "elevation_gain": 100,
      "difficulty": 3,
      "notes": "Buen entrenamiento",
      "weather": {"temp": 25, "conditions": "sunny"},
      "route": null,
      "is_race": false
    }
  ],
  "stats": {
    "total_workouts": 74
  }
}
```

## Consideraciones de Seguridad

- La contraseña se mantiene hasheada en todo el proceso
- El archivo JSON contiene datos sensibles, manejar con cuidado
- No versionar archivos de exportación en Git
- Usar conexiones seguras (SCP/SFTP) para transferir archivos

## Limitaciones

- No exporta/importa relaciones con `training_groups` o `races` (se establecen como `null`)
- No exporta/importa relación con `business` (debe especificarse con `--business-id`)
- Los avatares deben copiarse manualmente
- Solo funciona para un usuario a la vez

## Troubleshooting

### Error: "Usuario no encontrado"
Verifica que el email sea correcto y que el usuario exista en la base de datos.

### Error: "Archivo no encontrado"
Verifica la ruta del archivo. Por defecto se busca en `storage/app/user-export.json`.

### Workouts no se importan
- Verifica que el usuario se haya creado/encontrado correctamente
- Usa `--dry-run` para previsualizar y ver errores
- Revisa los logs de Laravel para más detalles

### Avatar no aparece
Los avatares deben copiarse manualmente. El comando te indicará la ruta del archivo a copiar.

## Ejemplos de Uso Completos

### Migrar usuario de desarrollo a producción

```bash
# Local
php artisan user:export coach@example.com

# Copiar archivo
scp storage/app/user-export.json produccion:/var/www/app/storage/app/

# Producción
ssh produccion
php artisan user:import --dry-run
php artisan user:import --business-id=1
```

### Backup de usuario

```bash
php artisan user:export admin@app.com --output=backups/admin-backup-2025-12-26.json
```

### Restaurar desde backup

```bash
php artisan user:import backups/admin-backup-2025-12-26.json --dry-run
php artisan user:import backups/admin-backup-2025-12-26.json
```
