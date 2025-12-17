# Plan de Desarrollo MiEntreno 2025

**Fecha:** 2025-12-17
**Versión:** 1.0
**Objetivo:** Plan estratégico para completar funcionalidades core de multi-tenancy, roles y suscripciones

---

## 📊 Análisis de Estado Actual

### ✅ Funcionalidades Implementadas (100%)

1. **Sistema Base de Running**
   - ✅ Workouts CRUD completo con filtros y búsqueda
   - ✅ Races CRUD con gestión de carreras próximas y pasadas
   - ✅ Goals con 4 tipos (race, distance, pace, frequency)
   - ✅ Cálculo automático de progreso de objetivos
   - ✅ Sistema de reportes semanales/mensuales con PDF y links compartibles
   - ✅ Dashboard runner con métricas y estadísticas
   - ✅ Sistema de perfil de usuario con avatar y campos específicos

2. **Autenticación y Usuarios**
   - ✅ Register/Login sin prefijo de business
   - ✅ Sistema de invitaciones con tokens Base64
   - ✅ Campo `role` en users (runner/coach/admin)
   - ✅ Campo `business_id` nullable para usuarios individuales

3. **Infraestructura**
   - ✅ Components Blade reutilizables (card, metric-card, button)
   - ✅ MetricsService para cálculos
   - ✅ ReportService para generación de reportes
   - ✅ GoalProgressService para cálculo automático de progreso

---

## ❌ Gaps Identificados

### 1. Multi-tenancy y Rutas

**Problema Actual:**
- Todas las rutas son globales sin prefijo de business: `/dashboard`, `/workouts`, etc.
- No hay diferenciación de contexto entre usuarios con/sin business
- Arquitectura documenta rutas `/{business}/dashboard` pero NO están implementadas

**Gap:**
- ❌ No hay middleware para contexto de business
- ❌ No hay rutas diferenciadas por tipo de usuario
- ❌ No hay redirección inteligente post-login según contexto

---

### 2. Dashboards Diferenciados por Rol

**Problema Actual:**
- Un solo `DashboardController` para todos los usuarios
- Vista de dashboard idéntica para runner, coach y admin
- Coaches ven el dashboard de runner sin opciones específicas

**Gap:**
- ❌ No existe `CoachDashboardController`
- ❌ No existe vista `coach/dashboard.blade.php`
- ❌ No hay panel para ver alumnos
- ❌ No hay estadísticas de grupos de entrenamiento

---

### 3. Gestión de Businesses (para Coaches)

**Problema Actual:**
- Tabla `businesses` existe pero no hay CRUD
- No hay interface para que coaches creen su business
- No hay forma de gestionar días/horarios de entrenamientos

**Gap:**
- ❌ No existe `BusinessController`
- ❌ No hay vistas para crear/editar business
- ❌ No hay sistema para configurar horarios de grupos
- ❌ No hay gestión de miembros del business

---

### 4. Training Groups

**Problema Actual:**
- Tabla `training_groups` existe vacía
- No hay funcionalidad implementada
- No se pueden crear grupos dentro de un business

**Gap:**
- ❌ No existe `TrainingGroupController`
- ❌ No existe modelo `TrainingGroup` completo
- ❌ No hay vistas para gestión de grupos
- ❌ No hay sistema de asistencias

---

### 5. Sistema de Suscripciones

**Problema Actual:**
- **NO está documentado ni implementado**
- Es un requerimiento nuevo del usuario

**Gap Completo:**
- ❌ No hay tabla `subscriptions`
- ❌ No hay planes de suscripción
- ❌ No hay límites por plan
- ❌ No hay integración de pagos
- ❌ No hay middleware de validación de suscripción

---

## 🎯 Prioridades del Usuario

Basado en la conversación, el usuario prioriza:

### Prioridad 1: Sistema de Coaches (CRÍTICO)
1. Dashboard diferenciado para entrenadores
2. Creación/gestión de business por parte del coach
3. Configuración de días y horarios de entrenamientos
4. Sistema de invitación a alumnos mejorado
5. Vista de alumnos del coach

### Prioridad 2: Rutas Multi-tenant (ALTA)
1. Rutas con prefijo `/{business}/*` para usuarios con business
2. Rutas sin prefijo `/` para usuarios individuales
3. Middleware para contexto de business
4. Redirección inteligente post-login

### Prioridad 3: Suscripciones (MEDIA-ALTA)
1. Modelo de suscripción atado al coach (no al alumno)
2. Plan FREE: hasta 20 alumnos
3. Planes PAID: 100, 200 alumnos
4. Para usuarios individuales: suscripción por usuario
5. Limitaciones y validaciones según plan

---

## 📋 Plan de Desarrollo Propuesto

### SPRINT 1: Dashboard y Panel de Coach (Prioridad 1)
**Duración estimada:** 2-3 días
**Objetivo:** Diferenciar experiencia de coaches vs runners

#### Tareas:
1. **Crear CoachDashboardController**
   - Método `index()` con métricas de coach
   - Totalizadores de alumnos activos
   - Estadísticas de grupos
   - Alumnos destacados y rezagados

2. **Crear vista `coach/dashboard.blade.php`**
   - Métricas de grupos (total alumnos, asistencia promedio)
   - Lista de grupos activos
   - Resumen de entrenamientos grupales
   - Accesos rápidos a gestión

3. **Implementar redirección por rol en LoginController**
   ```php
   if ($user->role === 'coach' || $user->role === 'admin') {
       return redirect()->route('coach.dashboard');
   }
   return redirect()->route('dashboard');
   ```

4. **Actualizar sidebar para coaches**
   - Sección "Coaching" visible solo para coaches
   - Links a: Grupos, Alumnos, Business

**Entregable:** Coaches ven dashboard diferente con métricas relevantes

---

### SPRINT 2: Gestión de Business (Prioridad 1)
**Duración estimada:** 2-3 días
**Objetivo:** Coaches pueden crear y gestionar su business

#### Tareas:

1. **Crear BusinessController**
   - `index()` - Ver business del coach (si existe)
   - `create()` - Formulario creación
   - `store()` - Crear business
   - `edit()` - Editar business
   - `update()` - Actualizar business
   - Policy: Solo coaches pueden crear/editar su business

2. **Migración: agregar campos a `businesses`**
   ```php
   Schema::table('businesses', function (Blueprint $table) {
       $table->unsignedBigInteger('owner_id')->nullable()->after('id');
       $table->json('schedule')->nullable(); // Días y horarios
       $table->boolean('is_active')->default(true);

       $table->foreign('owner_id')->references('id')->on('users');
   });
   ```

3. **Crear vistas:**
   - `coach/business/create.blade.php` - Formulario crear business
   - `coach/business/edit.blade.php` - Editar business
   - `coach/business/show.blade.php` - Detalle del business

4. **Formulario de creación:**
   - Nombre del business
   - Slug (auto-generado)
   - Descripción
   - Configuración de horarios (JSON con días/horas)
   - Nivel target (principiante/intermedio/avanzado)

5. **Auto-asignación:**
   - Al crear business, asignar `business_id` al coach
   - Actualizar `businesses.owner_id` con el coach

**Entregable:** Coaches pueden crear y configurar su business desde la UI

---

### SPRINT 3: Sistema de Training Groups (Prioridad 1)
**Duración estimada:** 3-4 días
**Objetivo:** Gestión completa de grupos de entrenamiento

#### Tareas:

1. **Completar modelo TrainingGroup**
   ```php
   class TrainingGroup extends Model {
       protected $fillable = [
           'business_id', 'coach_id', 'name', 'description',
           'schedule', 'level', 'max_members', 'is_active'
       ];

       protected $casts = [
           'schedule' => 'array',
           'is_active' => 'boolean'
       ];

       // Relaciones
       public function business() { return $this->belongsTo(Business::class); }
       public function coach() { return $this->belongsTo(User::class, 'coach_id'); }
       public function members() {
           return $this->belongsToMany(User::class, 'training_group_user')
                       ->withTimestamps()
                       ->withPivot('joined_at', 'is_active');
       }
   }
   ```

2. **Crear TrainingGroupController**
   - CRUD completo
   - Gestión de miembros
   - Policy para coaches

3. **Migración pivot: `training_group_user`**
   ```php
   Schema::create('training_group_user', function (Blueprint $table) {
       $table->id();
       $table->foreignId('training_group_id')->constrained()->onDelete('cascade');
       $table->foreignId('user_id')->constrained()->onDelete('cascade');
       $table->timestamp('joined_at')->useCurrent();
       $table->boolean('is_active')->default(true);
       $table->timestamps();
   });
   ```

4. **Vistas:**
   - `coach/groups/index.blade.php` - Lista de grupos
   - `coach/groups/create.blade.php` - Crear grupo
   - `coach/groups/show.blade.php` - Detalle con miembros
   - `coach/groups/members/add.blade.php` - Agregar miembros

5. **Sistema de invitaciones a grupos:**
   - Generar tokens específicos por grupo
   - Invitar alumnos vía link o email
   - Auto-asignación a grupo al registrarse

**Entregable:** Coaches pueden crear grupos y gestionar miembros

---

### SPRINT 4: Rutas Multi-tenant (Prioridad 2)
**Duración estimada:** 3-4 días
**Objetivo:** Implementar rutas diferenciadas por contexto

#### Tareas:

1. **Crear middleware `SetBusinessContext`**
   ```php
   class SetBusinessContext {
       public function handle($request, Closure $next) {
           $business = null;

           // Si hay slug en la ruta
           if ($slug = $request->route('business')) {
               $business = Business::where('slug', $slug)->firstOrFail();
               View::share('currentBusiness', $business);
           }

           // Si usuario tiene business pero accede sin prefijo
           if (!$business && auth()->check() && auth()->user()->business_id) {
               $business = auth()->user()->business;
               View::share('currentBusiness', $business);
           }

           return $next($request);
       }
   }
   ```

2. **Crear rutas duales en `web.php`:**
   ```php
   // Rutas para usuarios individuales (sin business)
   Route::middleware(['auth', 'individual'])->group(function () {
       Route::get('/dashboard', [DashboardController::class, 'index']);
       Route::resource('workouts', WorkoutController::class);
       // ... resto de recursos
   });

   // Rutas para usuarios con business
   Route::prefix('{business}')->middleware(['auth', 'business'])->group(function () {
       Route::get('/dashboard', [DashboardController::class, 'index']);
       Route::resource('workouts', WorkoutController::class);
       // ... resto de recursos
   });

   // Rutas para coaches
   Route::prefix('{business}/coach')->middleware(['auth', 'coach'])->group(function () {
       Route::get('/dashboard', [CoachDashboardController::class, 'index']);
       Route::resource('groups', TrainingGroupController::class);
       Route::get('/students', [CoachController::class, 'students']);
       // ... resto de recursos coach
   });
   ```

3. **Middlewares de validación:**
   - `IndividualUser`: Solo usuarios sin business
   - `BusinessUser`: Solo usuarios con business válido
   - `CoachMiddleware`: Solo usuarios con rol coach/admin

4. **Actualizar LoginController:**
   ```php
   protected function redirectAfterLogin(User $user) {
       if ($user->role === 'coach' || $user->role === 'admin') {
           if ($user->business) {
               return redirect()->route('coach.dashboard', $user->business->slug);
           }
           return redirect()->route('coach.business.create'); // Si no tiene business
       }

       if ($user->business) {
           return redirect()->route('dashboard', $user->business->slug);
       }

       return redirect()->route('dashboard'); // Individual
   }
   ```

5. **Actualizar helpers y enlaces:**
   - Helper `businessRoute($name, $params = [])` para generar URLs
   - Actualizar todos los enlaces en blade para usar helper
   - Mantener compatibilidad con rutas actuales

**Entregable:** Sistema con rutas diferenciadas funcional

---

### SPRINT 5: Sistema de Suscripciones Base (Prioridad 3)
**Duración estimada:** 4-5 días
**Objetivo:** Implementar suscripciones y límites

#### Tareas:

1. **Diseño de base de datos:**

   **Tabla `subscription_plans`:**
   ```php
   Schema::create('subscription_plans', function (Blueprint $table) {
       $table->id();
       $table->string('name'); // "Free", "Basic", "Pro"
       $table->string('slug'); // "free", "basic", "pro"
       $table->integer('max_students'); // 20, 100, 200
       $table->decimal('price', 8, 2); // 0.00, 29.99, 59.99
       $table->string('currency', 3)->default('USD');
       $table->string('billing_period'); // "monthly", "yearly"
       $table->json('features')->nullable(); // Features del plan
       $table->boolean('is_active')->default(true);
       $table->timestamps();
   });
   ```

   **Tabla `subscriptions`:**
   ```php
   Schema::create('subscriptions', function (Blueprint $table) {
       $table->id();
       $table->foreignId('user_id')->constrained()->onDelete('cascade'); // El coach
       $table->foreignId('business_id')->nullable()->constrained()->onDelete('cascade');
       $table->foreignId('subscription_plan_id')->constrained();
       $table->string('status'); // "active", "cancelled", "expired", "trial"
       $table->timestamp('trial_ends_at')->nullable();
       $table->timestamp('ends_at')->nullable();
       $table->timestamps();
   });
   ```

2. **Modelos:**
   ```php
   class SubscriptionPlan extends Model {
       public function subscriptions() {
           return $this->hasMany(Subscription::class);
       }
   }

   class Subscription extends Model {
       public function user() { return $this->belongsTo(User::class); }
       public function business() { return $this->belongsTo(Business::class); }
       public function plan() { return $this->belongsTo(SubscriptionPlan::class); }

       public function isActive() {
           return $this->status === 'active' &&
                  (!$this->ends_at || $this->ends_at->isFuture());
       }

       public function canAddStudent() {
           $currentStudents = $this->business->users()->where('role', 'runner')->count();
           return $currentStudents < $this->plan->max_students;
       }
   }
   ```

3. **Seeder de planes:**
   ```php
   SubscriptionPlan::create([
       'name' => 'Free',
       'slug' => 'free',
       'max_students' => 20,
       'price' => 0,
       'billing_period' => 'monthly',
       'features' => ['20 alumnos', 'Grupos ilimitados', 'Reportes básicos']
   ]);

   SubscriptionPlan::create([
       'name' => 'Pro 100',
       'slug' => 'pro-100',
       'max_students' => 100,
       'price' => 29.99,
       'billing_period' => 'monthly',
       'features' => ['100 alumnos', 'Grupos ilimitados', 'Reportes avanzados', 'Soporte prioritario']
   ]);

   SubscriptionPlan::create([
       'name' => 'Pro 200',
       'slug' => 'pro-200',
       'max_students' => 200,
       'price' => 49.99,
       'billing_period' => 'monthly',
       'features' => ['200 alumnos', 'Grupos ilimitados', 'Reportes avanzados', 'Soporte prioritario', 'API access']
   ]);
   ```

4. **Middleware `CheckSubscription`:**
   ```php
   class CheckSubscription {
       public function handle($request, Closure $next) {
           $user = auth()->user();

           // Solo aplica a coaches con business
           if ($user->role === 'coach' && $user->business) {
               $subscription = $user->business->subscription;

               if (!$subscription || !$subscription->isActive()) {
                   return redirect()->route('subscription.expired');
               }
           }

           return $next($request);
       }
   }
   ```

5. **Validación al agregar alumnos:**
   ```php
   // En TrainingGroupController o BusinessController
   public function addStudent(Request $request) {
       $business = auth()->user()->business;
       $subscription = $business->subscription;

       if (!$subscription->canAddStudent()) {
           return back()->with('error',
               "Has alcanzado el límite de {$subscription->plan->max_students} alumnos.
                Actualiza tu plan para agregar más."
           );
       }

       // Proceder a agregar alumno
   }
   ```

6. **Vistas:**
   - `subscriptions/plans.blade.php` - Listado de planes
   - `subscriptions/subscribe.blade.php` - Formulario suscripción
   - `subscriptions/manage.blade.php` - Gestión de suscripción
   - `subscriptions/expired.blade.php` - Aviso de suscripción vencida

7. **Auto-asignación de plan Free:**
   ```php
   // En BusinessController@store
   $business = Business::create($data);

   // Asignar plan Free automáticamente
   $freePlan = SubscriptionPlan::where('slug', 'free')->first();
   Subscription::create([
       'user_id' => auth()->id(),
       'business_id' => $business->id,
       'subscription_plan_id' => $freePlan->id,
       'status' => 'active'
   ]);
   ```

**Entregable:** Sistema de suscripciones funcional con límites y validaciones

---

### SPRINT 6: Integración de Pagos (Opcional - Fase 2)
**Duración estimada:** 5-7 días
**Objetivo:** Procesar pagos para suscripciones pagas

#### Nota:
Este sprint se puede posponer para una fase posterior. El sistema funcionará con plan Free hasta implementar pagos.

#### Opciones de pasarela:
- Stripe (recomendado - Laravel Cashier)
- MercadoPago (para LATAM)
- PayPal

---

## 🗂️ Organización del Trabajo

### Estructura de Archivos Propuesta

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Coach/
│   │   │   ├── DashboardController.php
│   │   │   ├── GroupController.php (TrainingGroup)
│   │   │   ├── StudentController.php
│   │   │   └── BusinessController.php
│   │   ├── Subscription/
│   │   │   ├── SubscriptionController.php
│   │   │   └── PlanController.php
│   ├── Middleware/
│   │   ├── SetBusinessContext.php
│   │   ├── CoachMiddleware.php
│   │   ├── IndividualUser.php
│   │   ├── BusinessUser.php
│   │   └── CheckSubscription.php
│   ├── Requests/
│   │   ├── Business/
│   │   │   ├── CreateBusinessRequest.php
│   │   │   └── UpdateBusinessRequest.php
│   │   ├── TrainingGroup/
│   │   │   ├── CreateGroupRequest.php
│   │   │   └── UpdateGroupRequest.php
├── Models/
│   ├── TrainingGroup.php
│   ├── SubscriptionPlan.php
│   └── Subscription.php
├── Services/
│   ├── SubscriptionService.php
│   └── BusinessService.php
├── Policies/
│   ├── BusinessPolicy.php
│   ├── TrainingGroupPolicy.php
│   └── SubscriptionPolicy.php

resources/views/
├── coach/
│   ├── dashboard.blade.php
│   ├── business/
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   ├── groups/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── show.blade.php
│   │   └── members/
│   │       └── add.blade.php
│   └── students/
│       ├── index.blade.php
│       └── show.blade.php
├── subscriptions/
│   ├── plans.blade.php
│   ├── subscribe.blade.php
│   ├── manage.blade.php
│   └── expired.blade.php

database/migrations/
├── 2025_12_18_add_owner_to_businesses_table.php
├── 2025_12_18_create_training_group_user_table.php
├── 2025_12_18_create_subscription_plans_table.php
└── 2025_12_18_create_subscriptions_table.php
```

---

## 📅 Cronograma Estimado

| Sprint | Duración | Inicio | Fin Estimado |
|--------|----------|--------|--------------|
| Sprint 1: Dashboard Coach | 2-3 días | Inmediato | Día 3 |
| Sprint 2: Gestión Business | 2-3 días | Día 4 | Día 6 |
| Sprint 3: Training Groups | 3-4 días | Día 7 | Día 10 |
| Sprint 4: Rutas Multi-tenant | 3-4 días | Día 11 | Día 14 |
| Sprint 5: Suscripciones Base | 4-5 días | Día 15 | Día 19 |
| **TOTAL MVP** | **14-19 días** | - | **~3 semanas** |

**Nota:** Estimaciones asumen trabajo part-time (4-6 horas/día)

---

## 🎯 Entregables por Sprint

### Sprint 1 ✅
- Dashboard diferenciado para coaches
- Redirección por rol funcional
- Sidebar actualizado con sección Coaching

### Sprint 2 ✅
- CRUD de Business funcional
- Coaches pueden crear su business desde UI
- Configuración de horarios implementada

### Sprint 3 ✅
- Sistema completo de Training Groups
- Gestión de miembros por grupo
- Invitaciones a grupos funcionales

### Sprint 4 ✅
- Rutas con prefijo `/{business}/*` funcionando
- Rutas sin prefijo para individuales
- Middlewares de contexto implementados

### Sprint 5 ✅
- Planes de suscripción creados
- Límites por plan implementados
- Validaciones de suscripción activas
- Plan Free auto-asignado

---

## 🔄 Metodología de Trabajo

1. **Desarrollo incremental:** Completar cada sprint antes de avanzar
2. **Testing manual:** Probar cada feature antes de commit
3. **Documentación paralela:** Actualizar PROJECT_STATUS.md al final de cada sprint
4. **Commits descriptivos:** Formato: `feat(scope): descripción`
5. **Revisión de código:** Auto-review antes de commit

---

## 📝 Notas Importantes

### Compatibilidad Backward
- Mantener rutas actuales funcionando durante migración
- Implementar redirects 301 de rutas viejas a nuevas
- Fase de transición gradual para usuarios existentes

### Seguridad
- Validar ownership en todas las operaciones
- Policies para Business, TrainingGroup, Subscription
- Middleware de suscripción en rutas de coach

### Performance
- Eager loading en listados de alumnos
- Cache de métricas de dashboard coach (1 hora)
- Índices en nuevas tablas (business_id, coach_id)

---

## 🚀 Quick Start: Próximo Paso

**Comenzar con Sprint 1:**
```bash
# Crear CoachDashboardController
php artisan make:controller Coach/DashboardController

# Crear vista
touch resources/views/coach/dashboard.blade.php

# Actualizar rutas
# Editar routes/web.php

# Actualizar LoginController
# Editar app/Http/Controllers/Auth/v1/LoginController.php
```

---

**Última actualización:** 2025-12-17
**Autor:** Plan generado con Claude Code
