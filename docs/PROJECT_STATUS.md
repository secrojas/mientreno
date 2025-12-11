# MiEntreno - Estado del Proyecto

**Fecha de inicio**: Noviembre 2025
**Stack**: Laravel 11.x
**Concepto**: Aplicación de registro y análisis de entrenamientos de running que mezcla el mundo del desarrollo con el running.

---

## Estado Actual (2025-12-11)

### Lo que ya está implementado

#### 1. Infraestructura Base
- Laravel 11.x instalado y configurado
- Sistema de autenticación base (Laravel Breeze)
- Migraciones de tablas base ejecutadas

#### 2. Multi-tenancy / Sistema de Businesses
- **Tabla `businesses`**: Para representar grupos/equipos de entrenamiento
  - Campos: `id`, `name`, `slug`, `settings` (JSON), `timestamps`
  - Permite que múltiples grupos usen la plataforma de forma independiente

- **Sistema de usuarios por business**:
  - Campo `business_id` en users (nullable, permite usuarios sin grupo)
  - Campo `role` en users (default: 'user')
  - Email único por business (no globalmente único)
  - Constraint: `users_business_email_unique`

#### 3. Rutas y Autenticación
- Rutas con prefijo `{business}` para multi-tenancy
- Middleware `set.business` para contexto de business
- Controllers custom de autenticación (v1):
  - `RegisterController`
  - `LoginController`
  - `DashboardController`

#### 4. Diseño y Frontend
Carpeta `landing/` con 4 HTMLs completos y profesionales:

- **`index.html`**: Landing page principal
  - Hero section con presentación del concepto
  - Features: Registro de entrenos, Modo Coach, Pensado por devs
  - FAQ section
  - Branding completo con logo y colores definidos

- **`dashboard.html`**: Dashboard de usuario
  - Sidebar con navegación completa
  - Secciones: Dashboard, Entrenamientos, Carreras, Objetivos, Grupos, Alumnos
  - Métricas semanales: km, tiempo, pace, próxima carrera
  - Panel Coach integrado
  - Diseño responsive

- **`login.html`**: Página de login
- **`register.html`**: Página de registro

**Sistema de diseño**:
- Paleta de colores oscura profesional
- Tipografías: Space Grotesk (headers) + Inter (body)
- Componentes: cards, buttons, badges, pills
- Estilo dev-friendly con código embebido

#### 5. Modelos Existentes
- `User`: Con relación a Business, campo role, y relación workouts
- `Business`: Modelo básico para grupos de entrenamiento
- `Workout`: Modelo completo con relaciones, scopes y helpers ✅
- `Race`: Modelo base creado (funcionalidad pendiente)
- `TrainingGroup`: Modelo base creado (funcionalidad pendiente)

#### 6. Sistema de Autenticación Refactorizado
- **Rutas simplificadas**: /login, /register, /dashboard (sin business en URL)
- **Sistema de invitaciones con tokens**: Base64 encoding de business_id
- **Comando artisan**: `invitation:generate {business_slug}`
- **Usuarios individuales**: business_id nullable permite corredores sin grupo
- **Login unificado**: Busca usuario por email sin importar business

#### 7. Funcionalidad de Workouts (FASE 1 COMPLETADA) ✅

**Base de datos:**
- Tabla `workouts` con 18 campos
- Relaciones: user, training_group (nullable), race (nullable)
- Indices optimizados para queries frecuentes

**Modelo Workout:**
- 6 tipos de entrenamiento: easy_run, intervals, tempo, long_run, recovery, race
- Scopes: thisWeek(), thisMonth(), thisYear(), byType(), forUser()
- Helpers: calculatePace(), formattedPace, formattedDuration, typeLabel
- Casts automáticos para dates, decimals y JSON

**WorkoutController (CRUD completo):**
- index: Lista paginada (15 por página)
- create/store: Formulario con validación y cálculo automático de pace
- edit/update: Edición con ownership validation
- destroy: Eliminación con confirmación
- Seguridad: Solo el dueño puede ver/editar/eliminar sus workouts

**Vistas Blade:**
- `workouts/create.blade.php`: Formulario con inputs de duración (H:M:S) y selector visual de dificultad
- `workouts/index.blade.php`: Lista responsive con paginación y estado vacío
- `workouts/edit.blade.php`: Edición pre-cargada con datos

**Dashboard Integrado:**
- Métricas semanales: km totales, tiempo total, pace medio, número de sesiones
- Lista de 5 entrenamientos más recientes con links a editar
- Panel de resumen: totales históricos y fecha de registro
- Datos reales desde la base de datos (no hardcodeados)

**Seeder con datos de prueba:**
- 13 workouts distribuidos en 4 semanas
- 142.5 km totales, 11h 55min de entrenamiento
- Variedad de tipos, distancias y dificultades
- Usuario de prueba: atleta@test.com / password

---

## Lo que falta implementar

### 1. Modelos Core de Running (Pendientes)
- ~~`Workout`~~ ✅ **COMPLETADO**
- `Race`: Implementar CRUD y funcionalidad completa
- `Goal`: Objetivos del corredor
- `TrainingPlan`: Planes de entrenamiento
- ~~`TrainingGroup`~~ (base creada, falta funcionalidad)
- `Attendance`: Asistencias a entrenamientos grupales

### 2. Base de Datos
- Migraciones para todos los modelos core
- Relaciones entre modelos
- Seeders para datos de prueba

### 3. Backend/API
- Controllers para cada recurso
- Form Requests para validación
- Resources/Transformers para API
- Políticas de autorización (Policies)
- Servicios de negocio

### 4. Frontend
- Convertir HTMLs a Blade templates
- Sistema de components reutilizables
- Formularios para crear/editar entrenamientos
- Dashboards interactivos
- Gráficos y estadísticas

### 5. Funcionalidades Específicas
- Cálculo automático de métricas (pace, totalizadores)
- Análisis semanal/mensual
- Sistema de compartir con coach
- Gestión de grupos de entrenamiento
- Panel del coach para ver alumnos
- Exportación de datos

### 6. Integraciones Futuras (opcional)
- Strava API
- Relojes GPS (Garmin, Polar, etc.)
- Exportación a formatos estándar (GPX, TCX)

---

## Decisiones de Arquitectura Tomadas

1. **Multi-tenancy por Business**: Permite tanto usuarios individuales (business_id null) como grupos de entrenamiento
2. **Sistema de Roles**: Campo `role` en users para diferenciar entre 'user', 'coach', 'admin', etc.
3. **Email único por business**: Permite que el mismo email se registre en diferentes grupos
4. **Diseño dark mode**: Estética moderna y dev-friendly
5. **Laravel puro**: Sin frontend framework (por ahora), usando Blade

---

## Próximos Pasos Sugeridos

Ver archivo `ROADMAP.md` para el plan de desarrollo detallado.

---

## Notas Técnicas

### Convenciones
- Usar español para nombres de entidades del dominio cuando sea más natural
- Mantener inglés para nombres técnicos de Laravel (controllers, models, etc.)
- Documentar todo en español

### Stack Tecnológico
- **Backend**: Laravel 11.x
- **Base de Datos**: MySQL (via Laragon)
- **Frontend**: Blade + CSS vanilla (por ahora)
- **Autenticación**: Sistema custom multi-tenant

### Git
- Rama actual: `main`
- Commits descriptivos en español
- Documentar cambios importantes en este archivo
