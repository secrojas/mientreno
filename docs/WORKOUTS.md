# Sistema de Workouts - MiEntreno

Documentación completa del sistema de gestión de entrenamientos.

---

## Descripción General

El sistema de Workouts permite a los corredores:
- Registrar entrenamientos con múltiples métricas
- Ver historial completo con paginación
- Editar entrenamientos pasados
- Visualizar métricas semanales en el dashboard
- Calcular automáticamente el pace

---

## Modelo de Datos

### Tabla: `workouts`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | ID único del entrenamiento |
| `user_id` | bigint | Usuario dueño del entrenamiento (FK) |
| `training_group_id` | bigint nullable | Grupo de entrenamiento (si aplica) |
| `race_id` | bigint nullable | Carrera asociada (si es oficial) |
| `date` | date | Fecha del entrenamiento |
| `type` | enum | Tipo: easy_run, intervals, tempo, long_run, recovery, race |
| `distance` | decimal(8,2) | Distancia en kilómetros |
| `duration` | integer | Duración en segundos |
| `avg_pace` | integer nullable | Pace promedio en segundos/km (auto-calculado) |
| `avg_heart_rate` | integer nullable | Frecuencia cardíaca promedio en bpm |
| `elevation_gain` | integer nullable | Desnivel positivo en metros |
| `difficulty` | tinyint | Dificultad percibida (1-5) |
| `notes` | text nullable | Notas del corredor |
| `weather` | json nullable | Condiciones climáticas (futuro) |
| `route` | json nullable | Ruta GPS (futuro) |
| `is_race` | boolean | Marca si fue una carrera oficial |
| `created_at` | timestamp | Fecha de creación del registro |
| `updated_at` | timestamp | Última actualización |

**Indices:**
- `workouts_user_id_date_index`: (user_id, date) - para queries de lista por usuario
- `workouts_type_index`: (type) - para filtrar por tipo
- Foreign key: `user_id` → `users.id` (cascade on delete)

---

## Tipos de Entrenamiento

```php
'easy_run'   => 'Rodaje suave',
'intervals'  => 'Series/Intervalos',
'tempo'      => 'Tempo run',
'long_run'   => 'Rodaje largo',
'recovery'   => 'Recuperación',
'race'       => 'Carrera',
```

---

## Modelo Eloquent

**Archivo:** `app/Models/Workout.php`

### Relaciones

```php
// Pertenece a un usuario
public function user()
{
    return $this->belongsTo(User::class);
}

// Puede pertenecer a un grupo de entrenamiento (nullable)
public function trainingGroup()
{
    return $this->belongsTo(TrainingGroup::class);
}

// Puede estar asociado a una carrera (nullable)
public function race()
{
    return $this->belongsTo(Race::class);
}
```

### Scopes

```php
// Workouts de esta semana
Workout::thisWeek()->get();

// Workouts de este mes
Workout::thisMonth()->get();

// Workouts de este año
Workout::thisYear()->get();

// Filtrar por tipo
Workout::byType('intervals')->get();

// Filtrar por usuario
Workout::forUser(auth()->id())->get();
```

### Helpers y Accessors

```php
// Calcular pace (método estático)
$pace = Workout::calculatePace($distance, $duration);
// Retorna: segundos por kilómetro (integer)

// Formatear pace (accessor)
$workout->formatted_pace; // "4:30/km"

// Formatear duración (accessor)
$workout->formatted_duration; // "1h 23m"

// Obtener label del tipo en español (accessor)
$workout->type_label; // "Rodaje suave"
```

---

## Controller

**Archivo:** `app/Http/Controllers/WorkoutController.php`

### Rutas Disponibles

| Método | URI | Acción | Descripción |
|--------|-----|--------|-------------|
| GET | `/workouts` | index | Lista paginada de workouts |
| GET | `/workouts/create` | create | Formulario de creación |
| POST | `/workouts` | store | Crear nuevo workout |
| GET | `/workouts/{id}` | show | Ver detalle (futuro) |
| GET | `/workouts/{id}/edit` | edit | Formulario de edición |
| PUT/PATCH | `/workouts/{id}` | update | Actualizar workout |
| DELETE | `/workouts/{id}` | destroy | Eliminar workout |

### Validaciones

```php
[
    'date' => 'required|date',
    'type' => 'required|in:easy_run,intervals,tempo,long_run,recovery,race',
    'distance' => 'required|numeric|min:0.1|max:999',
    'duration' => 'required|integer|min:1',
    'avg_heart_rate' => 'nullable|integer|min:40|max:250',
    'elevation_gain' => 'nullable|integer|min:0',
    'difficulty' => 'required|integer|min:1|max:5',
    'notes' => 'nullable|string|max:5000',
]
```

### Seguridad (Ownership Validation)

Todos los métodos verifican que el workout pertenezca al usuario autenticado:

```php
if ($workout->user_id !== Auth::id()) {
    abort(403, 'No autorizado');
}
```

---

## Vistas

### 1. Crear Workout (`workouts/create.blade.php`)

**Campos del formulario:**

- **Fecha**: `<input type="date">` (requerido)
- **Tipo**: `<select>` con 6 opciones (requerido)
- **Distancia**: `<input type="number" step="0.01">` en km (requerido)
- **Duración**: 3 inputs separados (horas, minutos, segundos)
  - JavaScript calcula el total en segundos automáticamente
  - Se envía como input hidden `duration`
- **FC Promedio**: `<input type="number">` en bpm (opcional)
- **Desnivel**: `<input type="number">` en metros (opcional)
- **Dificultad**: Selector visual 1-5 con radio buttons (requerido)
- **Notas**: `<textarea>` (opcional)

**JavaScript incluido:**
```javascript
// Calcular duración total
function updateDuration() {
    const hours = parseInt(document.getElementById('hours').value) || 0;
    const minutes = parseInt(document.getElementById('minutes').value) || 0;
    const seconds = parseInt(document.getElementById('seconds').value) || 0;
    const total = (hours * 3600) + (minutes * 60) + seconds;
    document.getElementById('duration').value = total;
}

// Actualizar en cada input de duración
document.getElementById('hours').addEventListener('input', updateDuration);
document.getElementById('minutes').addEventListener('input', updateDuration);
document.getElementById('seconds').addEventListener('input', updateDuration);
```

### 2. Lista de Workouts (`workouts/index.blade.php`)

**Características:**

- Header con botón "Nuevo Entreno"
- Mensaje flash de éxito (tras crear/editar/eliminar)
- Tabla responsive con columnas:
  - Fecha (dd/mm/YYYY)
  - Tipo
  - Distancia
  - Duración
  - Pace
  - Dificultad (badge)
  - Acciones (editar/eliminar)
- Paginación: 15 workouts por página
- Estado vacío con mensaje y botón CTA
- Responsive: colapsa a 1 columna en mobile

**Confirmación al eliminar:**
```html
<form onsubmit="return confirm('¿Eliminar este entrenamiento?');">
    @method('DELETE')
    <button type="submit">Eliminar</button>
</form>
```

### 3. Editar Workout (`workouts/edit.blade.php`)

Similar a create.blade.php pero:
- Pre-carga todos los valores existentes usando `old()` con fallback a `$workout`
- Breadcrumb "Volver" a la lista
- Header muestra fecha y tipo del workout
- Usa `@method('PUT')` para actualizar
- Botón "Actualizar Entrenamiento"

**Ejemplo pre-carga de duración:**
```php
value="{{ old('hours', floor($workout->duration / 3600)) }}"
value="{{ old('minutes', floor(($workout->duration % 3600) / 60)) }}"
value="{{ old('seconds', $workout->duration % 60) }}"
```

---

## Dashboard Integration

### DashboardController

```php
public function index()
{
    $user = Auth::user();

    // Workouts de esta semana
    $thisWeekWorkouts = $user->workouts()->thisWeek()->get();

    // Métricas de la semana
    $weekStats = [
        'total_distance' => $thisWeekWorkouts->sum('distance'),
        'total_duration' => $thisWeekWorkouts->sum('duration'),
        'total_workouts' => $thisWeekWorkouts->count(),
        'avg_pace' => $thisWeekWorkouts->avg('avg_pace'),
    ];

    // Últimos 5 entrenamientos
    $recentWorkouts = $user->workouts()
        ->orderBy('date', 'desc')
        ->limit(5)
        ->get();

    return view('dashboard', compact('weekStats', 'recentWorkouts'));
}
```

### Métricas Mostradas

**1. Km esta semana:**
```php
{{ number_format($weekStats['total_distance'], 1) }}
```

**2. Tiempo total:**
```php
@php
    $hours = floor($weekStats['total_duration'] / 3600);
    $minutes = floor(($weekStats['total_duration'] % 3600) / 60);
@endphp
{{ $hours > 0 ? $hours . 'h ' : '' }}{{ $minutes }}m
```

**3. Pace medio:**
```php
@if($weekStats['avg_pace'])
    @php
        $avgMinutes = floor($weekStats['avg_pace'] / 60);
        $avgSeconds = $weekStats['avg_pace'] % 60;
    @endphp
    {{ $avgMinutes }}:{{ str_pad($avgSeconds, 2, '0', STR_PAD_LEFT) }}
@else
    –
@endif
```

**4. Número de sesiones:**
```php
{{ $weekStats['total_workouts'] }} {{ $weekStats['total_workouts'] === 1 ? 'sesión' : 'sesiones' }}
```

### Panel de Entrenamientos Recientes

```blade
@if($recentWorkouts->count() > 0)
    @foreach($recentWorkouts as $workout)
        <a href="{{ route('workouts.edit', $workout) }}">
            <div>{{ $workout->date->format('d/m') }}</div>
            <div>{{ $workout->type_label }}</div>
            <div>{{ $workout->distance }} km</div>
            <div>{{ $workout->formatted_pace }}</div>
        </a>
    @endforeach
@else
    <div>No hay entrenamientos cargados todavía.</div>
    <a href="{{ route('workouts.create') }}">Crear primer entreno</a>
@endif
```

---

## Seeder

**Archivo:** `database/seeders/WorkoutSeeder.php`

Crea 13 workouts de ejemplo distribuidos en 4 semanas:

- **Semana 4**: 3 workouts (easy run 8.5km, intervals 10km, long run 18km)
- **Semana 3**: 4 workouts (recovery 6km, tempo 12km, easy run 9km, long run 21km)
- **Semana 2**: 3 workouts (intervals 11km, easy run 7.5km, long run 16km)
- **Semana 1**: 3 workouts (recovery 5km, tempo 10.5km, easy run 8km)

**Total:** 142.5 km en 11h 55min

Para ejecutar:
```bash
php artisan db:seed --class=WorkoutSeeder
```

O con fresh migrations:
```bash
php artisan migrate:fresh --seed
```

---

## Uso

### 1. Crear un Workout desde la UI

```
1. Login en /login
2. Click en "Nuevo Entreno" (dashboard o sidebar)
3. Completar formulario:
   - Fecha: 11/12/2025
   - Tipo: Tempo run
   - Distancia: 10.5 km
   - Duración: 0h 48m 0s
   - FC Promedio: 165 bpm (opcional)
   - Desnivel: 60m (opcional)
   - Dificultad: 4
   - Notas: "Tempo run controlado, buen ritmo"
4. Click "Crear Entrenamiento"
5. Redirección a /workouts con mensaje de éxito
```

**Resultado:**
- Pace calculado automáticamente: 4:34/km
- Workout guardado en BD
- Visible en dashboard y lista

### 2. Ver Métricas en Dashboard

```
1. Acceder a /dashboard
2. Ver card "Km esta semana" → suma de workouts de la semana
3. Ver card "Tiempo total" → duración total en h/m
4. Ver card "Pace medio" → promedio de todos los paces de la semana
5. Ver panel "Entrenamientos recientes" → últimos 5 workouts
```

### 3. Editar un Workout

```
1. Click en "Editar" desde lista o dashboard
2. Formulario pre-cargado con datos existentes
3. Modificar campos necesarios
4. Click "Actualizar Entrenamiento"
5. Pace recalculado automáticamente si cambió distancia o duración
```

### 4. Eliminar un Workout

```
1. Click en botón "Eliminar" (icono basura) desde lista
2. Confirmar en dialog JavaScript
3. Workout eliminado y redirigido a lista con mensaje
```

---

## Cálculos y Lógica de Negocio

### Cálculo de Pace

**Fórmula:**
```
Pace (seg/km) = Duration (segundos) / Distance (km)
```

**Implementación:**
```php
public static function calculatePace(float $distance, int $duration): ?int
{
    if ($distance <= 0) return null;
    return (int) round($duration / $distance);
}
```

**Ejemplo:**
```
Distance: 10.5 km
Duration: 2880 segundos (48 minutos)
Pace = 2880 / 10.5 = 274.28 ≈ 274 seg/km = 4:34/km
```

### Formato de Pace

```php
public function getFormattedPaceAttribute(): string
{
    if (!$this->avg_pace) return '–';
    $minutes = floor($this->avg_pace / 60);
    $seconds = $this->avg_pace % 60;
    return sprintf("%d:%02d/km", $minutes, $seconds);
}
```

**Ejemplo:**
```
avg_pace = 274 segundos
minutes = 274 / 60 = 4
seconds = 274 % 60 = 34
Resultado: "4:34/km"
```

### Formato de Duración

```php
public function getFormattedDurationAttribute(): string
{
    $hours = floor($this->duration / 3600);
    $minutes = floor(($this->duration % 3600) / 60);

    if ($hours > 0) {
        return "{$hours}h {$minutes}m";
    }
    return "{$minutes}m";
}
```

**Ejemplo:**
```
duration = 2880 segundos
hours = 2880 / 3600 = 0
minutes = (2880 % 3600) / 60 = 48
Resultado: "48m"

duration = 6300 segundos
hours = 6300 / 3600 = 1
minutes = (6300 % 3600) / 60 = 45
Resultado: "1h 45m"
```

---

## Próximas Mejoras

### Corto Plazo
- [ ] Agregar validación de fechas futuras (no permitir workouts futuros)
- [ ] Implementar búsqueda y filtros en lista (por tipo, rango de fechas)
- [ ] Vista de detalle (`workouts/show.blade.php`)
- [ ] Crear components Blade reutilizables (`<x-metric-card>`, etc.)

### Mediano Plazo
- [ ] Exportar workouts a CSV/PDF
- [ ] Gráficos de evolución (Chart.js)
- [ ] Comparar con semana/mes anterior
- [ ] Racha de entrenamientos (días consecutivos)
- [ ] Calcular VO2max estimado

### Largo Plazo
- [ ] Integración con Strava
- [ ] Importar archivos GPX/TCX
- [ ] Weather API para auto-completar clima
- [ ] Sugerencias de entrenamientos basadas en historial

---

## FAQ

### ¿Cómo se calcula el pace automáticamente?

El pace se calcula en el backend usando la fórmula `duration / distance`. El usuario no puede ingresarlo manualmente para evitar inconsistencias.

### ¿Por qué la duración se guarda en segundos?

Para facilitar cálculos y agregaciones. En el frontend se muestra en formato legible (H:M:S o Xh Ym).

### ¿Puedo ver workouts de otros usuarios?

No. El sistema valida ownership en todos los métodos del controller. Solo puedes ver/editar/eliminar tus propios workouts.

### ¿Qué pasa si elimino un workout?

Se elimina permanentemente de la BD (cascade delete). No hay papelera o recuperación.

### ¿Los workouts pueden estar asociados a carreras?

Sí, hay un campo `race_id` nullable. En Fase 2 se implementará la funcionalidad completa de vincular workouts a races.

---

**Última actualización:** 2025-12-11
