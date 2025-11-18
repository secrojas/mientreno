# MiEntreno - Recomendaciones y Mejores Prácticas

Guía de recomendaciones técnicas y decisiones de arquitectura para el desarrollo del proyecto.

---

## Arquitectura y Diseño

### 1. Separación de Conceptos: Business vs TrainingGroup

**Importante**: No confundir estos dos conceptos.

- **Business**: Organización/Empresa que usa la plataforma
  - Ejemplo: "Running Team Buenos Aires", "Club de Corredores Rosario"
  - Un business puede tener múltiples grupos de entrenamiento
  - Define el tenant en multi-tenancy
  - URL: `/{business_slug}/...`

- **TrainingGroup**: Grupo de entrenamiento específico dentro de un business
  - Ejemplo: "Grupo Principiantes Lunes 19hs", "Grupo 10K Miércoles"
  - Un usuario puede pertenecer a múltiples training groups
  - Tiene un coach asignado
  - Tiene horarios y nivel específico

### 2. Usuarios Individuales

**Recomendación**: Permitir `business_id = null` para usuarios que usan la app individualmente.

**Ventajas**:
- Mayor flexibilidad
- Corredor puede usar la app antes de unirse a un grupo
- Puede migrar a un business después

**Implementación**:
```php
// Middleware para detectar contexto
if ($user->business_id) {
    // Redirigir a /{business}/dashboard
} else {
    // Redirigir a /dashboard (individual)
}
```

### 3. Cálculo de Pace

**Formato**: Pace se mide en minutos:segundos por kilómetro (min/km)

```php
// Almacenar en BD
'avg_pace' => 312  // 312 segundos/km = 5:12 min/km

// Calcular desde workout
$avgPace = $workout->duration / $workout->distance;  // seg/km

// Formatear para display
public function getFormattedPaceAttribute(): string
{
    $minutes = floor($this->avg_pace / 60);
    $seconds = $this->avg_pace % 60;
    return sprintf("%d:%02d", $minutes, $seconds);
}
```

**Validación razonable**:
- Pace mínimo: ~2:30 min/km (récord mundial de maratón)
- Pace máximo: ~10:00 min/km (caminata rápida)
- Para validación, aceptar 2:00 - 12:00 min/km

### 4. Tipos de Entrenamientos

**Recomendación**: Usar constantes en el modelo.

```php
// Workout.php
class Workout extends Model
{
    const TYPE_EASY_RUN = 'easy_run';      // Fondo suave
    const TYPE_INTERVALS = 'intervals';    // Pasadas/series
    const TYPE_TEMPO = 'tempo';            // Ritmo sostenido
    const TYPE_LONG_RUN = 'long_run';      // Tirada larga
    const TYPE_RECOVERY = 'recovery';      // Recuperación
    const TYPE_RACE = 'race';              // Competencia

    public static function getTypes(): array
    {
        return [
            self::TYPE_EASY_RUN => 'Fondo suave',
            self::TYPE_INTERVALS => 'Series/Pasadas',
            self::TYPE_TEMPO => 'Tempo run',
            self::TYPE_LONG_RUN => 'Tirada larga',
            self::TYPE_RECOVERY => 'Recuperación',
            self::TYPE_RACE => 'Carrera',
        ];
    }
}
```

### 5. Métricas Semanales/Mensuales

**Recomendación**: Usar scopes de Eloquent + Carbon.

```php
// Workout.php
public function scopeThisWeek($query)
{
    return $query->whereBetween('date', [
        now()->startOfWeek(),
        now()->endOfWeek(),
    ]);
}

public function scopeThisMonth($query)
{
    return $query->whereBetween('date', [
        now()->startOfMonth(),
        now()->endOfMonth(),
    ]);
}

// Uso
$kmThisWeek = $user->workouts()
    ->thisWeek()
    ->sum('distance');

$avgPaceThisWeek = $user->workouts()
    ->thisWeek()
    ->avg('avg_pace');
```

**Performance**: Cachear estos cálculos por 1 hora.

```php
$kmThisWeek = Cache::remember(
    "user.{$user->id}.km_this_week",
    now()->addHour(),
    fn() => $user->workouts()->thisWeek()->sum('distance')
);
```

---

## Base de Datos

### 1. Indexes Críticos

```php
// En migraciones
$table->index('user_id');
$table->index('date');
$table->index(['user_id', 'date']);  // Composite para queries frecuentes
$table->index('business_id');
$table->index('training_group_id');
```

### 2. JSON Fields

**Recomendación**: Usar JSON para datos flexibles, pero no abusar.

**Bien**:
```php
'settings' => [
    'theme' => 'dark',
    'notifications_enabled' => true,
]

'weather' => [
    'temp' => 18,
    'conditions' => 'sunny',
    'humidity' => 60,
]
```

**Mal** (mejor usar columnas):
```php
// NO hacer esto
'workout_data' => [
    'distance' => 10,   // Mejor como columna normal
    'duration' => 3600,
    'type' => 'easy_run',
]
```

### 3. Soft Deletes

**Recomendación**: Usar soft deletes en entidades principales.

```php
// En migraciones
$table->softDeletes();

// En modelos
use SoftDeletes;
```

Aplicar a:
- Workouts (para poder recuperar si borra por error)
- Races
- Goals
- TrainingGroups

No aplicar a:
- Attendances (no tiene sentido recuperarlas)
- Relaciones pivot

---

## Seguridad

### 1. Policies en Todo

**Regla**: NUNCA confiar en el frontend. Siempre validar ownership en backend.

```php
// WorkoutPolicy.php
public function update(User $user, Workout $workout): bool
{
    return $user->id === $workout->user_id;
}

// Controller
public function update(UpdateWorkoutRequest $request, Workout $workout)
{
    $this->authorize('update', $workout);
    // ...
}
```

### 2. Multi-tenancy Isolation

**Crítico**: Asegurar que un usuario de business A no vea datos de business B.

```php
// En controllers
$workouts = Workout::where('user_id', $request->user()->id)
    ->whereHas('user', function($q) use ($business) {
        $q->where('business_id', $business->id);
    })
    ->get();

// O mejor, crear scope global
// Workout.php
protected static function booted()
{
    static::addGlobalScope('business', function ($query) {
        if (auth()->check() && auth()->user()->business_id) {
            $query->whereHas('user', function($q) {
                $q->where('business_id', auth()->user()->business_id);
            });
        }
    });
}
```

### 3. Validación de Inputs

**Form Requests** para TODO.

```php
// StoreWorkoutRequest.php
public function rules(): array
{
    return [
        'date' => 'required|date|before_or_equal:today',
        'type' => 'required|in:easy_run,intervals,tempo,long_run,recovery,race',
        'distance' => 'required|numeric|min:0.1|max:200',  // 200km max razonable
        'duration' => 'required|integer|min:60|max:86400',  // 1min - 24h
        'difficulty' => 'required|integer|min:1|max:5',
        'notes' => 'nullable|string|max:1000',
    ];
}

public function messages(): array
{
    return [
        'date.before_or_equal' => 'No puedes registrar entrenamientos futuros.',
        'distance.max' => 'La distancia máxima permitida es 200km.',
    ];
}
```

---

## Frontend

### 1. Components Blade Reutilizables

**Crear desde el inicio** para mantener consistencia.

```blade
<!-- resources/views/components/card.blade.php -->
@props(['title' => null, 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($title)
        <div class="card-header">
            <div class="card-title">{{ $title }}</div>
            @if($subtitle)
                <div class="card-subtitle">{{ $subtitle }}</div>
            @endif
        </div>
    @endif

    <div class="card-body">
        {{ $slot }}
    </div>
</div>

<!-- Uso -->
<x-card title="Entrenamientos recientes" subtitle="Esta semana">
    <!-- Contenido -->
</x-card>
```

### 2. Formateo de Datos

**Helpers globales** para formateo consistente.

```php
// app/helpers.php (crear y agregar a composer.json autoload.files)

if (!function_exists('format_pace')) {
    function format_pace(int $seconds): string
    {
        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;
        return sprintf("%d:%02d", $minutes, $secs);
    }
}

if (!function_exists('format_duration')) {
    function format_duration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf("%dh %02dm %02ds", $hours, $minutes, $secs);
        }
        return sprintf("%dm %02ds", $minutes, $secs);
    }
}

// Uso en Blade
{{ format_pace($workout->avg_pace) }}  // "5:12"
{{ format_duration($workout->duration) }}  // "1h 05m 30s"
```

### 3. Estados Vacíos

**Siempre mostrar empty states** informativos.

```blade
@forelse($workouts as $workout)
    <!-- Mostrar workout -->
@empty
    <div class="empty-state">
        <svg><!-- Ícono --></svg>
        <h3>No hay entrenamientos registrados</h3>
        <p>Empieza registrando tu primer entrenamiento.</p>
        <a href="{{ route('workouts.create') }}" class="btn-primary">
            Crear entrenamiento
        </a>
    </div>
@endforelse
```

---

## Testing

### 1. Estructura de Tests

```php
// tests/Feature/WorkoutTest.php
class WorkoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_create_workout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('workouts.store'), [
            'date' => '2025-11-18',
            'type' => 'easy_run',
            'distance' => 10,
            'duration' => 3000,  // 50 minutos
            'difficulty' => 3,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('workouts', [
            'user_id' => $user->id,
            'distance' => 10,
        ]);
    }

    /** @test */
    public function user_cannot_view_other_users_workout()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $workout = Workout::factory()->for($user1)->create();

        $response = $this->actingAs($user2)
            ->get(route('workouts.show', $workout));

        $response->assertForbidden();
    }
}
```

### 2. Factories Completas

```php
// database/factories/WorkoutFactory.php
public function definition(): array
{
    return [
        'user_id' => User::factory(),
        'date' => $this->faker->dateTimeBetween('-30 days', 'now'),
        'type' => $this->faker->randomElement([
            Workout::TYPE_EASY_RUN,
            Workout::TYPE_INTERVALS,
            Workout::TYPE_LONG_RUN,
        ]),
        'distance' => $this->faker->randomFloat(2, 5, 25),  // 5-25 km
        'duration' => function (array $attributes) {
            // Calcular duración realista basada en distancia
            $paceSeconds = $this->faker->numberBetween(240, 360);  // 4:00 - 6:00 min/km
            return (int)($attributes['distance'] * $paceSeconds);
        },
        'avg_pace' => $this->faker->numberBetween(240, 360),
        'difficulty' => $this->faker->numberBetween(1, 5),
        'notes' => $this->faker->optional()->sentence(),
    ];
}
```

---

## Performance

### 1. Eager Loading

**Siempre** usar `with()` para relaciones que se van a usar.

```php
// Mal (N+1 problem)
$workouts = Workout::where('user_id', $userId)->get();
foreach ($workouts as $workout) {
    echo $workout->user->name;  // Query por cada workout
}

// Bien
$workouts = Workout::with('user')
    ->where('user_id', $userId)
    ->get();
foreach ($workouts as $workout) {
    echo $workout->user->name;  // Sin queries adicionales
}
```

### 2. Paginación

**Siempre** paginar listas largas.

```php
// Controller
$workouts = $user->workouts()
    ->latest('date')
    ->paginate(25);

// View
{{ $workouts->links() }}
```

### 3. Caching de Métricas

```php
// MetricsService.php
public function getUserWeeklyMetrics(User $user): array
{
    return Cache::remember(
        "metrics.user.{$user->id}.weekly",
        now()->addHour(),
        function() use ($user) {
            return [
                'total_km' => $user->workouts()->thisWeek()->sum('distance'),
                'total_time' => $user->workouts()->thisWeek()->sum('duration'),
                'avg_pace' => $user->workouts()->thisWeek()->avg('avg_pace'),
                'sessions' => $user->workouts()->thisWeek()->count(),
            ];
        }
    );
}

// Invalidar cache al crear workout
protected static function booted()
{
    static::created(function ($workout) {
        Cache::forget("metrics.user.{$workout->user_id}.weekly");
        Cache::forget("metrics.user.{$workout->user_id}.monthly");
    });
}
```

---

## Integración Continua

### 1. GitHub Actions (Recomendado)

```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test
```

### 2. Pre-commit Hooks

```bash
# .git/hooks/pre-commit (hacer ejecutable)
#!/bin/sh
php artisan test
```

---

## Deployment

### 1. Checklist Pre-deploy

- [ ] Todos los tests pasan
- [ ] `.env` production configurado (no commitear!)
- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Database backups configurados
- [ ] HTTPS habilitado
- [ ] Cache de configuración: `php artisan config:cache`
- [ ] Cache de rutas: `php artisan route:cache`
- [ ] Optimizar autoloader: `composer install --optimize-autoloader --no-dev`

### 2. Monitoreo

**Recomendado**: Laravel Telescope (development) + Sentry (production)

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate

composer require sentry/sentry-laravel
```

---

**Última actualización**: 2025-11-18
