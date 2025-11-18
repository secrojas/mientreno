# MiEntreno - Arquitectura de la Aplicación

## Visión General

MiEntreno es una plataforma de registro y análisis de entrenamientos de running con soporte multi-tenant, que permite tanto a corredores individuales como a grupos de entrenamiento gestionar sus actividades.

---

## Modelo de Datos

### Entidades Principales

```
┌─────────────┐
│  Business   │ (Grupos de entrenamiento)
└──────┬──────┘
       │ 1:N
       │
┌──────▼──────┐
│    User     │ (Corredores y coaches)
└──────┬──────┘
       │ 1:N
       │
       ├──────────┐
       │          │
┌──────▼──────┐  │
│  Workout    │  │ (Entrenamientos individuales)
└─────────────┘  │
       │         │
       │         │
┌──────▼──────┐  │
│    Race     │  │ (Carreras)
└─────────────┘  │
       │         │
┌──────▼──────┐  │
│    Goal     │  │ (Objetivos)
└─────────────┘  │
                │
         ┌──────▼──────────┐
         │ TrainingGroup   │ (Grupos dentro de business)
         └──────┬──────────┘
                │ N:M
         ┌──────▼──────────┐
         │   Attendance    │ (Asistencias)
         └─────────────────┘
```

### 1. Business (Ya implementado)

Representa un equipo/grupo de entrenamiento o una organización.

```php
businesses
├── id
├── name                // "Running Team BA", "Corredores del Sur"
├── slug                // "running-team-ba" (para URLs)
├── settings (JSON)     // Configuraciones específicas del grupo
└── timestamps
```

**Relaciones**:
- `hasMany(User::class)`
- `hasMany(TrainingGroup::class)`

---

### 2. User (Parcialmente implementado)

Corredores, coaches o administradores.

```php
users
├── id
├── business_id (nullable)   // null = usuario individual
├── name
├── email                    // Único por business
├── password
├── role                     // 'runner', 'coach', 'admin'
├── profile (JSON)           // Datos adicionales del perfil
│   ├── birth_date
│   ├── gender
│   ├── running_level       // 'beginner', 'intermediate', 'advanced'
│   └── preferences
├── remember_token
└── timestamps
```

**Roles posibles**:
- `runner`: Corredor que registra sus entrenamientos
- `coach`: Entrenador con acceso a vista de alumnos
- `admin`: Administrador del business

**Relaciones**:
- `belongsTo(Business::class)`
- `hasMany(Workout::class)`
- `hasMany(Race::class)`
- `hasMany(Goal::class)`
- `belongsToMany(TrainingGroup::class)` - como miembro
- `hasMany(TrainingGroup::class, 'coach_id')` - como coach

---

### 3. Workout (Entrenamientos)

Registro individual de cada entrenamiento.

```php
workouts
├── id
├── user_id
├── training_group_id (nullable)  // Si fue entrenamiento grupal
├── date                          // Fecha del entrenamiento
├── type                          // 'easy_run', 'intervals', 'tempo', 'long_run', 'race'
├── distance                      // En kilómetros (decimal)
├── duration                      // En segundos (integer)
├── avg_pace                      // Calculado: duration / distance (en seg/km)
├── avg_heart_rate (nullable)     // Pulsaciones por minuto
├── elevation_gain (nullable)     // Desnivel positivo en metros
├── difficulty                    // 1-5 (percepción del esfuerzo)
├── notes (text)                  // Notas del corredor
├── weather (JSON)                // Opcional: temp, condiciones
├── route (JSON)                  // Opcional: datos GPS, mapa
├── is_race                       // Boolean: si fue carrera oficial
├── race_id (nullable)            // Si está asociado a una carrera
└── timestamps
```

**Tipos de entrenamiento**:
- `easy_run`: Fondo suave
- `intervals`: Pasadas/series
- `tempo`: Ritmo sostenido
- `long_run`: Tirada larga
- `recovery`: Recuperación
- `race`: Competencia

**Relaciones**:
- `belongsTo(User::class)`
- `belongsTo(TrainingGroup::class)`
- `belongsTo(Race::class)`

**Scopes útiles**:
- `thisWeek()`, `thisMonth()`, `thisYear()`
- `byType($type)`
- `forUser($userId)`

---

### 4. Race (Carreras)

Carreras en las que el usuario participó o participará.

```php
races
├── id
├── user_id
├── name                    // "Maratón de Buenos Aires"
├── distance                // Distancia oficial (5K, 10K, 21K, 42K, etc.)
├── date                    // Fecha de la carrera
├── location                // Ciudad/lugar
├── status                  // 'upcoming', 'completed', 'cancelled'
├── target_time (nullable)  // Tiempo objetivo (en segundos)
├── actual_time (nullable)  // Tiempo real (si ya corrió)
├── position (nullable)     // Posición general
├── category_position (nullable)
├── bib_number (nullable)   // Número de dorsal
├── notes (text)
├── workout_id (nullable)   // Link al workout si lo registró
└── timestamps
```

**Relaciones**:
- `belongsTo(User::class)`
- `hasOne(Workout::class)` - el entrenamiento asociado si existe

---

### 5. Goal (Objetivos)

Metas que se propone el corredor.

```php
goals
├── id
├── user_id
├── type                    // 'race', 'distance', 'pace', 'frequency'
├── title                   // "Correr 10K sub 50 minutos"
├── description (text)
├── target_value (JSON)     // Valor objetivo según el tipo
│   ├── distance (for distance goals)
│   ├── time (for race goals)
│   ├── pace (for pace goals)
│   └── sessions_per_week (for frequency goals)
├── target_date (nullable)  // Fecha límite
├── status                  // 'active', 'completed', 'abandoned'
├── progress (JSON)         // Progreso actual
├── race_id (nullable)      // Si está asociado a una carrera
└── timestamps
```

**Tipos de objetivos**:
- `race`: Completar una carrera en X tiempo
- `distance`: Correr X km por semana/mes
- `pace`: Mejorar pace promedio a X:XX/km
- `frequency`: Entrenar X veces por semana

**Relaciones**:
- `belongsTo(User::class)`
- `belongsTo(Race::class)` - si el objetivo es una carrera específica

---

### 6. TrainingGroup (Grupos de Entrenamiento)

Grupos dentro de un business (por ej: "Grupo Principiantes", "Grupo 10K").

```php
training_groups
├── id
├── business_id
├── coach_id                // Usuario que es coach del grupo
├── name                    // "Grupo Principiantes Lunes y Miércoles"
├── description (text)
├── schedule (JSON)         // Días y horarios de entrenamiento
│   └── [{ day: 'monday', time: '19:00' }, ...]
├── level                   // 'beginner', 'intermediate', 'advanced'
├── max_members (nullable)
├── is_active               // Boolean
└── timestamps
```

**Relaciones**:
- `belongsTo(Business::class)`
- `belongsTo(User::class, 'coach_id')` - el coach
- `belongsToMany(User::class)->withPivot('joined_at', 'is_active')` - miembros
- `hasMany(Workout::class)` - entrenamientos del grupo
- `hasMany(Attendance::class)`

---

### 7. Attendance (Asistencias)

Registro de asistencia a entrenamientos grupales.

```php
attendances
├── id
├── training_group_id
├── user_id
├── date                    // Fecha del entrenamiento
├── status                  // 'present', 'absent', 'justified'
├── notes (text)
└── timestamps
```

**Relaciones**:
- `belongsTo(TrainingGroup::class)`
- `belongsTo(User::class)`

---

### 8. TrainingPlan (Planes de Entrenamiento) - Fase 2

Para funcionalidad más avanzada de coaches.

```php
training_plans
├── id
├── coach_id
├── user_id (nullable)      // Si es para un alumno específico
├── training_group_id (nullable)  // Si es para un grupo
├── name                    // "Plan 10K - 8 semanas"
├── description (text)
├── start_date
├── end_date
├── weeks                   // Número de semanas
├── template (JSON)         // Estructura del plan por semana
└── timestamps
```

---

## Lógica de Negocio Clave

### 1. Métricas Calculadas

**A nivel de Workout**:
- `avg_pace` = `duration / distance` (seg/km)
- Convertir a formato MM:SS/km para display

**A nivel de Usuario (agregados)**:
- Total km semana/mes/año
- Promedio de pace
- Total de entrenamientos
- Racha de días consecutivos

### 2. Sistema Multi-tenant

**Usuario Individual** (`business_id = null`):
- Accede directo a su dashboard
- No ve funcionalidades de grupo
- Solo gestiona sus propios datos

**Usuario en Business**:
- URL: `/{business_slug}/dashboard`
- Ve sus datos + datos del grupo (si es miembro)
- Si es coach: ve datos de alumnos

### 3. Roles y Permisos

```php
// Runner
- Ver sus propios entrenamientos, carreras, objetivos
- Registrar nuevos entrenamientos
- Unirse a grupos de su business

// Coach
- Todo lo de Runner
+ Ver entrenamientos de alumnos de sus grupos
+ Crear y gestionar grupos
+ Ver asistencias
+ Crear planes de entrenamiento

// Admin
- Todo lo de Coach
+ Gestionar el business
+ Gestionar usuarios del business
+ Configuraciones globales
```

---

## API / Endpoints Principales

### Autenticación
- `GET /{business}/login` - Formulario login
- `POST /{business}/login` - Autenticar
- `POST /{business}/logout` - Cerrar sesión
- `GET /{business}/register` - Formulario registro
- `POST /{business}/register` - Registrar usuario

### Dashboard
- `GET /{business}/dashboard` - Vista principal
- `GET /dashboard` - Para usuarios sin business

### Workouts
- `GET /{business}/workouts` - Lista de entrenamientos
- `POST /{business}/workouts` - Crear entrenamiento
- `GET /{business}/workouts/{id}` - Ver detalle
- `PUT /{business}/workouts/{id}` - Editar
- `DELETE /{business}/workouts/{id}` - Eliminar

### Races
- `GET /{business}/races` - Lista de carreras
- `POST /{business}/races` - Crear carrera
- Similar CRUD

### Goals
- Similar estructura CRUD

### Training Groups (solo coaches)
- `GET /{business}/groups` - Lista de grupos
- `POST /{business}/groups` - Crear grupo
- `GET /{business}/groups/{id}/members` - Miembros
- `POST /{business}/groups/{id}/members` - Agregar miembro

### Coach Panel
- `GET /{business}/coach/students` - Lista de alumnos
- `GET /{business}/coach/students/{id}` - Detalle de alumno
- `GET /{business}/coach/groups/{id}/attendance` - Asistencias

---

## Stack Tecnológico

### Backend
- **Framework**: Laravel 11.x
- **Base de Datos**: MySQL 8.x
- **Autenticación**: Laravel Sanctum (para API futura)
- **Validación**: Form Requests
- **Autorización**: Policies

### Frontend (Actual)
- **Templating**: Blade
- **CSS**: Vanilla CSS con custom properties
- **JS**: Vanilla JS (Alpine.js como opción futura)

### Frontend (Futuro - Opcional)
- **React/Vue**: Para dashboard interactivo
- **Charts**: Chart.js / ApexCharts para gráficos
- **Maps**: Leaflet para rutas

### DevOps
- **Desarrollo**: Laragon (Windows)
- **Versionado**: Git
- **Deploy**: Por definir (VPS, Laravel Forge, etc.)

---

## Seguridad

1. **Multi-tenancy**: Validar business_id en todas las queries
2. **Policies**: Verificar ownership de recursos
3. **Validación**: Form Requests en todos los endpoints
4. **SQL Injection**: Usar Eloquent/Query Builder
5. **XSS**: Escapar output en Blade (`{{ }}`)
6. **CSRF**: Tokens en formularios

---

## Testing (Recomendado)

### Feature Tests
- Autenticación multi-tenant
- CRUD de workouts, races, goals
- Cálculos de métricas
- Permisos de coaches

### Unit Tests
- Modelos: relationships, scopes
- Cálculo de pace
- Agregación de métricas

---

## Consideraciones de Performance

1. **Indexación**:
   - `users.business_id`
   - `workouts.user_id`, `workouts.date`
   - `races.user_id`, `races.date`

2. **Eager Loading**:
   - Cargar relaciones con `with()` para evitar N+1

3. **Caching**:
   - Métricas semanales/mensuales (cache de 1 hora)
   - Totalizadores del dashboard

4. **Paginación**:
   - Listar entrenamientos (25 por página)
   - Listar alumnos del coach

---

## Fases de Desarrollo

Ver `ROADMAP.md` para el plan detallado de implementación.
