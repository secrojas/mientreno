# Guía de Desarrollo - MiEntreno

Guía rápida para desarrolladores trabajando en el proyecto.

---

## Setup Inicial

```bash
# 1. Clonar y entrar al directorio
cd C:\laragon\www\mientreno

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
DB_DATABASE=mientreno
DB_USERNAME=root
DB_PASSWORD=

# 5. Crear base de datos en Laragon/MySQL
# (Usar HeidiSQL o línea de comandos)

# 6. Ejecutar migraciones
php artisan migrate

# 7. (Opcional) Seeders con datos de prueba
php artisan db:seed

# 8. Correr servidor de desarrollo
php artisan serve
# Visitar: http://localhost:8000
```

---

## Comandos Comunes

### Desarrollo diario

```bash
# Iniciar servidor
php artisan serve

# Ver rutas
php artisan route:list

# Ejecutar tests
php artisan test
php artisan test --filter WorkoutTest

# Limpiar caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Limpiar TODO (útil cuando algo no funciona)
php artisan optimize:clear
```

### Base de datos

```bash
# Crear migración
php artisan make:migration create_workouts_table

# Ejecutar migraciones
php artisan migrate

# Rollback última migración
php artisan migrate:rollback

# Rollback todo y re-migrar
php artisan migrate:fresh

# Re-migrar + seeders
php artisan migrate:fresh --seed

# Crear seeder
php artisan make:seeder WorkoutSeeder

# Ejecutar seeder específico
php artisan db:seed --class=WorkoutSeeder
```

### Generar código

```bash
# Crear modelo + migración + factory + seeder + controller + policy
php artisan make:model Workout -mfsc --policy

# Crear solo modelo
php artisan make:model Workout

# Crear controller con recursos
php artisan make:controller WorkoutController --resource

# Crear Form Request
php artisan make:request StoreWorkoutRequest

# Crear Policy
php artisan make:policy WorkoutPolicy --model=Workout

# Crear Factory
php artisan make:factory WorkoutFactory

# Crear Test
php artisan make:test WorkoutTest
php artisan make:test WorkoutTest --unit

# Crear Component Blade
php artisan make:component Card
```

### Tinker (REPL)

```bash
# Iniciar tinker (PHP interactivo)
php artisan tinker

# Ejemplos en tinker:
User::count()
$user = User::first()
$user->workouts
Workout::factory()->count(10)->create(['user_id' => 1])
```

---

## Estructura de Desarrollo

### Crear nueva feature: Ejemplo "Workouts"

#### 1. Migración

```bash
php artisan make:migration create_workouts_table
```

```php
// database/migrations/xxxx_create_workouts_table.php
Schema::create('workouts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->date('date');
    $table->string('type');
    $table->decimal('distance', 8, 2);
    $table->integer('duration');
    $table->integer('avg_pace');
    $table->integer('difficulty');
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->index(['user_id', 'date']);
});
```

```bash
php artisan migrate
```

#### 2. Modelo

```bash
php artisan make:model Workout
```

```php
// app/Models/Workout.php
protected $fillable = [
    'user_id', 'date', 'type', 'distance',
    'duration', 'avg_pace', 'difficulty', 'notes',
];

protected $casts = [
    'date' => 'date',
    'distance' => 'decimal:2',
];

// Relaciones
public function user()
{
    return $this->belongsTo(User::class);
}

// Scopes
public function scopeThisWeek($query)
{
    return $query->whereBetween('date', [
        now()->startOfWeek(),
        now()->endOfWeek(),
    ]);
}
```

#### 3. Factory

```bash
php artisan make:factory WorkoutFactory
```

```php
// database/factories/WorkoutFactory.php
public function definition(): array
{
    return [
        'user_id' => User::factory(),
        'date' => $this->faker->dateTimeBetween('-30 days', 'now'),
        'type' => 'easy_run',
        'distance' => $this->faker->randomFloat(2, 5, 20),
        'duration' => 3000,
        'avg_pace' => 300,
        'difficulty' => $this->faker->numberBetween(1, 5),
    ];
}
```

#### 4. Seeder

```bash
php artisan make:seeder WorkoutSeeder
```

```php
// database/seeders/WorkoutSeeder.php
public function run(): void
{
    $user = User::first();
    Workout::factory()->count(20)->create([
        'user_id' => $user->id,
    ]);
}
```

#### 5. Controller

```bash
php artisan make:controller WorkoutController --resource
```

```php
// app/Http/Controllers/WorkoutController.php
public function index()
{
    $workouts = auth()->user()
        ->workouts()
        ->latest('date')
        ->paginate(25);

    return view('workouts.index', compact('workouts'));
}

public function store(StoreWorkoutRequest $request)
{
    $workout = auth()->user()->workouts()->create(
        $request->validated()
    );

    return redirect()
        ->route('workouts.show', $workout)
        ->with('success', 'Entrenamiento creado exitosamente');
}
```

#### 6. Form Request

```bash
php artisan make:request StoreWorkoutRequest
```

```php
// app/Http/Requests/StoreWorkoutRequest.php
public function authorize(): bool
{
    return true;
}

public function rules(): array
{
    return [
        'date' => 'required|date|before_or_equal:today',
        'type' => 'required|string',
        'distance' => 'required|numeric|min:0.1',
        'duration' => 'required|integer|min:60',
        'difficulty' => 'required|integer|min:1|max:5',
        'notes' => 'nullable|string|max:1000',
    ];
}
```

#### 7. Policy

```bash
php artisan make:policy WorkoutPolicy --model=Workout
```

```php
// app/Policies/WorkoutPolicy.php
public function update(User $user, Workout $workout): bool
{
    return $user->id === $workout->user_id;
}
```

#### 8. Rutas

```php
// routes/web.php
Route::middleware('auth')->group(function () {
    Route::resource('workouts', WorkoutController::class);
});
```

#### 9. Vistas

```bash
mkdir resources/views/workouts
```

Crear:
- `resources/views/workouts/index.blade.php`
- `resources/views/workouts/create.blade.php`
- `resources/views/workouts/show.blade.php`
- `resources/views/workouts/edit.blade.php`

#### 10. Tests

```bash
php artisan make:test WorkoutTest
```

```php
// tests/Feature/WorkoutTest.php
/** @test */
public function user_can_create_workout()
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('workouts.store'), [
        'date' => '2025-11-18',
        'type' => 'easy_run',
        'distance' => 10,
        'duration' => 3000,
        'difficulty' => 3,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('workouts', [
        'user_id' => $user->id,
    ]);
}
```

---

## Debugging

### Laravel Debugbar

```bash
composer require barryvdh/laravel-debugbar --dev
```

### Log de queries

```php
// En AppServiceProvider boot()
\DB::listen(function ($query) {
    \Log::info($query->sql, $query->bindings);
});
```

### Dump & Die

```php
dd($variable);  // Dump and die
dump($variable);  // Solo dump, continúa ejecución
```

---

## Git Workflow

### Commits

```bash
# Ver estado
git status

# Agregar archivos
git add .
git add docs/

# Commit
git commit -m "feat(workouts): agregar CRUD de entrenamientos"

# Push
git push origin main
```

### Convención de commits

```
feat(scope): descripción corta
fix(scope): descripción corta
docs: descripción corta
refactor(scope): descripción corta
test(scope): descripción corta
chore: descripción corta
```

Ejemplos:
```
feat(workouts): agregar formulario de creación
fix(metrics): corregir cálculo de pace semanal
docs: actualizar session log sesión 02
refactor(dashboard): extraer métricas a service
test(workouts): agregar test de ownership
chore: actualizar dependencias
```

---

## Troubleshooting

### Error: Class not found

```bash
composer dump-autoload
```

### Migrations out of sync

```bash
php artisan migrate:fresh --seed
```

### Cache issues

```bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Permission issues (Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
```

---

## Recursos Útiles

### Documentación del proyecto
- `docs/README.md` - Índice de documentación
- `docs/PROJECT_STATUS.md` - Estado actual
- `docs/ARCHITECTURE.md` - Arquitectura completa
- `docs/ROADMAP.md` - Plan de desarrollo
- `docs/SESSION_LOG.md` - Log de sesiones
- `docs/RECOMENDACIONES.md` - Mejores prácticas

### Laravel
- [Laravel Docs](https://laravel.com/docs)
- [Laracasts](https://laracasts.com)

### Testing
- `php artisan test` - Ejecutar tests
- `php artisan test --filter=WorkoutTest` - Test específico
- `php artisan test --coverage` - Coverage report

---

**Última actualización**: 2025-11-18
