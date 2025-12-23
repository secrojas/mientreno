# Sistema de Suscripciones - MiEntreno

**Versión**: 1.0
**Fecha**: 2025-12-23
**Sprint**: 5

---

## Tabla de Contenidos

1. [Overview](#overview)
2. [Arquitectura](#arquitectura)
3. [Planes de Suscripción](#planes-de-suscripción)
4. [Modelos](#modelos)
5. [Flujos de Usuario](#flujos-de-usuario)
6. [Validaciones y Límites](#validaciones-y-límites)
7. [API de Métodos](#api-de-métodos)
8. [Ejemplos de Uso](#ejemplos-de-uso)
9. [Integración en Controllers](#integración-en-controllers)
10. [Vistas y UI](#vistas-y-ui)
11. [Testing](#testing)
12. [Próximos Pasos](#próximos-pasos)

---

## Overview

### Propósito

El sistema de suscripciones permite monetizar la plataforma estableciendo límites de capacidad por plan. Cada **Business** (negocio de coaching) puede tener una suscripción activa que determina cuántos estudiantes, grupos y almacenamiento puede utilizar.

### Características Principales

- ✅ **4 planes predefinidos**: Free, Starter, Pro, Enterprise
- ✅ **Límites configurables**: Estudiantes, grupos, almacenamiento
- ✅ **Validaciones automáticas**: Verifica límites al crear recursos
- ✅ **Ciclo de vida completo**: Activar, cancelar, renovar, expirar
- ✅ **Panel de gestión**: UI completa para coaches
- ✅ **Fallback inteligente**: Plan Free por defecto cuando no hay suscripción
- ✅ **Precios anuales**: Con descuento del 17%

### Modelo de Negocio

```
Business sin suscripción → Plan Free (5 estudiantes, 2 grupos)
                ↓
Business se suscribe a plan → Límites según plan elegido
                ↓
Business alcanza límite → Mensaje sugiriendo upgrade
                ↓
Business puede upgrade/downgrade en cualquier momento
```

---

## Arquitectura

### Diagrama de Relaciones

```
SubscriptionPlan (Catálogo de planes)
        ↓ 1:N
    Subscription (Suscripción activa)
        ↓ N:1
     Business (Negocio de coaching)
        ↓ 1:N
    User (Estudiantes/Runners)
        ↓ N:M
  TrainingGroup (Grupos de entrenamiento)
```

### Base de Datos

#### Tabla: `subscription_plans`

Almacena el catálogo de planes disponibles.

```sql
CREATE TABLE subscription_plans (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    monthly_price DECIMAL(8,2) DEFAULT 0.00,
    annual_price DECIMAL(8,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'USD',
    features JSON,  -- {student_limit, group_limit, storage_limit_gb}
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    INDEX idx_slug (slug)
);
```

**Estructura del JSON `features`:**
```json
{
  "student_limit": 5,          // null = ilimitado
  "group_limit": 2,            // null = ilimitado
  "storage_limit_gb": 1        // null = ilimitado
}
```

#### Tabla: `subscriptions`

Almacena las suscripciones activas de cada business.

```sql
CREATE TABLE subscriptions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    business_id BIGINT UNSIGNED NOT NULL,
    plan_id BIGINT UNSIGNED NOT NULL,
    status ENUM('active','cancelled','expired','trial') DEFAULT 'trial',
    current_period_start DATE NOT NULL,
    current_period_end DATE NOT NULL,
    next_billing_date DATE,
    auto_renew BOOLEAN DEFAULT TRUE,
    cancellation_reason TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id) ON DELETE RESTRICT,

    INDEX idx_business_id (business_id),
    INDEX idx_plan_id (plan_id),
    INDEX idx_status (status),
    INDEX idx_business_status (business_id, status)
);
```

**Estados de Suscripción:**
- `trial`: Período de prueba
- `active`: Suscripción activa y vigente
- `cancelled`: Cancelada por el usuario (mantiene acceso hasta fin de período)
- `expired`: Período vencido sin renovación

---

## Planes de Suscripción

### 1. Plan Free (Gratuito)

**Precio:**
- Mensual: $0
- Anual: $0

**Límites:**
- Estudiantes: 5
- Grupos: 2
- Almacenamiento: 1 GB

**Descripción:**
> Plan gratuito para comenzar. Ideal para coaches independientes que recién empiezan.

**Target:**
- Coaches individuales
- Proyectos personales
- Testing de la plataforma

**Slug:** `free`

---

### 2. Plan Starter

**Precio:**
- Mensual: $19.99
- Anual: $199.99 (ahorro de ~17%)

**Límites:**
- Estudiantes: 20
- Grupos: 5
- Almacenamiento: 5 GB

**Descripción:**
> Para coaches en crecimiento con hasta 20 alumnos y múltiples grupos.

**Target:**
- Coaches con negocio establecido
- 1-2 grupos activos simultáneos
- Gestión profesional

**Slug:** `starter`

---

### 3. Plan Pro

**Precio:**
- Mensual: $49.99
- Anual: $499.99 (ahorro de ~17%)

**Límites:**
- Estudiantes: 100
- Grupos: 20
- Almacenamiento: 20 GB

**Descripción:**
> Plan profesional con límites amplios para negocios establecidos.

**Target:**
- Academias pequeñas
- Coaches con múltiples grupos
- Alto volumen de alumnos

**Slug:** `pro`

---

### 4. Plan Enterprise (Empresarial)

**Precio:**
- Mensual: $99.99
- Anual: $999.99 (ahorro de ~17%)

**Límites:**
- Estudiantes: Ilimitado (null)
- Grupos: Ilimitado (null)
- Almacenamiento: Ilimitado (null)

**Descripción:**
> Plan sin límites para academias y negocios grandes. Escalabilidad total.

**Target:**
- Academias grandes
- Franquicias
- Negocios con crecimiento rápido

**Slug:** `enterprise`

---

## Modelos

### SubscriptionPlan

**Ubicación:** `app/Models/SubscriptionPlan.php`

**Propósito:** Representa el catálogo de planes disponibles.

#### Atributos

```php
protected $fillable = [
    'name', 'slug', 'description', 'monthly_price',
    'annual_price', 'currency', 'features', 'is_active'
];

protected $casts = [
    'features' => 'array',
    'monthly_price' => 'decimal:2',
    'annual_price' => 'decimal:2',
    'is_active' => 'boolean',
];
```

#### Métodos Principales

| Método | Retorno | Descripción |
|--------|---------|-------------|
| `getStudentLimit()` | `?int` | Límite de estudiantes o null (ilimitado) |
| `getGroupLimit()` | `?int` | Límite de grupos o null (ilimitado) |
| `getStorageLimitGb()` | `?int` | Límite de storage en GB o null |
| `hasStudentLimit()` | `bool` | True si tiene límite de estudiantes |
| `hasGroupLimit()` | `bool` | True si tiene límite de grupos |
| `hasStorageLimit()` | `bool` | True si tiene límite de storage |
| `isFree()` | `bool` | True si monthly_price y annual_price son 0 |
| `getAnnualDiscount()` | `float` | % de descuento del plan anual |

#### Scopes

```php
// Obtener solo planes activos
SubscriptionPlan::active()->get();
```

#### Relaciones

```php
// Un plan puede tener muchas suscripciones
$plan->subscriptions;
```

---

### Subscription

**Ubicación:** `app/Models/Subscription.php`

**Propósito:** Representa una suscripción activa de un business a un plan.

#### Atributos

```php
protected $fillable = [
    'business_id', 'plan_id', 'status',
    'current_period_start', 'current_period_end',
    'next_billing_date', 'auto_renew', 'cancellation_reason'
];

protected $casts = [
    'current_period_start' => 'date',
    'current_period_end' => 'date',
    'next_billing_date' => 'date',
    'auto_renew' => 'boolean',
];
```

#### Métodos de Ciclo de Vida

| Método | Descripción |
|--------|-------------|
| `activate()` | Marca la suscripción como activa |
| `cancel($reason)` | Cancela la suscripción (mantiene acceso hasta fin de período) |
| `expire()` | Marca como expirada |
| `renew($months)` | Renueva por N meses |

#### Verificadores de Estado

| Método | Retorno | Descripción |
|--------|---------|-------------|
| `isActive()` | `bool` | True si status = 'active' |
| `isCancelled()` | `bool` | True si status = 'cancelled' |
| `isExpired()` | `bool` | True si status = 'expired' |
| `isTrial()` | `bool` | True si status = 'trial' |
| `isValid()` | `bool` | True si activa/trial Y dentro del período |

#### Validaciones de Límites

| Método | Parámetros | Retorno | Descripción |
|--------|-----------|---------|-------------|
| `canAddStudents()` | `int $count = 1` | `bool` | True si puede agregar N estudiantes |
| `canAddGroups()` | `int $count = 1` | `bool` | True si puede agregar N grupos |
| `hasStorageAvailable()` | `float $requiredGb = 0` | `bool` | True si hay almacenamiento disponible |

**Lógica de validación:**
```php
// Si suscripción no válida → false
// Si límite = null (ilimitado) → true
// Si (count actual + N) <= límite → true
// Caso contrario → false
```

#### Helpers de Período

| Método | Retorno | Descripción |
|--------|---------|-------------|
| `daysRemaining()` | `int` | Días restantes del período actual |
| `isNearExpiration()` | `bool` | True si faltan 7 días o menos |

#### Scopes

```php
// Suscripciones activas
Subscription::active()->get();

// Suscripciones canceladas
Subscription::cancelled()->get();

// Suscripciones expiradas
Subscription::expired()->get();

// Suscripciones en trial
Subscription::trial()->get();
```

#### Relaciones

```php
// Pertenece a un business
$subscription->business;

// Pertenece a un plan
$subscription->plan;
```

---

### Business (Actualizado)

**Ubicación:** `app/Models/Business.php`

#### Nuevas Relaciones

```php
// Todas las suscripciones del business (historial)
$business->subscriptions;

// Suscripción activa actual
$business->activeSubscription;

// Alias de trainingGroups
$business->groups();
```

#### Métodos de Suscripción

| Método | Retorno | Descripción |
|--------|---------|-------------|
| `getActiveSubscription()` | `?Subscription` | Obtiene suscripción activa o null |
| `hasActiveSubscription()` | `bool` | True si tiene suscripción vigente |
| `getCurrentPlan()` | `?SubscriptionPlan` | Obtiene plan actual o null |

#### Validaciones de Límites

| Método | Parámetros | Retorno | Descripción |
|--------|-----------|---------|-------------|
| `canAddStudents()` | `int $count = 1` | `bool` | Verifica si puede agregar N estudiantes |
| `canAddGroups()` | `int $count = 1` | `bool` | Verifica si puede agregar N grupos |
| `hasStorageAvailable()` | `float $requiredGb = 0` | `bool` | Verifica almacenamiento disponible |

**Lógica con Fallback:**
```php
// Si NO tiene suscripción activa:
//   → Usa límites de Plan Free (5 estudiantes, 2 grupos, 1GB)

// Si tiene suscripción activa:
//   → Delega a $subscription->canAddStudents()
```

---

## Flujos de Usuario

### 1. Ver Suscripción Actual

**Ruta:** `/{business}/coach/subscriptions`

**Flujo:**
```
1. Coach hace clic en "Suscripción" en sidebar
2. SubscriptionController@index
3. Obtiene business del coach autenticado
4. Obtiene suscripción activa (o null)
5. Calcula uso actual de recursos
6. Renderiza vista con:
   - Plan actual (nombre, estado, precio)
   - Días restantes y fecha de vencimiento
   - Barras de progreso (estudiantes, grupos)
   - Alertas (cerca de límite, próximo a vencer)
   - Formulario de cancelación
```

**Vista:** `resources/views/coach/subscriptions/index.blade.php`

---

### 2. Ver Planes Disponibles

**Ruta:** `/{business}/coach/subscriptions/plans`

**Flujo:**
```
1. Coach hace clic en "Ver Planes"
2. SubscriptionController@plans
3. Obtiene todos los planes activos
4. Identifica plan actual del business
5. Renderiza grid con:
   - Card por cada plan
   - Destaca plan actual
   - Precios (mensual y anual con descuento)
   - Características (límites)
   - Botón "Activar Plan" o "Cambiar a este Plan"
```

**Vista:** `resources/views/coach/subscriptions/plans.blade.php`

---

### 3. Cambiar de Plan (Upgrade/Downgrade)

**Ruta:** `POST /{business}/coach/subscriptions/subscribe`

**Parámetros:**
- `plan_id`: ID del plan destino

**Flujo:**
```
1. Coach selecciona plan y confirma
2. SubscriptionController@subscribe
3. Validaciones:
   - Business existe y pertenece al coach
   - Plan existe
   - Plan destino ≠ plan actual
4. Proceso:
   - Cancela suscripción anterior (si existe)
   - Crea nueva suscripción:
     - status: 'active'
     - current_period_start: now()
     - current_period_end: now() + 1 mes
     - next_billing_date: current_period_end + 1 día
     - auto_renew: true
5. Redirect a index con mensaje de éxito
```

**Resultado:**
- Suscripción anterior: `status = 'cancelled'`
- Nueva suscripción: `status = 'active'`
- Límites actualizados inmediatamente

---

### 4. Cancelar Suscripción

**Ruta:** `POST /{business}/coach/subscriptions/cancel`

**Parámetros:**
- `reason` (opcional): Motivo de cancelación

**Flujo:**
```
1. Coach completa formulario de cancelación
2. SubscriptionController@cancel
3. Validaciones:
   - Business existe y pertenece al coach
   - Tiene suscripción activa
4. Proceso:
   - Marca suscripción como 'cancelled'
   - Guarda motivo de cancelación
   - auto_renew = false
   - NO modifica current_period_end
5. Redirect a index con mensaje de confirmación
```

**Resultado:**
- Suscripción: `status = 'cancelled'`
- Coach mantiene acceso hasta `current_period_end`
- NO se cobra próximo período

---

### 5. Validación Automática de Límites

#### Al Registrar Estudiante

**Flujo:**
```
1. Alumno intenta registrarse con invitation token
2. RegisterController@register
3. Decodifica business_id del token
4. Verifica business->canAddStudents(1)
5. SI alcanzó límite:
   - Retorna error con mensaje:
     "Este negocio ha alcanzado el límite de estudiantes..."
   - Sugiere que coach actualice plan
6. SI tiene espacio:
   - Crea usuario normalmente
```

**Archivo:** `app/Http/Controllers/Auth/v1/RegisterController.php`

---

#### Al Crear Grupo

**Flujo:**
```
1. Coach intenta crear grupo de entrenamiento
2. TrainingGroupController@store
3. Verifica business->canAddGroups(1)
4. SI alcanzó límite:
   - Retorna error con mensaje:
     "Has alcanzado el límite de grupos de tu plan..."
   - Sugiere actualizar plan
5. SI tiene espacio:
   - Crea grupo normalmente
```

**Archivo:** `app/Http/Controllers/Coach/TrainingGroupController.php`

---

## Validaciones y Límites

### Lógica de Validación

#### Estudiantes

```php
// En Business.php
public function canAddStudents(int $count = 1): bool
{
    $subscription = $this->getActiveSubscription();

    // Sin suscripción → límites de Free
    if (!$subscription) {
        return $this->runners()->count() + $count <= 5;
    }

    // Con suscripción → delega a Subscription
    return $subscription->canAddStudents($count);
}
```

```php
// En Subscription.php
public function canAddStudents(int $count = 1): bool
{
    if (!$this->isValid()) {
        return false;
    }

    $limit = $this->plan->getStudentLimit();

    // null = ilimitado
    if ($limit === null) {
        return true;
    }

    $currentCount = $this->business->runners()->count();
    return ($currentCount + $count) <= $limit;
}
```

#### Grupos

```php
// Lógica idéntica pero con grupos
$limit = $this->plan->getGroupLimit();
$currentCount = $this->business->groups()->count();
return ($currentCount + $count) <= $limit;
```

#### Almacenamiento

```php
// Por ahora siempre retorna true (no implementado)
public function hasStorageAvailable(float $requiredGb = 0): bool
{
    if (!$this->isValid()) {
        return false;
    }

    $limit = $this->plan->getStorageLimitGb();

    if ($limit === null) {
        return true;
    }

    // TODO: Implementar cálculo real de storage usado
    return true;
}
```

### Helper de Mensajes

**Función:** `subscriptionLimitMessage()`
**Ubicación:** `app/helpers.php`

```php
function subscriptionLimitMessage(string $resource, Business $business): string
{
    $currentPlan = $business->getCurrentPlan();
    $planName = $currentPlan ? $currentPlan->name : 'free';

    if ($resource === 'students') {
        $limit = $currentPlan ? $currentPlan->getStudentLimit() : 5;
        $resourceLabel = 'estudiantes';
    } else {
        $limit = $currentPlan ? $currentPlan->getGroupLimit() : 2;
        $resourceLabel = 'grupos';
    }

    return "Has alcanzado el límite de {$resourceLabel} de tu plan {$planName} " .
           "({$limit} {$resourceLabel}). Actualiza tu plan para poder agregar más {$resourceLabel}.";
}
```

**Uso:**
```php
if (!$business->canAddGroups(1)) {
    return back()->with('error', subscriptionLimitMessage('groups', $business));
}
```

---

## API de Métodos

### Consultar Plan Actual

```php
$business = Business::find(1);

// Obtener plan
$plan = $business->getCurrentPlan();

if ($plan) {
    echo $plan->name; // "Starter"
    echo $plan->getStudentLimit(); // 20
    echo $plan->getGroupLimit(); // 5
}
```

### Verificar Límites

```php
$business = Business::find(1);

// ¿Puede agregar 1 estudiante?
if ($business->canAddStudents(1)) {
    // Permitir registro
} else {
    // Mostrar mensaje de límite alcanzado
}

// ¿Puede agregar 5 estudiantes de golpe?
if ($business->canAddStudents(5)) {
    // Permitir importación masiva
}

// ¿Puede crear nuevo grupo?
if ($business->canAddGroups(1)) {
    // Permitir crear grupo
}
```

### Obtener Suscripción Activa

```php
$business = Business::find(1);
$subscription = $business->getActiveSubscription();

if ($subscription) {
    echo $subscription->status; // "active"
    echo $subscription->daysRemaining(); // 25

    if ($subscription->isNearExpiration()) {
        // Mostrar alerta
    }
}
```

### Cambiar Plan Programáticamente

```php
$business = Business::find(1);
$newPlan = SubscriptionPlan::where('slug', 'pro')->first();

// Cancelar suscripción actual
$currentSubscription = $business->getActiveSubscription();
if ($currentSubscription) {
    $currentSubscription->cancel('Upgrade a Pro');
}

// Crear nueva suscripción
Subscription::create([
    'business_id' => $business->id,
    'plan_id' => $newPlan->id,
    'status' => 'active',
    'current_period_start' => now(),
    'current_period_end' => now()->addMonth(),
    'next_billing_date' => now()->addMonth()->addDay(),
    'auto_renew' => true,
]);
```

---

## Ejemplos de Uso

### Ejemplo 1: Validar al Registrar Usuario

```php
// RegisterController.php

public function register(Request $request)
{
    $data = $request->validated();

    // Obtener business del token de invitación
    $businessId = $this->decodeInvitationToken($request->input('invitation_token'));

    if ($businessId) {
        $business = Business::find($businessId);

        // VALIDAR LÍMITE
        if ($business && !$business->canAddStudents(1)) {
            return back()->withErrors([
                'invitation_token' => 'Este negocio ' .
                    lcfirst(subscriptionLimitMessage('students', $business)) .
                    ' El coach debe actualizar su plan.'
            ])->withInput();
        }
    }

    // Crear usuario si pasó validación
    $user = User::create([...]);

    return redirect()->route('dashboard');
}
```

### Ejemplo 2: Mostrar Límites en Dashboard

```php
// DashboardController.php

public function index()
{
    $business = auth()->user()->business;

    $studentsCount = $business->runners()->count();
    $groupsCount = $business->groups()->count();

    $subscription = $business->getActiveSubscription();
    $plan = $business->getCurrentPlan();

    $studentLimit = $plan ? $plan->getStudentLimit() : 5;
    $groupLimit = $plan ? $plan->getGroupLimit() : 2;

    return view('coach.dashboard', compact(
        'studentsCount', 'studentLimit',
        'groupsCount', 'groupLimit',
        'subscription'
    ));
}
```

### Ejemplo 3: Renovar Suscripción Automáticamente

```php
// Command/Job para ejecutar diariamente

public function handle()
{
    // Suscripciones que vencen hoy y tienen auto_renew
    $subscriptions = Subscription::where('current_period_end', today())
        ->where('auto_renew', true)
        ->where('status', 'active')
        ->get();

    foreach ($subscriptions as $subscription) {
        // Procesar pago (integración con Stripe/PayPal)
        $payment = $this->processPayment($subscription);

        if ($payment->successful()) {
            // Renovar por 1 mes
            $subscription->renew(1);
        } else {
            // Marcar como expirada
            $subscription->expire();
        }
    }
}
```

---

## Integración en Controllers

### Controllers que Validan Límites

#### 1. RegisterController

**Archivo:** `app/Http/Controllers/Auth/v1/RegisterController.php`
**Método:** `register()`
**Validación:** Límite de estudiantes

```php
if ($businessId) {
    $business = Business::find($businessId);

    if ($business && !$business->canAddStudents(1)) {
        return back()->withErrors([
            'invitation_token' => 'Este negocio ' .
                lcfirst(subscriptionLimitMessage('students', $business)) .
                ' El coach debe actualizar su plan.'
        ])->withInput();
    }
}
```

#### 2. TrainingGroupController

**Archivo:** `app/Http/Controllers/Coach/TrainingGroupController.php`
**Método:** `store()`
**Validación:** Límite de grupos

```php
$business = $user->business;

if (!$business->canAddGroups(1)) {
    return back()->with('error', subscriptionLimitMessage('groups', $business));
}
```

### SubscriptionController

**Archivo:** `app/Http/Controllers/Coach/SubscriptionController.php`

**Métodos:**

1. **index()**: Vista de suscripción actual
   - Obtiene business y suscripción activa
   - Calcula uso de recursos
   - Retorna vista con métricas

2. **plans()**: Vista de planes disponibles
   - Lista todos los planes activos
   - Identifica plan actual
   - Retorna vista con grid

3. **subscribe(Request)**: Cambiar de plan
   - Valida plan_id
   - Cancela suscripción anterior
   - Crea nueva suscripción activa
   - Redirect con mensaje de éxito

4. **cancel(Request)**: Cancelar suscripción
   - Valida que tenga suscripción activa
   - Marca como cancelada
   - Guarda motivo
   - Redirect con confirmación

---

## Vistas y UI

### Vista: index.blade.php

**Ubicación:** `resources/views/coach/subscriptions/index.blade.php`

**Componentes:**

1. **Card de Plan Actual**
   - Nombre del plan
   - Badge de estado (activa, trial, cancelada)
   - Días restantes
   - Fecha de vencimiento
   - Alerta de vencimiento próximo (≤7 días)

2. **Card de Uso de Recursos**
   - Barra de progreso: Estudiantes (X / límite)
   - Barra de progreso: Grupos (X / límite)
   - Alertas cuando uso ≥ 80%
   - Colores: verde/naranja/rojo según uso

3. **Botones de Acción**
   - Ver Planes (link a plans)
   - Cancelar Suscripción (formulario con motivo)

**Diseño:**
- Grid de 2 columnas en desktop
- Cards con fondo oscuro consistente
- Barras de progreso animadas
- Alertas con iconos

---

### Vista: plans.blade.php

**Ubicación:** `resources/views/coach/subscriptions/plans.blade.php`

**Componentes:**

1. **Grid de Planes**
   - 4 columnas en desktop
   - 2 columnas en tablet
   - 1 columna en móvil
   - Cards con altura igual

2. **Cada Card de Plan**
   - Badge "Plan Actual" si corresponde
   - Nombre del plan
   - Precio mensual (grande)
   - Precio anual con % descuento
   - Lista de características con checks
   - Botón "Activar Plan" o "Plan Actual" (disabled)

3. **Características Mostradas**
   - Estudiantes (límite o "Ilimitado")
   - Grupos (límite o "Ilimitado")
   - Almacenamiento (GB o "Ilimitado")

**Diseño:**
- Borde especial para plan actual
- Gradient en botones
- Iconos SVG para checks
- Responsive grid

---

### Sidebar

**Ubicación:** `resources/views/layouts/app.blade.php`

**Enlace Agregado:**
```html
<a href="{{ businessRoute('coach.subscriptions.index') }}"
   class="sidebar-nav-link {{ request()->routeIs('coach.subscriptions.*') || request()->routeIs('business.coach.subscriptions.*') ? 'active' : '' }}">
    <!-- Icono de tarjeta de crédito -->
    <svg viewBox="0 0 24 24">
        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
        <path d="M2 10h20"></path>
    </svg>
    <span class="sidebar-expanded-text">Suscripción</span>
</a>
```

**Visibilidad:** Solo para usuarios con rol `coach` o `admin`

---

## Testing

### Casos de Prueba Manuales

#### 1. Ver Suscripción sin Plan Activo

```
✅ Business sin suscripción muestra "Plan Free"
✅ Límites muestran: 5 estudiantes, 2 grupos
✅ Botón "Ver Planes" funciona
✅ No hay formulario de cancelación
```

#### 2. Cambiar a Plan Starter

```
✅ Grid muestra 4 planes
✅ Plan Free marcado como actual
✅ Click en "Activar Plan" de Starter
✅ Redirect a index con mensaje de éxito
✅ Plan actual ahora es Starter
✅ Límites actualizados: 20 estudiantes, 5 grupos
```

#### 3. Validación de Límite de Estudiantes

```
✅ Business con plan Free (5 estudiantes)
✅ Crear 5 estudiantes → OK
✅ Intentar crear 6to estudiante → Error
✅ Mensaje muestra plan actual y límite
✅ Sugiere actualizar plan
```

#### 4. Validación de Límite de Grupos

```
✅ Business con plan Starter (5 grupos)
✅ Crear 5 grupos → OK
✅ Intentar crear 6to grupo → Error
✅ Mensaje usa helper subscriptionLimitMessage()
✅ Sugiere actualizar plan
```

#### 5. Cancelar Suscripción

```
✅ Suscripción activa con 15 días restantes
✅ Click en "Cancelar Suscripción"
✅ Formulario solicita motivo (opcional)
✅ Submit → Confirmación
✅ Status cambia a 'cancelled'
✅ Mantiene acceso hasta fecha de vencimiento
✅ auto_renew = false
```

#### 6. Barras de Progreso

```
✅ Business con 3 de 5 estudiantes → 60% (verde)
✅ Business con 4 de 5 estudiantes → 80% (naranja)
✅ Business con 5 de 5 estudiantes → 100% (rojo)
✅ Alerta "Cerca del límite" aparece ≥ 80%
```

#### 7. Alertas de Vencimiento

```
✅ Suscripción con 10 días restantes → Sin alerta
✅ Suscripción con 7 días restantes → Alerta naranja
✅ Suscripción con 2 días restantes → Alerta naranja
✅ Mensaje muestra días exactos restantes
```

### Tests Automatizados (Futuro)

```php
// tests/Feature/SubscriptionTest.php

/** @test */
public function business_without_subscription_uses_free_limits()
{
    $business = Business::factory()->create();

    $this->assertTrue($business->canAddStudents(5));
    $this->assertFalse($business->canAddStudents(6));
    $this->assertTrue($business->canAddGroups(2));
    $this->assertFalse($business->canAddGroups(3));
}

/** @test */
public function business_can_change_to_higher_plan()
{
    $business = Business::factory()->create();
    $starterPlan = SubscriptionPlan::where('slug', 'starter')->first();

    // Suscribir a Starter
    $subscription = Subscription::create([
        'business_id' => $business->id,
        'plan_id' => $starterPlan->id,
        'status' => 'active',
        'current_period_start' => now(),
        'current_period_end' => now()->addMonth(),
    ]);

    $this->assertTrue($business->canAddStudents(20));
    $this->assertFalse($business->canAddStudents(21));
}
```

---

## Próximos Pasos

### Sprint 6: Integración de Pagos

**Objetivo:** Conectar con pasarela de pagos para cobros reales.

**Tareas:**
1. Integrar Stripe/PayPal SDK
2. Crear checkout flow
3. Webhooks para eventos:
   - `payment.succeeded` → Activar suscripción
   - `payment.failed` → Notificar al usuario
   - `subscription.cancelled` → Actualizar estado
4. Guardar métodos de pago
5. Historial de transacciones
6. Facturas en PDF

**Archivos a crear:**
- `app/Services/PaymentService.php`
- `app/Http/Controllers/Coach/CheckoutController.php`
- `routes/webhooks.php`
- `resources/views/coach/checkout.blade.php`

---

### Sprint 7: Sistema de Notificaciones

**Objetivo:** Alertas por email sobre suscripciones.

**Tareas:**
1. Email de bienvenida al activar plan
2. Email 7 días antes de vencimiento
3. Email al alcanzar 80% de límite
4. Email de renovación exitosa
5. Email de pago fallido
6. Email de cancelación confirmada

**Archivos a crear:**
- `app/Mail/SubscriptionWelcome.php`
- `app/Mail/SubscriptionExpiring.php`
- `app/Mail/LimitReached.php`
- `app/Jobs/SendSubscriptionNotifications.php`
- `resources/views/emails/subscriptions/*`

---

### Sprint 8: Panel de Administración

**Objetivo:** Gestión de suscripciones desde admin.

**Tareas:**
1. Vista de todos los businesses y sus planes
2. Asignación manual de planes
3. Estadísticas de suscripciones
4. CRUD de planes
5. Reportes de facturación
6. Dashboard de métricas

**Archivos a crear:**
- `app/Http/Controllers/Admin/SubscriptionController.php`
- `resources/views/admin/subscriptions/*`
- Políticas de autorización para admin

---

### Mejoras Futuras

**Funcionalidades Pendientes:**

1. **Cupones y Descuentos**
   - Tabla `coupons`
   - Aplicar descuentos en checkout
   - Cupones de prueba gratis

2. **Métricas de Almacenamiento**
   - Calcular storage real usado
   - Implementar validación de límite
   - Dashboard de uso de storage

3. **Planes Personalizados**
   - Permitir crear planes custom para clientes específicos
   - Negociación de límites

4. **Período de Trial**
   - 14 días gratis de cualquier plan
   - Conversión automática a pago

5. **Facturación Prorrateada**
   - Al cambiar de plan mid-cycle
   - Calcular crédito/cargo proporcional

6. **Multi-Currency**
   - Soporte para ARS, EUR, etc.
   - Conversión automática

---

## Changelog

### v1.0 - 2025-12-23 (Sprint 5)

**Agregado:**
- Sistema completo de suscripciones
- 4 planes predefinidos (Free, Starter, Pro, Enterprise)
- Validaciones automáticas de límites
- Panel UI para gestión
- Fallback a plan Free
- Documentación completa

**Archivos Creados:**
- 2 migraciones
- 2 modelos (SubscriptionPlan, Subscription)
- 1 controlador (SubscriptionController)
- 1 helper (subscriptionLimitMessage)
- 2 vistas (index, plans)
- 1 seeder (SubscriptionPlanSeeder)

**Modificado:**
- Business model (9 métodos agregados)
- RegisterController (validación de límites)
- TrainingGroupController (validación de límites)
- Sidebar (enlace Suscripción)

---

**Última actualización:** 2025-12-23
