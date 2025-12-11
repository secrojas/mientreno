# MiEntreno - Documentación del Proyecto

Bienvenido a la documentación técnica de **MiEntreno**, una aplicación web para registro y análisis de entrenamientos de running, diseñada por y para personas que mezclan el running con la programación.

---

## Inicio Rápido

### Para Desarrolladores Nuevos

1. **Lee primero**: `PROJECT_STATUS.md` - para entender dónde estamos
2. **Arquitectura**: `ARCHITECTURE.md` - para entender cómo está diseñado todo
3. **Plan de trabajo**: `ROADMAP.md` - para ver qué sigue
4. **Log de sesiones**: `SESSION_LOG.md` - para seguir el progreso

### Setup Local

```bash
# Clonar repo
git clone [url]
cd mientreno

# Instalar dependencias
composer install
npm install

# Configurar .env
cp .env.example .env
php artisan key:generate

# Base de datos
# Crear BD en Laragon/MySQL
php artisan migrate
php artisan db:seed

# Correr servidor
php artisan serve
```

---

## Estructura de Documentación

### `PROJECT_STATUS.md`
**Qué es**: Snapshot del estado actual del proyecto.

**Cuándo leerlo**:
- Al empezar a trabajar en el proyecto
- Después de un tiempo sin trabajar en él
- Para ver qué está implementado y qué no

**Contiene**:
- Funcionalidades ya implementadas
- Funcionalidades pendientes
- Decisiones de arquitectura tomadas
- Stack tecnológico actual

### `ARCHITECTURE.md`
**Qué es**: Diseño técnico completo de la aplicación.

**Cuándo leerlo**:
- Antes de crear nuevos modelos o migraciones
- Al diseñar nuevos features
- Para entender relaciones entre entidades

**Contiene**:
- Modelo de datos (8 entidades con campos y relaciones)
- Lógica de negocio clave
- Estructura de API/endpoints
- Consideraciones de seguridad y performance
- Testing strategy

### `ROADMAP.md`
**Qué es**: Plan de desarrollo en 8 fases con tareas concretas.

**Cuándo leerlo**:
- Al iniciar una nueva sesión de desarrollo
- Para decidir qué trabajar next
- Para estimar tiempos

**Contiene**:
- 8 fases de desarrollo (Foundation → Production)
- Checklist detallado de tareas
- Entregables de cada fase
- Estimaciones de tiempo
- Prioridades (Must/Should/Nice to have)

### `SESSION_LOG.md`
**Qué es**: Bitácora de todas las sesiones de desarrollo.

**Cuándo usarlo**:
- Al TERMINAR cada sesión de desarrollo
- Para ver qué se hizo en sesiones anteriores
- Para tracking de progreso

**Contiene**:
- Log de cada sesión con fecha
- Qué se hizo, problemas, decisiones
- Archivos modificados/creados
- Próximos pasos
- Tiempo invertido

**IMPORTANTE**: Actualizar este archivo al final de CADA sesión.

### `INVITATIONS.md`
**Qué es**: Documentación del sistema de invitaciones con tokens.

**Cuándo leerlo**:
- Al implementar features de grupos/businesses
- Para entender cómo vincular usuarios a businesses
- Para generar links de invitación

**Contiene**:
- Cómo funciona el sistema de tokens
- Cómo generar invitaciones (UI y comando artisan)
- Formatos de URL y tokens
- Flujos de registro

### `WORKOUTS.md`
**Qué es**: Documentación completa del sistema de entrenamientos.

**Cuándo leerlo**:
- Al trabajar con workouts
- Para entender cálculos de pace y métricas
- Para ver ejemplos de uso del modelo y vistas

**Contiene**:
- Modelo de datos de workouts
- Relaciones y scopes
- Controller y rutas
- Vistas y formularios
- Cálculos (pace, duración)
- Dashboard integration
- Ejemplos de uso

---

## Flujo de Trabajo Recomendado

### Al iniciar una sesión de desarrollo

1. **Lee** `SESSION_LOG.md` última entrada → ver "Próximos pasos"
2. **Abre** `ROADMAP.md` → identifica tareas de la fase actual
3. **Ejecuta** tests → asegúrate que todo está verde
4. **Trabaja** en las tareas seleccionadas
5. **Commitea** frecuentemente con mensajes descriptivos
6. **Actualiza** `SESSION_LOG.md` al terminar

### Al crear nuevas entidades

1. **Revisa** `ARCHITECTURE.md` → verifica el diseño
2. **Crea** migración
3. **Crea** modelo con relaciones
4. **Crea** seeder
5. **Crea** tests básicos
6. **Documenta** cualquier cambio a la arquitectura

### Al completar una fase

1. **Verifica** checklist en `ROADMAP.md`
2. **Ejecuta** suite de tests completa
3. **Actualiza** `PROJECT_STATUS.md` con nuevo estado
4. **Documenta** en `SESSION_LOG.md`
5. **Commitea** y tagea release (opcional)

---

## Conceptos Clave del Proyecto

### 1. Multi-tenancy

MiEntreno soporta dos modos de uso:

- **Usuario Individual** (business_id = null):
  - Corredor que usa la app solo para sí mismo
  - URL: `/dashboard`, `/workouts`, etc.
  - No ve features de grupos

- **Usuario en Business**:
  - Pertenece a un grupo/equipo de entrenamiento
  - URL: `/{business_slug}/dashboard`, etc.
  - Ve features de grupos, coaches, etc.

### 2. Roles

- **runner**: Corredor estándar (registra entrenamientos, carreras, objetivos)
- **coach**: Entrenador (+ acceso a alumnos, grupos, planes)
- **admin**: Administrador del business (+ configuraciones del business)

### 3. Entidades Core

```
User → tiene muchos → Workouts, Races, Goals
Business → tiene muchos → Users, TrainingGroups
TrainingGroup → tiene muchos → Users (miembros), Attendances
```

Ver diagrama completo en `ARCHITECTURE.md`.

### 4. Métricas Calculadas

- **avg_pace**: Se calcula como `duration / distance` (segundos por km)
- **Totalizadores**: Se agregan por semana/mes/año usando Carbon
- **Progreso de objetivos**: Se calcula comparando workouts vs target

---

## Convenciones de Código

### Nombres

- **Español** para dominio: `entrenamientos`, `carreras`, `objetivos` (en docs y UI)
- **Inglés** para código: `workouts`, `races`, `goals` (en código Laravel)
- **Commits** en español
- **Comentarios** en español
- **Docs** en español

### Estructura de Archivos

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/v1/          # Autenticación custom
│   │   ├── WorkoutController.php
│   │   ├── RaceController.php
│   │   └── ...
│   ├── Requests/
│   │   ├── StoreWorkoutRequest.php
│   │   └── ...
│   └── Resources/
│       ├── WorkoutResource.php
│       └── ...
├── Models/
│   ├── User.php
│   ├── Business.php
│   ├── Workout.php
│   └── ...
├── Services/               # Lógica de negocio
│   ├── MetricsService.php
│   └── ...
└── Policies/
    ├── WorkoutPolicy.php
    └── ...

resources/
└── views/
    ├── layouts/
    │   ├── app.blade.php       # Layout con sidebar
    │   └── guest.blade.php     # Layout sin auth
    ├── components/
    │   ├── card.blade.php
    │   ├── metric-card.blade.php
    │   └── ...
    ├── workouts/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   └── ...
    └── ...

docs/                       # ESTA CARPETA
├── README.md               # Este archivo
├── PROJECT_STATUS.md
├── ARCHITECTURE.md
├── ROADMAP.md
├── SESSION_LOG.md
├── INVITATIONS.md
├── WORKOUTS.md
└── RECOMENDACIONES.md
```

### Testing

- **Feature tests**: Para controllers y flujos completos
- **Unit tests**: Para modelos, services, cálculos
- Ejecutar: `php artisan test`

### Git

```bash
# Commits descriptivos en español
git commit -m "feat(workouts): agregar CRUD de entrenamientos"
git commit -m "fix(metrics): corregir cálculo de pace promedio"
git commit -m "docs: actualizar session log sesión 02"

# Prefijos:
# feat: nueva funcionalidad
# fix: corrección de bug
# docs: cambios en documentación
# refactor: refactorización sin cambio de funcionalidad
# test: agregar o modificar tests
# chore: tareas de mantenimiento
```

---

## Estado Actual (Ver PROJECT_STATUS.md para detalles)

- **Fase**: Fase 1 Completada ✅
- **Funcionalidades**:
  - ✅ Autenticación simplificada (/login, /register, /dashboard)
  - ✅ Sistema de invitaciones con tokens
  - ✅ CRUD completo de Workouts
  - ✅ Dashboard con métricas reales
  - ✅ Cálculo automático de pace
- **Base de datos**: `users`, `businesses`, `workouts`, `races` (base), `training_groups` (base)
- **Frontend**: Blade templates funcionando con diseño custom
- **Siguiente**: Fase 2 - Implementar CRUD de Races y Goals

**Credenciales de prueba:**
- Email: `atleta@test.com`
- Password: `password`
- Datos: 13 workouts (142.5 km en 4 semanas)

---

## Contacto y Contribución

### Autor
Sebastián Rojas ([@srojasweb](https://srojasweb.dev))

### Filosofía del Proyecto

MiEntreno nace de la intersección entre dos pasiones:
- **Running**: Deporte de constancia, métricas y superación personal
- **Programación**: Construir soluciones elegantes a problemas reales

El objetivo es crear una herramienta que:
1. Sea útil para runners reales (no solo un CRUD genérico)
2. Tenga código limpio y bien documentado
3. Sirva como portfolio de buenas prácticas Laravel
4. Se pueda usar realmente en grupos de entrenamiento

### Valores

- **Simplicidad**: Empezar simple, agregar complejidad solo cuando se necesite
- **Documentación**: Si no está documentado, no existe
- **Testing**: Code without tests is broken by design
- **Iteración**: MVP rápido, mejora continua

---

## Recursos Útiles

### Laravel
- [Documentación oficial](https://laravel.com/docs)
- [Laracasts](https://laracasts.com) - Videos de aprendizaje

### Running/Métricas
- Fórmulas de pace, ritmo, zonas de frecuencia cardíaca
- Metodologías de entrenamiento (Jack Daniels, Lydiard, etc.)

### Diseño
- HTMLs en `landing/` como referencia
- Paleta de colores en CSS custom properties

---

## Licencia

[Por definir]

---

**Última actualización**: 2025-12-11
**Versión del proyecto**: 0.2.0 (Fase 1 Completada - MVP Workouts)
