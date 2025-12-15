# MiEntreno - Estado del Proyecto

**Fecha de inicio**: Noviembre 2025
**Stack**: Laravel 11.x
**Concepto**: Aplicación de registro y análisis de entrenamientos de running que mezcla el mundo del desarrollo con el running.

---

## Estado Actual (2025-12-15)

### ✨ FASE 2 COMPLETADA - Races & Goals ✅
### ✨ UX IMPROVEMENTS COMPLETADAS ✅
### ✨ WORKOUT REPORTS - FASE 3 COMPLETADA ✅ (Links Compartibles)

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

#### 7. Funcionalidad de Workouts ✅

**FASE 1 COMPLETADA AL 100%** ✅

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

#### 8. Components Blade Reutilizables ✅

**Creados 3 componentes:**
- `<x-card>`: Card genérico con título, subtítulo y headerAction
- `<x-metric-card>`: Card especializado para métricas con accent colors
- `<x-button>`: Botón con 4 variantes (primary, secondary, ghost, danger) y 3 tamaños

**Ventajas:**
- Código más limpio y mantenible
- Consistencia visual
- Reutilizables en toda la app

#### 9. MetricsService (Separación de Lógica) ✅

**Archivo:** `app/Services/MetricsService.php`

**10 métodos implementados:**
- `getWeeklyMetrics()`, `getMonthlyMetrics()`, `getYearlyMetrics()`, `getTotalMetrics()`
- `formatDuration()`, `formatPace()`
- `getWorkoutTypeDistribution()`, `calculateStreak()`
- `getRecentWorkouts()`, `compareWeekToWeek()`

**Beneficios:**
- Lógica de negocio separada de controllers
- Métodos reutilizables
- Preparado para caching
- Más fácil de testear

#### 10. Filtros y Búsqueda en Workouts ✅

**Filtros implementados:**
- Por tipo de entrenamiento (easy_run, intervals, etc.)
- Por rango de fechas (desde/hasta)
- Búsqueda por notas (LIKE)
- Combinación de múltiples filtros
- Paginación mantiene parámetros con `appends()`

**UI:**
- Formulario de filtros con 4 inputs + botones
- Botón "Limpiar" (solo aparece con filtros activos)
- URLs shareables (GET parameters)

#### 11. Sistema de Carreras (Races) ✅

**FASE 2 - RACES COMPLETADA** ✅

**Modelo Race:**
- Campos completos: name, distance, date, location, target_time, actual_time, position, status, notes
- 4 Scopes: upcoming(), completed(), past(), forUser()
- 7 Accessors: formatted_target_time, formatted_actual_time, days_until, status_label, distance_label
- Helpers estáticos: statusOptions(), commonDistances()

**RaceController (CRUD completo):**
- index: Separación de carreras upcoming y past
- create/store: Formulario con distancias comunes y validación
- edit/update: Edición con campos adicionales (actual_time, position) para carreras completadas
- destroy: Eliminación con ownership validation

**Vistas Blade:**
- `races/index.blade.php`: Lista con secciones separadas (próximas y pasadas)
- `races/create.blade.php`: Formulario con selector de distancia y tiempo objetivo
- `races/edit.blade.php`: Edición con campos condicionales según status

**Integración Dashboard:**
- Card "Próxima carrera" con cuenta regresiva de días
- Muestra nombre, distancia y fecha de la próxima carrera

**Seeder con datos realistas:**
- 2 carreras próximas (10K en 15 días, Media Maratón en 45 días)
- 3 carreras completadas con tiempos y posiciones reales

#### 12. Sistema de Objetivos (Goals) ✅

**FASE 2 - GOALS COMPLETADA** ✅

**Modelo Goal:**
- Campos: type (race/distance/pace/frequency), title, description, target_value (JSON), progress (JSON)
- 5 Scopes: active(), completed(), byType(), forUser(), dueSoon()
- 4 Accessors: type_label, status_label, days_until, progress_percentage
- Helpers complejos: getTargetDescription() con match statement para diferentes tipos

**GoalController (CRUD completo):**
- index: Lista de objetivos con filtros por status y type
- create/store: Con campos dinámicos sin JSON manual (UX mejorada)
- edit/update: Edición con pre-carga de valores y cálculo automático de progreso
- destroy: Eliminación con ownership validation

**Vistas Blade:**
- `goals/index.blade.php`: Lista con badges de tipo y barras de progreso
- `goals/create.blade.php`: Formulario dinámico con JavaScript que genera JSON automáticamente
- `goals/edit.blade.php`: Edición con campos específicos según tipo de goal

**4 Tipos de Goals:**
1. **Race**: Tiempo objetivo para una carrera específica (vinculado a Race)
2. **Distance**: Distancia total por período (ej: 50km/semana)
3. **Pace**: Pace promedio objetivo (ej: 5:00/km)
4. **Frequency**: Número de sesiones por período (ej: 4 entrenamientos/semana)

**Integración Dashboard:**
- Panel "Objetivos Activos" con los 3 más recientes
- Progress bars visuales con porcentajes
- Badges con tipo de objetivo y días restantes

**Seeder con datos variados:**
- 5 objetivos diferentes tipos (4 activos, 1 completado)
- Progreso realista basado en entrenamientos

#### 13. UX Improvements - Forms & Automation ✅

**MEJORAS DE UX COMPLETADAS** ✅

**A) Formularios de Goals sin JSON manual:**
- Campos dinámicos que cambian según tipo seleccionado
- Race: 3 inputs (horas, minutos, segundos) → genera JSON automáticamente
- Distance: Distancia + período dropdown (semana/mes)
- Pace: Minutos y segundos → calcula pace en segundos
- Frequency: Sesiones + período dropdown
- JavaScript genera el JSON en background sin intervención del usuario
- En edit: Pre-carga automática de valores desde JSON existente

**B) Vinculación Workouts → Races:**
- Selector de carreras próximas en formularios de workouts (create y edit)
- Campo "¿Es para una carrera específica?" con dropdown
- Validación de race_id en WorkoutController
- Permite linkear entrenamientos a carreras para mejor tracking

**C) Cálculo Automático de Progreso:**
- **Nuevo servicio:** `GoalProgressService.php`
- 4 algoritmos de cálculo automático:
  1. **Race Progress**: Busca workout vinculado a la carrera, compara tiempos
  2. **Distance Progress**: Suma distancia total en el período (semana/mes)
  3. **Pace Progress**: Promedio de últimos 5 workouts, escala progresiva
  4. **Frequency Progress**: Cuenta sesiones en período especificado
- Integración automática:
  - GoalController: Recalcula al crear/actualizar goal
  - WorkoutController: Recalcula al crear/actualizar/eliminar workout
- Método `updateUserGoalsProgress()`: Actualiza todos los goals activos del usuario
- **Testing completado:** Todos los cálculos funcionando correctamente

**Beneficios de UX:**
- ✅ No más inputs manuales de JSON
- ✅ Progreso calculado automáticamente basado en entrenamientos reales
- ✅ Mejor seguimiento de preparación para carreras
- ✅ Experiencia de usuario fluida y profesional

#### 14. Sistema de Reportes (Workout Reports) 📊

**ESTADO: FASE 1, 2 Y 3 COMPLETADAS** ✅

**Propósito:**
Sistema para generar reportes semanales y mensuales de entrenamientos con exportación a PDF y links compartibles, pensado principalmente para compartir progreso con entrenadores.

**Documento de diseño:** `docs/WORKOUT_REPORTS.md` (completado)

**Funcionalidades Implementadas:**

**A) Vistas de Reportes:** ✅
- `/reports/weekly` - Resumen semanal con navegación anterior/siguiente
- `/reports/monthly` - Resumen mensual con navegación anterior/siguiente
- Navegación temporal funcional (semanas/meses anteriores y siguientes)
- Link en sidebar del dashboard

**B) Contenido de Reportes:** ✅
- **Métricas Generales:**
  - Total km, tiempo, sesiones, pace promedio, FC promedio, desnivel
- **Distribución por Tipo:**
  - Barras de progreso mostrando tipos de entrenamientos
  - Porcentajes y distancias por categoría
- **Comparativas:**
  - Semana/mes actual vs período anterior
  - Tendencias visuales (mejorando/estable/bajando)
  - Diferencias absolutas y porcentuales
- **Insights Automáticos:**
  - Mejor entrenamiento del período
  - Rachas de días consecutivos
  - Pace más rápido
  - Tipo de entrenamiento más frecuente
  - Sesión más larga
- **Detalle de Entrenamientos:**
  - Tabla completa con todos los workouts del período
  - Incluye notas si existen

**C) Exportación PDF:** ✅
- Generación de PDF con librería DomPDF v3.1.1
- Diseño profesional optimizado para impresión
- Incluye logo, métricas, comparativas y tablas
- Templates separados para semanal y mensual
- Nombres de archivo descriptivos:
  - `reporte-semanal-{year}-semana-{week}.pdf`
  - `reporte-mensual-{mes}-{year}.pdf`
- Botón de descarga en ambas vistas
- Paper size A4 portrait

**D) Links Compartibles (Shareable Links):** ✅
- **Sistema de tokens únicos:**
  - Tabla `report_shares` con token de 32 caracteres
  - Expiración automática en 24 horas
  - Tracking de vistas (view_count y last_viewed_at)
  - Prevención de duplicados (retorna share existente si válido)
- **Modelo ReportShare:**
  - `createShare()` - genera o retorna share válido
  - `findValidByToken()` - busca shares no expirados
  - `incrementViews()` - tracking de visualizaciones
  - `getShareUrl()` - genera URL completa
  - `cleanupExpired()` - limpieza de shares vencidos
  - Scopes: valid(), expired()
- **ReportController métodos de sharing:**
  - `shareWeekly()` - genera link compartible semanal
  - `shareMonthly()` - genera link compartible mensual
  - `showShared()` - muestra reporte público desde token
- **Vistas públicas:**
  - Layout público sin sidebar (public-layout.blade.php)
  - `reports/public/weekly.blade.php` - vista pública semanal
  - `reports/public/monthly.blade.php` - vista pública mensual
  - Aviso destacado con usuario, fecha y contador de vistas
  - Mismo diseño y estética que vistas privadas
- **UI de compartir:**
  - Botón "🔗 Compartir" con color fuscia en ambas vistas
  - Modal JavaScript con URL y fecha de expiración
  - Funcionalidad copiar al portapapeles
  - Feedback visual en botones
- **Rutas implementadas:**
  - POST `/reports/weekly/{year}/{week}/share` (protegida)
  - POST `/reports/monthly/{year}/{month}/share` (protegida)
  - GET `/share/{token}` (pública, sin autenticación)
- **Características:**
  - Links expiran en 24 horas automáticamente
  - No se generan duplicados si existe share válido
  - Tracking completo de vistas
  - Acceso público sin necesidad de login
  - URLs shareables para entrenadores

**Implementación Técnica:**

**Backend:**
- **ReportService** (`app/Services/ReportService.php`):
  - `getWeeklyReport()` - Reporte semanal completo
  - `getMonthlyReport()` - Reporte mensual completo
  - `calculateSummary()` - Métricas del período
  - `getWorkoutDistribution()` - Distribución por tipo con %
  - `getComparison()` - Comparativas período a período
  - `getInsights()` - 5 tipos de insights automáticos
  - `calculatePeriodStreak()` - Racha de días consecutivos

- **ReportController** (`app/Http/Controllers/ReportController.php`):
  - `index()` - Redirect a weekly
  - `weekly()` - Vista semanal
  - `monthly()` - Vista mensual
  - `exportWeeklyPDF()` - Exportación PDF semanal
  - `exportMonthlyPDF()` - Exportación PDF mensual

**Frontend:**
- **Componentes Blade** (reutilizables):
  - `<x-report-card>` - Card para secciones del reporte
  - `<x-metric-comparison>` - Comparativas con flechas de tendencia
  - `<x-workout-table>` - Tabla completa de entrenamientos

- **Vistas**:
  - `reports/weekly.blade.php` - Vista semanal web
  - `reports/monthly.blade.php` - Vista mensual web
  - `reports/pdf/weekly.blade.php` - Template PDF semanal
  - `reports/pdf/monthly.blade.php` - Template PDF mensual

**Rutas Implementadas:**
```php
/reports                           → Vista principal
/reports/weekly                    → Semana actual
/reports/weekly/{year}/{week}     → Semana específica
/reports/weekly/{year}/{week}/pdf → PDF semanal
/reports/monthly                   → Mes actual
/reports/monthly/{year}/{month}   → Mes específico
/reports/monthly/{year}/{month}/pdf → PDF mensual
```

**Fases Completadas:**

**✅ Fase 1 - Core Report Views (Completada 2025-12-15):**
- ReportController con métodos weekly() y monthly() ✅
- ReportService con lógica de cálculos ✅
- Vistas Blade para reportes semanales y mensuales ✅
- Componentes reutilizables (report-card, metric-comparison, workout-table) ✅
- Navegación entre períodos ✅
- Insights automáticos ✅
- Tiempo real: ~3 horas ✅

**✅ Fase 2 - Exportación PDF (Completada 2025-12-15):**
- Instalación y configuración de DomPDF ✅
- Templates PDF optimizados para impresión ✅
- Métodos de exportación en controller ✅
- Botones de descarga en vistas ✅
- Rutas PDF configuradas ✅
- Tiempo real: ~2 horas ✅

**✅ Fase 3 - Links Compartibles (Completada 2025-12-15):**
- Migración y modelo ReportShare ✅
- Sistema de tokens únicos con expiración ✅
- Métodos de sharing en ReportController ✅
- Vistas públicas sin autenticación ✅
- Layout público responsive ✅
- Modal de compartir con copy-to-clipboard ✅
- Tracking de vistas y estadísticas ✅
- Prevención de duplicados ✅
- Testing completo ✅
- Tiempo real: ~3 horas ✅

**Fases Pendientes:**

**Fase 4 - Gráficos y Visualizaciones (⏸️ Pendiente):**
- Integración de Chart.js
- Gráficos de distribución, volumen, evolución
- Visualizaciones interactivas
- Estimación: ~2 horas

**Fase 5 - Comparativas e Insights Avanzados (⏸️ Pendiente):**
- Algoritmos de comparación avanzados
- Insights más sofisticados
- Detección de patrones
- Recomendaciones personalizadas
- Estimación: ~2.5 horas

**Fase 6 - UX Enhancements (⏸️ Pendiente):**
- Dropdown para selección rápida de períodos
- Calendario visual
- Historial de reportes generados
- Cache de reportes (1 hora TTL)
- Estimación: ~2 horas

**Tiempo Estimado Restante:** ~6.5 horas de 15 horas totales

**Beneficios Alcanzados:**
- ✅ Compartir progreso con entrenador de forma profesional
- ✅ Análisis visual de cumplimiento y tendencias
- ✅ Comparativas que motivan a mejorar
- ✅ Insights automáticos sin intervención manual
- ✅ PDF descargable y compartible
- ✅ Navegación intuitiva entre períodos
- ✅ Diseño responsive y profesional
- ✅ Links compartibles con expiración automática
- ✅ Acceso público sin necesidad de login para entrenadores
- ✅ Tracking de vistas de reportes compartidos

**Estado Actual (2025-12-15):**
- ✅ Planificación completa
- ✅ Fase 1 - Core Views implementada
- ✅ Fase 2 - Exportación PDF implementada
- ✅ Fase 3 - Links Compartibles implementada
- ⏸️ Fase 4, 5, 6 pendientes (opcionales)

#### 15. Data Migration & Import Tools 🔄

**Comando de Importación de Workouts** ✅

**Propósito:**
Migración de datos históricos desde proyectos anteriores con diferente esquema de base de datos.

**Implementación:**
- **Comando Artisan:** `workouts:import-from-old-db`
- **Archivo:** `app/Console/Commands/ImportWorkoutsFromOldDb.php`

**Características:**
- Conexión directa a base de datos externa (running-api)
- Mapeo automático de campos entre esquemas diferentes:
  - `training_type_id` → `type` (enum: training_run, easy_run, race)
  - `duration` (TIME) → `duration` (seconds)
  - `distance_km` → `distance`
  - `difficulty` (enum) → `difficulty` (1-5)
  - `title + description` → `notes`
- Cálculo automático de `avg_pace` en tiempo de importación
- Detección de duplicados por `user_id + date`
- Modo dry-run para previsualización sin insertar datos
- Barra de progreso y resumen detallado

**Opciones del comando:**
```bash
--user-id=2          # ID del usuario en BD nueva (default: 2)
--old-user-id=730    # ID del usuario en BD antigua (default: 730)
--dry-run            # Previsualizar sin insertar
--force              # Sobrescribir duplicados
```

**Uso:**
```bash
# Dry-run (previsualización)
php artisan workouts:import-from-old-db --dry-run

# Importación real
php artisan workouts:import-from-old-db --user-id=2 --old-user-id=730

# Sobrescribir duplicados
php artisan workouts:import-from-old-db --force
```

**Resultado:**
- 66 workouts importados exitosamente
- Pace calculado correctamente para todos los registros
- Conversión completa de esquema antiguo a nuevo

#### 16. UI/UX Improvements & Fixes 🎨

**Paginación Personalizada** ✅

**Problema:** Paginación por defecto de Laravel mostraba símbolos HTML grandes y sin estilo consistente
**Solución:**
- Vista de paginación personalizada en `resources/views/vendor/pagination/custom.blade.php`
- Diseño adaptado al dark theme de la aplicación
- Botones "‹ Anterior" y "Siguiente ›" estilizados
- Texto de resultados: "Mostrando X a Y de Z resultados"
- Estados disabled y active con colores del tema
- Usado en listado de workouts con `->links('vendor.pagination.custom')`

**Mejoras de Layout y Espaciado** ✅ (2025-12-15)

**Cambios en `layouts/app.blade.php`:**
1. **Logo aumentado:** De 28px a 42px de altura (+50%)
2. **Sidebar header optimizado:** Padding reducido para mejor aprovechamiento vertical
3. **Contenedor principal ampliado:** De max-width 1120px a 1500px (+34%)

**Cambios en `workouts/index.blade.php`:**
1. **Columna de acciones ampliada:** De 200px a 260px (+30%)
2. **Botones de acción corregidos:** Editar y Eliminar ahora visibles sin cortes
3. **Grid responsive actualizado** para mantener compatibilidad móvil

**Beneficios:**
- ✅ Logo más visible y profesional
- ✅ Mayor espacio para contenido en pantallas amplias
- ✅ Todos los botones de acción completamente visibles
- ✅ Mejor aprovechamiento del espacio disponible

---

## Lo que falta implementar

### 1. Fase 1 - Foundation & Core Features
- ✅ **COMPLETADA AL 100%** (2025-12-12)
- Workouts CRUD completo con filtros y búsqueda
- Components Blade reutilizables
- MetricsService implementado
- Dashboard funcional con datos reales

### 2. Fase 2 - Races & Goals
- ✅ **COMPLETADA AL 100%** (2025-12-12)
- Sistema de Carreras (Races) con CRUD completo
- Sistema de Objetivos (Goals) con 4 tipos diferentes
- UX Improvements: Forms dinámicos sin JSON
- Vinculación Workouts → Races
- Cálculo automático de progreso con GoalProgressService

### 3. Modelos Core de Running (Estado actual)
- ~~`Workout`~~ ✅ **COMPLETADO**
- ~~`Race`~~ ✅ **COMPLETADO**
- ~~`Goal`~~ ✅ **COMPLETADO**
- `TrainingPlan`: Planes de entrenamiento (Fase 6)
- ~~`TrainingGroup`~~ (base creada, falta funcionalidad - Fase 4)
- `Attendance`: Asistencias a entrenamientos grupales (Fase 4)

### 3. Base de Datos
- Migraciones para todos los modelos core
- Relaciones entre modelos
- Seeders para datos de prueba

### 4. Backend/API
- Controllers para cada recurso
- Form Requests para validación
- Resources/Transformers para API
- Políticas de autorización (Policies)
- Servicios de negocio

### 5. Frontend
- Convertir HTMLs a Blade templates
- Sistema de components reutilizables
- Formularios para crear/editar entrenamientos
- Dashboards interactivos
- Gráficos y estadísticas

### 6. Funcionalidades Específicas
- Cálculo automático de métricas (pace, totalizadores)
- Análisis semanal/mensual
- Sistema de compartir con coach
- Gestión de grupos de entrenamiento
- Panel del coach para ver alumnos
- Exportación de datos

### 7. Integraciones Futuras (opcional)
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
