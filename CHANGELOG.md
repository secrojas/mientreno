# Changelog - MiEntreno

Todos los cambios notables del proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/).

---

## [Unreleased]

### Pendiente
- SPRINT 3: Training Groups con CRUD completo
- SPRINT 4: Rutas multi-tenant con prefijo `/{business}`
- SPRINT 5: Sistema de suscripciones y límites por plan

---

## [2025-12-18] - SPRINT 2: Gestión de Business

### ✨ Agregado
- **BusinessController** con CRUD completo (7 métodos)
  - `index()` - Redirige a show o create según tenga business
  - `create()` - Formulario crear business
  - `store()` - Guardar con auto-asignación al coach
  - `show()` - Detalle con estadísticas y alumnos
  - `edit()` - Formulario edición
  - `update()` - Actualizar información
  - `destroy()` - Desactivar (soft delete)

- **BusinessPolicy** con reglas de autorización:
  - Solo coaches/admins pueden gestionar businesses
  - Solo el owner puede ver/editar su business
  - Solo coaches SIN business pueden crear uno

- **3 vistas Blade para gestión de Business:**
  - `coach/business/create.blade.php` - Formulario de creación
  - `coach/business/show.blade.php` - Detalle del negocio
  - `coach/business/edit.blade.php` - Edición del negocio

- **7 rutas nuevas:**
  - `GET /coach/business` → index
  - `POST /coach/business` → store
  - `GET /coach/business/create` → create
  - `GET /coach/business/{business}` → show
  - `GET /coach/business/{business}/edit` → edit
  - `PUT /coach/business/{business}` → update
  - `DELETE /coach/business/{business}` → destroy

### 🔧 Modificado
- **Migración:** `add_fields_to_businesses_table`
  - Agregado `owner_id` (FK a users)
  - Agregado `description` (text)
  - Agregado `level` (string: beginner/intermediate/advanced)
  - Agregado `schedule` (json)
  - Agregado `is_active` (boolean)

- **Modelo Business:**
  - Relaciones: `owner()`, `runners()`
  - Auto-generación de slug único con boot event
  - Accessor: `getLevelLabelAttribute()`

- **Sidebar:**
  - Nuevo link "Mi Negocio" en sección Coaching
  - Highlight activo en rutas business.*

- **Dashboard Coach:**
  - Link funcional "Crear mi negocio" cuando no existe business

### 📝 Documentación
- Actualizado `PROJECT_STATUS.md` con sección "19. Sistema de Coach - Gestión de Business"
- Actualizado `PLAN_DESARROLLO_2025.md` marcando SPRINT 2 como completado

### 🎯 Beneficios
- Coaches pueden crear su negocio desde UI
- Gestión completa con CRUD funcional
- Auto-asignación bidireccional automática
- Validaciones robustas en backend
- Políticas de autorización estrictas
- Lista de alumnos con métricas

**Commit:** ef14f94 - `feat(coach): implementar gestión completa de Business (SPRINT 2)`

---

## [2025-12-18] - SPRINT 1: Dashboard Diferenciado por Rol

### ✨ Agregado
- **CoachDashboardController** (`app/Http/Controllers/Coach/DashboardController.php`)
  - Métricas específicas para coaches
  - Total de alumnos del business
  - Alumnos activos esta semana
  - Total de entrenamientos y kilómetros del grupo
  - Top 3 alumnos por distancia semanal
  - Alumnos inactivos (2+ semanas sin entrenar)
  - Actividad reciente de todos los alumnos

- **Vista Coach Dashboard** (`resources/views/coach/dashboard.blade.php`)
  - 4 metric cards con estadísticas clave
  - Panel de actividad reciente con nombre de alumno
  - Top 3 alumnos de la semana
  - Alumnos inactivos con alertas
  - Diseño consistente con dashboard runner

- **Ruta coach dashboard:**
  - `GET /coach/dashboard` → coach.dashboard

### 🔧 Modificado
- **LoginController** (v1 y AuthenticatedSessionController)
  - Redirección inteligente por rol
  - Coaches/Admins → `/coach/dashboard`
  - Runners → `/dashboard`

- **Sidebar** (`resources/views/layouts/app.blade.php`)
  - Link dinámico según rol:
    - Coaches ven "Dashboard Coach"
    - Runners ven "Dashboard"
  - Sección "Coaching" visible solo para coaches/admins

### 📝 Documentación
- Actualizado `PROJECT_STATUS.md` con sección "18. Sistema de Coach - Dashboard Diferenciado"
- Actualizado `PLAN_DESARROLLO_2025.md` marcando SPRINT 1 como completado

### 🎯 Beneficios
- Experiencia diferenciada por rol
- Coaches pueden ver métricas de sus alumnos
- Identificación rápida de alumnos inactivos
- Navegación intuitiva según tipo de usuario

**Commit:** d66b6c2 - `feat(coach): implementar dashboard diferenciado por rol (SPRINT 1)`

---

## [2025-12-17] - Sistema de Perfil de Usuario

### ✨ Agregado
- Campos de perfil específicos para corredores en tabla `users`
- Sistema de subida de avatar con preview
- Cálculo automático de edad e IMC
- Vista de perfil con diseño Athletic Editorial

### 🔧 Modificado
- Sidebar reorganizado con sección "Cuenta"
- Botón logout movido desde footer a menu principal

**Ver:** `PROJECT_STATUS.md` sección 17 para más detalles

---

## [2025-12-15] - Workout Reports: Links Compartibles (Fase 3)

### ✨ Agregado
- Sistema de tokens únicos para compartir reportes
- Tabla `report_shares` con expiración automática
- Tracking de vistas de reportes compartidos
- Vistas públicas sin autenticación
- Modal de compartir con copy-to-clipboard

**Ver:** `PROJECT_STATUS.md` sección 14 para más detalles

---

## [2025-12-15] - Workout Reports: Exportación PDF (Fase 2)

### ✨ Agregado
- Generación de PDF con DomPDF v3.1.1
- Templates optimizados para impresión
- Botones de descarga en vistas
- Nombres de archivo descriptivos

**Ver:** `PROJECT_STATUS.md` sección 14 para más detalles

---

## [2025-12-15] - Workout Reports: Core Views (Fase 1)

### ✨ Agregado
- ReportController con métodos weekly() y monthly()
- ReportService con lógica de cálculos
- Vistas Blade para reportes
- Componentes reutilizables (report-card, metric-comparison, workout-table)
- Navegación entre períodos
- Insights automáticos

**Ver:** `PROJECT_STATUS.md` sección 14 para más detalles

---

## [2025-12-12] - Races & Goals + UX Improvements

### ✨ Agregado
- Sistema de Carreras (Races) con CRUD completo
- Sistema de Objetivos (Goals) con 4 tipos
- GoalProgressService para cálculo automático
- Forms dinámicos sin JSON manual
- Vinculación Workouts → Races

**Ver:** `PROJECT_STATUS.md` secciones 11, 12, 13 para más detalles

---

## Versiones Anteriores

**Ver:** `PROJECT_STATUS.md` para historial completo de funcionalidades implementadas desde el inicio del proyecto.

---

## Leyenda

- ✨ Agregado: Nuevas funcionalidades
- 🔧 Modificado: Cambios en funcionalidades existentes
- 🐛 Corregido: Bugs resueltos
- 🗑️ Eliminado: Funcionalidades removidas
- 📝 Documentación: Cambios en docs
- 🎯 Beneficios: Impacto de los cambios
