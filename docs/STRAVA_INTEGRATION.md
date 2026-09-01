# Integración con Strava

## Visión General

Permitir que los usuarios conecten su cuenta de Strava y sincronicen sus actividades automáticamente como workouts en MiEntreno. El usuario no tiene que cargar manualmente cada entrenamiento si ya lo registró en Strava.

---

## Flujo de Usuario

```
1. Usuario va a Perfil → Conexiones → "Conectar con Strava"
2. Redirige a OAuth de Strava (autoriza permisos)
3. Strava redirige de vuelta con código de autorización
4. MiEntreno obtiene access_token + refresh_token
5. Se hace sync inicial (últimas N actividades)
6. Sync automático periódico (webhook o cron)
```

---

## Arquitectura Propuesta

### Base de Datos

**Nueva tabla: `strava_connections`**
```sql
CREATE TABLE strava_connections (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NOT NULL,
    strava_athlete_id BIGINT NOT NULL,
    access_token    TEXT NOT NULL,          -- cifrado en reposo
    refresh_token   TEXT NOT NULL,          -- cifrado en reposo
    token_expires_at TIMESTAMP NOT NULL,
    scope           VARCHAR(255),           -- permisos otorgados
    athlete_data    JSON,                   -- datos del atleta Strava (nombre, foto, etc.)
    last_synced_at  TIMESTAMP NULL,
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user (user_id),
    UNIQUE KEY unique_athlete (strava_athlete_id)
);
```

**Modificación tabla `workouts`:**
```sql
ALTER TABLE workouts
    ADD COLUMN strava_id     BIGINT NULL UNIQUE AFTER id,
    ADD COLUMN source        ENUM('manual', 'strava') DEFAULT 'manual' AFTER strava_id;
```

### Nuevos Archivos

```
app/
├── Models/
│   └── StravaConnection.php
├── Services/
│   └── StravaService.php          ← OAuth + API calls + mapeo
├── Jobs/
│   └── SyncStravaActivities.php   ← Job para sincronización
├── Http/Controllers/
│   └── StravaController.php       ← OAuth redirect + callback + disconnect
migrations/
├── xxxx_create_strava_connections_table.php
├── xxxx_add_strava_fields_to_workouts_table.php
```

---

## Plan de Implementación

### FASE 1 — OAuth y Conexión

**1.1 Configuración Strava API**
- Crear app en https://www.strava.com/settings/api
- Obtener `Client ID` y `Client Secret`
- Agregar a `.env`:
  ```env
  STRAVA_CLIENT_ID=
  STRAVA_CLIENT_SECRET=
  STRAVA_REDIRECT_URI="${APP_URL}/strava/callback"
  ```
- Agregar a `config/services.php`:
  ```php
  'strava' => [
      'client_id'     => env('STRAVA_CLIENT_ID'),
      'client_secret' => env('STRAVA_CLIENT_SECRET'),
      'redirect'      => env('STRAVA_REDIRECT_URI'),
  ],
  ```

**1.2 Migraciones**
```bash
php artisan make:migration create_strava_connections_table --no-interaction
php artisan make:migration add_strava_fields_to_workouts_table --no-interaction
```

**1.3 Modelo StravaConnection**
```bash
php artisan make:model StravaConnection --no-interaction
```
- Relación `belongsTo(User::class)`
- Método `isTokenExpired()` → compara `token_expires_at` con `now()`
- Campos cifrados: `access_token`, `refresh_token` → usar `encrypted` cast

**1.4 StravaController — OAuth**
```bash
php artisan make:controller StravaController --no-interaction
```

Métodos:
- `redirect()` → construye URL de autorización de Strava y redirige
- `callback(Request $request)` → intercambia code por tokens, guarda `StravaConnection`, dispara job de sync inicial
- `disconnect(Request $request)` → revoca token en Strava y elimina `StravaConnection`

URL de autorización de Strava:
```
https://www.strava.com/oauth/authorize
    ?client_id={CLIENT_ID}
    &redirect_uri={REDIRECT_URI}
    &response_type=code
    &scope=read,activity:read_all
```

**1.5 Rutas**
```php
Route::middleware('auth')->prefix('strava')->name('strava.')->group(function () {
    Route::get('/redirect',    [StravaController::class, 'redirect'])->name('redirect');
    Route::get('/callback',    [StravaController::class, 'callback'])->name('callback');
    Route::delete('/disconnect', [StravaController::class, 'disconnect'])->name('disconnect');
    Route::post('/sync',       [StravaController::class, 'sync'])->name('sync');
});
```

---

### FASE 2 — StravaService y Mapeo de Actividades

**2.1 StravaService**
```bash
php artisan make:class App/Services/StravaService --no-interaction
```

Responsabilidades:
- `getTokens(string $code): array` → POST a `/oauth/token` de Strava
- `refreshToken(StravaConnection $conn): StravaConnection` → renueva access_token
- `getActivities(StravaConnection $conn, int $after = null): array` → GET `/athlete/activities`
- `getActivityDetail(StravaConnection $conn, int $activityId): array` → GET `/activities/{id}`
- `mapActivityToWorkout(array $activity): array` → traduce campos Strava → campos Workout

**Mapeo de campos:**
| Campo Strava | Campo Workout | Notas |
|---|---|---|
| `id` | `strava_id` | Identificador único |
| `type` | `type` | Ver tabla de tipos abajo |
| `distance` | `distance` | metros → km (÷ 1000) |
| `moving_time` | `duration` | segundos |
| `start_date_local` | `date` | Solo fecha |
| `total_elevation_gain` | `elevation_gain` | metros |
| `average_heartrate` | `avg_heart_rate` | bpm |
| `average_speed` | `avg_pace` | m/s → seg/km (1000 ÷ speed) |
| `name` | `notes` | Nombre de actividad como nota |
| — | `source` | `'strava'` (fijo) |

**Mapeo de tipos:**
| Tipo Strava | Tipo MiEntreno |
|---|---|
| `Run` | `training_run` |
| `Race` | `race` |
| `Workout` | `intervals` |
| Otros | `training_run` |

> El usuario puede editar el tipo después de la sincronización.

**2.2 Refresh de token automático**

El access_token de Strava expira cada 6 horas. El servicio debe renovarlo antes de cada llamada:
```php
public function ensureValidToken(StravaConnection $conn): StravaConnection
{
    if ($conn->isTokenExpired()) {
        $conn = $this->refreshToken($conn);
    }
    return $conn;
}
```

---

### FASE 3 — Job de Sincronización

**3.1 SyncStravaActivities Job**
```bash
php artisan make:job SyncStravaActivities --no-interaction
```

Lógica:
1. Obtener `StravaConnection` del usuario
2. Llamar `StravaService::getActivities()` con `after = last_synced_at` (unix timestamp)
3. Para cada actividad:
   - Si ya existe `strava_id` en `workouts` → skip (evitar duplicados)
   - Si no existe → crear `Workout` con `source = 'strava'`
4. Actualizar `strava_connections.last_synced_at`

**Manejo de rate limits:** Strava permite 100 req/15min y 1000 req/día. El job debe respetar esto.

**3.2 Sincronización Manual**
- Botón "Sincronizar ahora" en Perfil → llama a `StravaController::sync()` que despacha el job

**3.3 Sincronización Automática (Cron)**
En `routes/console.php`:
```php
Schedule::job(new SyncStravaActivities($user))
    ->daily()
    ->when(fn() => StravaConnection::where('is_active', true)->exists());
```
O por usuario: iterar todos los usuarios con conexión activa y despachar un job por usuario.

---

### FASE 4 — UI

**4.1 Sección en Perfil**
En `resources/views/profile/edit.blade.php` (o donde viva el perfil), agregar card:

```
┌─────────────────────────────────────────┐
│  🟠 Strava                              │
│                                         │
│  [Conectado como "Juan Pérez"]          │
│  Última sync: hace 2 horas              │
│  Actividades importadas: 48             │
│                                         │
│  [🔄 Sincronizar ahora]  [Desconectar] │
└─────────────────────────────────────────┘
```

Si no conectado:
```
┌─────────────────────────────────────────┐
│  🟠 Conectar con Strava                 │
│                                         │
│  Importa tus actividades automáticamente│
│  desde Strava a MiEntreno.             │
│                                         │
│  [Conectar con Strava →]               │
└─────────────────────────────────────────┘
```

**4.2 Badge en workouts importados**
En la lista de workouts, mostrar badge "Strava" en workouts con `source = 'strava'`:
```blade
@if($workout->source === 'strava')
    <span class="badge-strava">⚡ Strava</span>
@endif
```

**4.3 Indicador en formulario de edición**
Si el workout viene de Strava, mostrar aviso: "Este entrenamiento fue importado desde Strava. Podés editar el tipo, dificultad y notas."

---

## Consideraciones Técnicas

### Seguridad
- Tokens cifrados en BD con `encrypted` cast de Laravel (usa `APP_KEY`)
- Nunca exponer tokens en vistas o logs
- Validar `state` parameter en OAuth callback para prevenir CSRF

### Idempotencia
- Usar `strava_id` como unique key para evitar duplicados
- `updateOrCreate(['strava_id' => $activity['id']], [...])` en el job

### Workouts editables post-sync
- Un workout de Strava es editable por el usuario (tipo, dificultad, notas)
- Solo los campos que Strava provee se sobreescriben en re-sync (si se implementa update)
- Recomendación: re-sync solo crea, no sobreescribe ediciones del usuario

### Rate Limiting Strava
- 100 requests cada 15 minutos
- 1000 requests por día
- Manejar respuesta `429 Too Many Requests` con retry/backoff

---

## Estimación

| Fase | Tarea | Estimación |
|---|---|---|
| 1 | OAuth + modelos + rutas + controller | ~3 horas |
| 2 | StravaService + mapeo de actividades | ~2 horas |
| 3 | Job de sync + cron | ~1.5 horas |
| 4 | UI en perfil + badges en workouts | ~1.5 horas |
| — | Testing + bugfixing | ~1 hora |
| **Total** | | **~9 horas** |

---

## Estado

**⏸️ Pendiente** — Planificado, no iniciado.

**Prioridad:** Alta (feature altamente solicitada, reduce fricción de carga manual).

**Prerequisito:** Tener credenciales de Strava API (Client ID + Secret).

---

**Documento creado:** 2026-03-27
**Última actualización:** 2026-03-27
