# MiEntreno - Roadmap de Desarrollo

Plan de desarrollo en fases iterativas, priorizando funcionalidad core sobre features avanzadas.

---

## Fase 1: Foundation & Core Features (2-3 semanas)

**Objetivo**: Sistema funcional para que un corredor individual registre y visualice sus entrenamientos.

### 1.1 Base de Datos - Workouts & Races
- [x] Crear migración `create_workouts_table` ✅
- [x] Crear migración `create_races_table` ✅ (base)
- [ ] Crear migración `create_goals_table`
- [x] Ejecutar migraciones ✅
- [x] Crear seeders con datos de ejemplo ✅

### 1.2 Modelos y Relaciones
- [x] Modelo `Workout` con relaciones y scopes ✅
- [x] Modelo `Race` con relaciones ✅ (base)
- [ ] Modelo `Goal` con relaciones
- [x] Completar modelo `User` con relaciones nuevas ✅
- [ ] Tests de relaciones básicas

### 1.3 Controllers - Workouts
- [x] `WorkoutController` (CRUD completo) ✅
- [x] Validación inline en controller (sin Form Requests separados) ✅
- [x] Rutas RESTful para workouts ✅
- [x] Ownership validation en controller ✅

### 1.4 Views - Sistema de Templates
- [x] Convertir `landing/index.html` a `welcome.blade.php` ✅
- [x] Convertir `landing/login.html` a `auth/login.blade.php` ✅
- [x] Convertir `landing/register.html` a `auth/register.blade.php` ✅
- [x] Crear layout base `layouts/app.blade.php` (con sidebar) ✅
- [x] Crear layout `layouts/guest.blade.php` ✅
- [ ] Crear components Blade reutilizables:
  - [ ] `<x-card>`
  - [ ] `<x-metric-card>`
  - [ ] `<x-button>`
  - [ ] `<x-sidebar-link>`

### 1.5 Dashboard Runner
- [x] Convertir `landing/dashboard.html` a `dashboard.blade.php` ✅
- [x] Métricas semanales (km, tiempo, pace, sesiones) ✅
- [x] Lista de entrenamientos recientes ✅
- [x] Integrar datos reales desde BD ✅

### 1.6 Formularios de Entrenamientos
- [x] Vista `workouts/create.blade.php` ✅
- [x] Vista `workouts/edit.blade.php` ✅
- [x] Vista `workouts/index.blade.php` (lista completa) ✅
- [ ] Vista `workouts/show.blade.php` (detalle) - opcional
- [x] Validación y mensajes de error/éxito ✅

### 1.7 Cálculos y Métricas
- [ ] Service `MetricsService` para cálculos (futuro)
  - [x] Calcular pace promedio ✅ (en modelo)
  - [x] Totalizadores semanales/mensuales ✅ (en controller)
  - [ ] Calcular racha de entrenamientos
- [x] Agregar computed attributes en modelo Workout ✅
- [ ] Tests de cálculos

**Entregable Fase 1**: Un corredor puede registrarse, crear entrenamientos, ver sus métricas semanales/mensuales. ✅ **COMPLETADO**

---

## Fase 2: Races & Goals (1-2 semanas)

**Objetivo**: Gestión de carreras y objetivos.

### 2.1 Races CRUD
- [ ] `RaceController` completo
- [ ] Form Requests para races
- [ ] Policy `RacePolicy`
- [ ] Rutas para races
- [ ] Views:
  - [ ] `races/index.blade.php` (próximas y pasadas)
  - [ ] `races/create.blade.php`
  - [ ] `races/show.blade.php`

### 2.2 Goals CRUD
- [ ] `GoalController` completo
- [ ] Form Requests para goals
- [ ] Policy `GoalPolicy`
- [ ] Views para goals
- [ ] Lógica de progreso automático

### 2.3 Integración Dashboard
- [ ] Widget "Próxima carrera" en dashboard
- [ ] Widget "Objetivos activos" en dashboard
- [ ] Progress bars para objetivos

### 2.4 Vinculación Workout-Race
- [ ] Al crear workout, poder asociarlo a una carrera
- [ ] Marcar workout como "carrera oficial"
- [ ] Auto-completar tiempo real de race desde workout

**Entregable Fase 2**: Sistema completo de gestión de carreras y objetivos para corredor individual.

---

## Fase 3: Multi-tenant Refinement (1 semana)

**Objetivo**: Mejorar experiencia multi-tenant y flujo de usuarios sin business.

### 3.1 Rutas Duales
- [ ] Rutas sin prefijo business para usuarios individuales
- [ ] Middleware para detectar contexto (business vs individual)
- [ ] Redirecciones inteligentes post-login

### 3.2 Landing Pública
- [ ] Landing page pública (sin business) en `/`
- [ ] Página de selección "Crear cuenta individual" vs "Unirse a grupo"
- [ ] Lista pública de businesses (opcional)

### 3.3 Perfil de Usuario
- [ ] Vista de perfil (`profile/edit.blade.php`)
- [ ] Editar datos personales
- [ ] Preferencias (nivel de corredor, etc.)

**Entregable Fase 3**: Sistema funcionando tanto para individuales como para businesses.

---

## Fase 4: Training Groups & Coach Panel (2-3 semanas)

**Objetivo**: Funcionalidades de grupos y panel del coach.

### 4.1 Training Groups Base
- [ ] Migración `create_training_groups_table`
- [ ] Migración pivot `training_group_user` (miembros)
- [ ] Modelo `TrainingGroup` con relaciones
- [ ] Seeders de grupos de ejemplo

### 4.2 Gestión de Grupos (Coach)
- [ ] `TrainingGroupController`
- [ ] Policy `TrainingGroupPolicy` (solo coaches)
- [ ] Views:
  - [ ] `groups/index.blade.php` (lista de grupos del coach)
  - [ ] `groups/create.blade.php`
  - [ ] `groups/show.blade.php` (detalle + miembros)
  - [ ] `groups/members/add.blade.php` (agregar miembros)

### 4.3 Asistencias
- [ ] Migración `create_attendances_table`
- [ ] Modelo `Attendance`
- [ ] `AttendanceController`
- [ ] Vista para marcar asistencias del día
- [ ] Reporte de asistencias por alumno

### 4.4 Panel Coach
- [ ] Vista `coach/students.blade.php`
  - [ ] Lista de alumnos de sus grupos
  - [ ] Métricas de cada alumno (km semana, asistencia %)
- [ ] Vista `coach/students/{id}.blade.php`
  - [ ] Detalle completo del alumno
  - [ ] Entrenamientos recientes
  - [ ] Gráficos de evolución
- [ ] Vista `coach/dashboard.blade.php`
  - [ ] Dashboard específico del coach
  - [ ] Totalizadores de grupos
  - [ ] Alumnos destacados/rezagados

### 4.5 Vista Runner en Grupos
- [ ] Widget "Mis grupos" en dashboard runner
- [ ] Ver próximos entrenamientos grupales
- [ ] Marcar asistencia propia

**Entregable Fase 4**: Coaches pueden crear grupos, gestionar alumnos, y ver métricas grupales.

---

## Fase 5: Analytics & Charts (1-2 semanas)

**Objetivo**: Visualización avanzada de datos con gráficos.

### 5.1 Setup Charts
- [ ] Integrar Chart.js o ApexCharts
- [ ] Crear component `<x-chart>` reutilizable
- [ ] Definir paleta de colores para gráficos

### 5.2 Gráficos Runner
- [ ] Gráfico de km por semana (últimas 12 semanas)
- [ ] Gráfico de evolución de pace
- [ ] Distribución de tipos de entrenamiento (donut)
- [ ] Calendario de entrenamientos (heatmap)

### 5.3 Gráficos Coach
- [ ] Comparativa de alumnos (km totales, asistencia)
- [ ] Evolución de un alumno específico
- [ ] Gráfico de asistencias por grupo

### 5.4 Reports Exportables
- [ ] Vista de reporte semanal/mensual (imprimible)
- [ ] Exportar a PDF (usando DomPDF o similar)
- [ ] Exportar entrenamientos a CSV/Excel

**Entregable Fase 5**: Dashboards con visualizaciones ricas de datos.

---

## Fase 6: Training Plans (2 semanas)

**Objetivo**: Planes de entrenamiento creados por coaches.

### 6.1 Base de Datos
- [ ] Migración `create_training_plans_table`
- [ ] Migración `create_training_plan_workouts_table` (workouts planificados)
- [ ] Modelos y relaciones

### 6.2 Creación de Planes (Coach)
- [ ] `TrainingPlanController`
- [ ] Form builder para crear plan semanal
- [ ] Asignar plan a alumno o grupo
- [ ] Duplicar/usar templates de planes

### 6.3 Vista Runner
- [ ] Ver plan asignado en dashboard
- [ ] Comparar workout real vs planificado
- [ ] Marcar sesiones como completadas

### 6.4 Analytics de Planes
- [ ] % de cumplimiento del plan
- [ ] Desviaciones respecto al plan
- [ ] Alertas para coach si alumno no cumple

**Entregable Fase 6**: Sistema completo de planes de entrenamiento.

---

## Fase 7: Integraciones & API (2-3 semanas)

**Objetivo**: API REST y posibles integraciones externas.

### 7.1 API REST
- [ ] Setup Laravel Sanctum
- [ ] API Resources para todos los modelos
- [ ] Endpoints API RESTful
- [ ] Documentación de API (Swagger/Scribe)
- [ ] Rate limiting

### 7.2 Integración Strava (Opcional)
- [ ] OAuth con Strava
- [ ] Importar actividades desde Strava
- [ ] Sincronización automática

### 7.3 Exportación GPS
- [ ] Formato GPX
- [ ] Formato TCX
- [ ] Importar desde archivo

### 7.4 Mobile App (Futuro)
- [ ] Considerar React Native / Flutter
- [ ] Consumir API de Laravel

**Entregable Fase 7**: API pública documentada, integraciones opcionales.

---

## Fase 8: Polish & Production (1-2 semanas)

**Objetivo**: Preparar para producción.

### 8.1 Testing Completo
- [ ] Feature tests de todos los controllers
- [ ] Tests de policies
- [ ] Tests de métricas
- [ ] Test de multi-tenancy

### 8.2 Performance
- [ ] Optimizar queries (eager loading)
- [ ] Implementar caching de métricas
- [ ] Indexes en BD
- [ ] Lazy loading de imágenes/gráficos

### 8.3 UX/UI Final
- [ ] Loading states
- [ ] Empty states mejorados
- [ ] Toasts/notificaciones
- [ ] Validación client-side (Alpine.js)

### 8.4 Deploy
- [ ] Configurar servidor (VPS / Laravel Forge)
- [ ] Setup CI/CD (GitHub Actions)
- [ ] Backups automáticos
- [ ] Monitoreo (Laravel Telescope / Sentry)

### 8.5 Documentación
- [ ] README completo
- [ ] Guía de instalación
- [ ] Guía de uso para coaches
- [ ] Guía de contribución

**Entregable Fase 8**: Aplicación production-ready.

---

## Features Futuras (Backlog)

- Integración con relojes GPS (Garmin, Polar, Coros)
- Retos entre usuarios
- Tabla de posiciones del grupo
- Análisis de zonas de frecuencia cardíaca
- Recomendaciones automáticas de entrenamientos (ML)
- App móvil nativa
- Integración con calendarios (Google Calendar)
- Notificaciones push
- Chat coach-alumno
- Marketplace de planes de entrenamiento

---

## Metodología

1. **Desarrollo iterativo**: Completar cada fase antes de avanzar
2. **Testing continuo**: Escribir tests a medida que se desarrolla
3. **Documentación paralela**: Actualizar docs con cada cambio importante
4. **Commits frecuentes**: Commits pequeños y descriptivos
5. **Revisión de código**: Auto-review antes de commit

---

## Tracking de Sesiones

Ver archivo `SESSION_LOG.md` para registro detallado de cada sesión de desarrollo.

---

## Estimación Total

- **MVP (Fases 1-3)**: ~4-6 semanas
- **Full Features (Fases 1-6)**: ~10-14 semanas
- **Production Ready (Fases 1-8)**: ~12-16 semanas

Tiempos estimados para desarrollo part-time (10-15 horas/semana).

---

## Prioridades

1. **Must Have (MVP)**:
   - Fase 1: Workouts CRUD
   - Fase 2: Races & Goals
   - Fase 3: Multi-tenant
   - Fase 4: Training Groups básico

2. **Should Have**:
   - Fase 4: Coach panel completo
   - Fase 5: Analytics
   - Fase 6: Training Plans

3. **Nice to Have**:
   - Fase 7: API & Integraciones
   - Features futuras del backlog

---

**Actualización**: 2025-11-18
