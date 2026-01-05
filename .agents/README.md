# 🤖 Sistema Multi-Agente para MiEntreno

Sistema de coordinación de agentes especializados para optimizar el desarrollo de MiEntreno.

---

## 📚 Tabla de Contenidos

1. [¿Qué es el Sistema Multi-Agente?](#qué-es)
2. [¿Cuándo Usar Qué?](#cuándo-usar-qué)
3. [Agentes Disponibles](#agentes-disponibles)
4. [Workflows Predefinidos](#workflows-predefinidos)
5. [Cómo Usar el Sistema](#cómo-usar-el-sistema)
6. [Ejemplos Prácticos](#ejemplos-prácticos)

---

## ¿Qué es?

El sistema multi-agente coordina **6 agentes especializados** para trabajar en paralelo o secuencialmente según la complejidad de la tarea:

- 🏗️ **Architecture Agent** - Diseño de estructura de datos
- ⚙️ **Backend Agent** - Implementación Laravel
- 🎨 **Frontend Agent** - Vistas Blade + Tailwind
- 🧪 **Testing Agent** - Tests PHPUnit
- 📝 **Documentation Agent** - Mantiene docs actualizadas
- 🚀 **DevOps Agent** - Deploy y CI/CD

---

## ¿Cuándo Usar Qué?

### Nivel 1: Tarea Trivial ⚡
**Complejidad:** Baja (typo, fix pequeño, actualización de doc)

**Estrategia:** Un solo agente especializado

**Ejemplos:**
- Corregir typo en vista → Frontend Agent solo
- Actualizar SESSION_LOG → Docs Agent solo
- Fix de validación → Backend Agent solo

**Comando:**
```
"Usa Backend Agent para corregir validación de email en RegisterRequest"
```

---

### Nivel 2: Feature Modular 🔧
**Complejidad:** Media (CRUD, módulo completo)

**Estrategia:** 2-3 agentes en paralelo

**Ejemplos:**
- CRUD de Attendances
- Sistema de Notificaciones simple
- Nuevo endpoint API

**Comando:**
```
"Implementar CRUD de Attendances usando Backend, Frontend y Testing agents en paralelo"
```

**Flujo:**
1. Quick design (Arch Agent - 15 min)
2. Implementación paralela:
   - Backend: Models + Controllers
   - Frontend: Vistas Blade
   - Testing: Feature tests
3. Docs update (Docs Agent)

---

### Nivel 3: Sprint Completo 🚀
**Complejidad:** Alta (Sistema completo, múltiples módulos)

**Estrategia:** Sistema multi-agente completo con PLAN.md

**Ejemplos:**
- Sistema de Pagos (Sprint 6)
- Sistema de Planes de Entrenamiento
- Refactor arquitectónico

**Comando:**
```
"Iniciar Sprint 6 usando sistema multi-agente completo"
```

**Flujo:**
1. Investigation (3 agentes en paralelo)
2. Design (Arch Agent + aprobación usuario)
3. Implementation (Backend + Frontend + Testing en paralelo)
4. Documentation (Docs Agent)
5. Deploy (DevOps Agent)

---

## Agentes Disponibles

### 🏗️ Architecture Agent

**Responsabilidad:** Diseño de estructura de datos y decisiones arquitectónicas

**Cuándo usarlo:**
- Planear nueva entidad/tabla
- Diseñar relaciones complejas
- Validar coherencia arquitectónica
- Decidir estructura de JSON fields

**Input esperado:**
- Descripción de feature
- Requisitos funcionales
- Restricciones (multi-tenancy, etc.)

**Output:**
- Diagrama de relaciones
- SQL schema
- Decisiones técnicas documentadas
- Sección de diseño en PLAN.md

**Trigger keywords:**
`diseñar`, `arquitectura`, `modelo`, `entidad`, `relaciones`, `tabla`

---

### ⚙️ Backend Agent

**Responsabilidad:** Implementación de lógica de servidor con Laravel

**Cuándo usarlo:**
- Crear migrations, models, controllers
- Implementar validación (Form Requests)
- Crear services para lógica compleja
- Configurar rutas y middleware

**Input esperado:**
- Diseño de Arch Agent (si aplica)
- Requisitos funcionales
- Reglas de negocio

**Output:**
- Migrations ejecutables
- Models con relaciones
- Controllers con CRUD completo
- Form Requests
- Policies
- Rutas configuradas (duales)
- Código formateado con Pint

**Trigger keywords:**
`controller`, `modelo`, `migration`, `service`, `policy`, `CRUD`

**Convenciones clave:**
- Form Requests (nunca validación inline)
- Services para lógica compleja
- Rutas duales (con/sin business prefix)
- Middleware correcto (business.context, auth, coach)
- Eager loading para evitar N+1

---

### 🎨 Frontend Agent

**Responsabilidad:** Interfaces con Blade y Tailwind CSS

**Cuándo usarlo:**
- Crear vistas para CRUD
- Implementar formularios
- Diseñar componentes reutilizables
- Actualizar dashboard

**Input esperado:**
- Diseño de UX (wireframes opcionales)
- Datos que debe mostrar
- Acciones disponibles

**Output:**
- Vistas Blade (index, create, edit)
- Uso de componentes existentes (x-card, x-button)
- Formularios con validación client-side
- Estados vacíos
- Responsive mobile-first

**Trigger keywords:**
`vista`, `blade`, `formulario`, `componente`, `tailwind`, `UI`

**Convenciones clave:**
- Usar componentes existentes SIEMPRE
- Tailwind: `gap` para spacing (no margins)
- Empty states en todas las listas
- Mobile-first responsive
- Dark mode si aplica

**Componentes disponibles:**
- `x-card` - Cards genéricos
- `x-metric-card` - Métricas con números destacados
- `x-button` - Botones con 4 variants

---

### 🧪 Testing Agent

**Responsabilidad:** Cobertura de tests con PHPUnit

**Cuándo usarlo:**
- Después de implementar feature
- Validar lógica compleja
- Tests de autorización
- Validar multi-tenancy

**Input esperado:**
- Código implementado (models, controllers)
- Reglas de negocio
- Casos edge

**Output:**
- Feature tests para controllers
- Unit tests para services/models
- Factories actualizadas
- Seeders con datos realistas
- Reporte de tests ejecutados

**Trigger keywords:**
`test`, `testing`, `validar`, `probar`, `coverage`

**Convenciones clave:**
- PHPUnit (NUNCA Pest)
- Feature tests en `tests/Feature/`
- Unit tests en `tests/Unit/`
- Usar factories para crear modelos
- Test naming: `test_can_do_action_description()`

**Suite de tests típica:**
```php
test_index_displays_list_of_resources()
test_store_creates_new_resource()
test_update_modifies_resource()
test_destroy_deletes_resource()
test_unauthorized_user_cannot_access()
test_user_cannot_modify_others_resources()
test_business_isolation()
```

---

### 📝 Documentation Agent

**Responsabilidad:** Mantener documentación sincronizada

**Cuándo usarlo:**
- Después de completar feature
- Al finalizar sprint
- Al tomar decisiones arquitectónicas importantes
- Commits descriptivos

**Input esperado:**
- Feature implementada
- Decisiones técnicas tomadas
- Tiempo invertido

**Output:**
- PROJECT_STATUS.md actualizado
- ROADMAP.md con progreso
- SESSION_LOG.md con entrada nueva
- ARCHITECTURE.md si cambió diseño
- Commits descriptivos en español

**Trigger keywords:**
`documentar`, `actualizar`, `docs`, `session log`, `commit`

**Documentos que mantiene:**
- `PROJECT_STATUS.md` - Estado actual del proyecto
- `ROADMAP.md` - Progreso de sprints
- `SESSION_LOG.md` - Bitácora de sesiones
- `ARCHITECTURE.md` - Decisiones arquitectónicas

---

### 🚀 DevOps Agent

**Responsabilidad:** Deploy y configuración de servidor

**Cuándo usarlo:**
- Deploy a producción
- Configurar GitHub Actions
- Troubleshooting de deploy
- Optimización de builds

**Input esperado:**
- Código listo para producción
- Tests pasando
- Assets compilados

**Output:**
- Deploy exitoso
- Validación en producción
- Logs revisados
- Smoke tests ejecutados

**Trigger keywords:**
`deploy`, `producción`, `servidor`, `build`, `CI/CD`

**Deploy checklist:**
1. Tests pasan (`php artisan test`)
2. Assets compilados (`npm run build`)
3. Pint ejecutado (`vendor/bin/pint --dirty`)
4. Push a `main`
5. GitHub Actions ejecuta
6. Smoke test en producción

---

## Workflows Predefinidos

### Workflow 1: Feature Nueva Completa (Sprint)

**Cuándo usar:** Implementar un sprint completo (ej: Sistema de Pagos)

**Complejidad:** Alta

**Agentes involucrados:** Todos (6 agentes)

**Fases:**

#### Phase 1: Investigation (Paralelo)
- 🏗️ Arch Agent: Revisa ARCHITECTURE.md
- ⚙️ Backend Agent: Verifica modelos relacionados
- 🎨 Frontend Agent: Revisa componentes disponibles

#### Phase 2: Design (Secuencial)
- 🏗️ Arch Agent: Crea diseño completo en PLAN.md
- **STOP:** Espera aprobación del usuario

#### Phase 3: Implementation (Paralelo)
- ⚙️ Backend Agent: Migrations, models, controllers, policies
- 🎨 Frontend Agent: Vistas Blade con Tailwind
- 🧪 Testing Agent: Feature tests, unit tests, factories

#### Phase 4: Documentation (Secuencial)
- 📝 Docs Agent: Actualiza todos los docs

#### Phase 5: Deploy (Opcional)
- 🚀 DevOps Agent: Deploy a producción

**Tiempo estimado:** 4-6 horas (vs 8-10 horas secuencial)

**Comando para iniciar:**
```
"Iniciar Sprint 6 (Sistema de Pagos) con workflow completo"
```

---

### Workflow 2: CRUD Feature Modular

**Cuándo usar:** Implementar CRUD de una entidad (ej: Attendances)

**Complejidad:** Media

**Agentes involucrados:** 4 agentes (Arch, Backend, Frontend, Testing)

**Fases:**

#### Phase 1: Quick Design
- 🏗️ Arch Agent: Diseño rápido de tabla y relaciones (15 min)

#### Phase 2: Implementation (Paralelo)
- ⚙️ Backend Agent: CRUD completo
- 🎨 Frontend Agent: Vistas index, create, edit
- 🧪 Testing Agent: Feature tests

#### Phase 3: Docs Update
- 📝 Docs Agent: Actualiza PROJECT_STATUS.md y SESSION_LOG.md

**Tiempo estimado:** 2-3 horas

**Comando para iniciar:**
```
"Implementar CRUD de Attendances con workflow modular"
```

---

### Workflow 3: Bug Fix Simple

**Cuándo usar:** Corregir bug o hacer fix pequeño

**Complejidad:** Baja

**Agentes involucrados:** 1-2 agentes

**Fases:**

#### Phase 1: Fix
- ⚙️ Backend Agent (o Frontend Agent): Corregir código y ejecutar tests

#### Phase 2: Log
- 📝 Docs Agent: Actualizar SESSION_LOG.md

**Tiempo estimado:** 15-30 min

**Comando para iniciar:**
```
"Backend Agent: corregir cálculo de pace en Workout model"
```

---

## Cómo Usar el Sistema

### Paso 1: Identificar Complejidad

Pregúntate:
- ¿Afecta múltiples archivos/módulos? → **Complejo**
- ¿Es un CRUD completo? → **Moderado**
- ¿Es un fix localizado? → **Trivial**

### Paso 2: Elegir Workflow

Basado en complejidad:
- **Complejo** → Workflow 1 (Sprint completo)
- **Moderado** → Workflow 2 (CRUD modular)
- **Trivial** → Workflow 3 (Fix simple)

### Paso 3: Invocar Agente(s)

**Para tareas triviales:**
```
"Backend Agent: [tarea específica]"
```

**Para tareas moderadas:**
```
"Implementar [feature] usando Backend, Frontend y Testing agents en paralelo"
```

**Para sprints completos:**
```
"Iniciar Sprint [N] con sistema multi-agente completo:
- Objetivo: [descripción]
- Features: [lista de features]
- Usar PLAN.md para tracking"
```

### Paso 4: Seguir PLAN.md

El archivo `.agents/PLAN.md` se actualiza en tiempo real con:
- Progreso de cada agente
- Decisiones técnicas
- Blockers
- Handoff notes entre agentes

### Paso 5: Review e Integración

El orquestador (Claude) revisa el trabajo de todos los agentes antes de integrar:
- Validar consistencia
- Resolver conflictos
- Ejecutar tests finales
- Aprobar merge

---

## Ejemplos Prácticos

### Ejemplo 1: Sistema de Attendances (Moderado)

**Request del usuario:**
> "Quiero implementar un sistema de asistencias para Training Groups. Los coaches deben poder marcar quién asistió a cada entrenamiento."

**Claude (Orchestrator) decide:**
- Complejidad: **Moderada** (CRUD con lógica específica)
- Workflow: **CRUD Feature Modular**
- Agentes: Arch, Backend, Frontend, Testing

**Ejecución:**

```markdown
## Phase 1: Quick Design (15 min)
🏗️ Arch Agent:
  - Diseña tabla `attendances`
  - Relaciones: belongsTo(TrainingGroup), belongsTo(User)
  - Status: present, absent, justified
  - Coach-only access

## Phase 2: Implementation (Paralelo - 2h)
⚙️ Backend Agent:
  ✅ Migration creada
  ✅ Modelo Attendance con relaciones
  ✅ AttendanceController con CRUD
  ✅ AttendancePolicy (solo coaches)
  ✅ Rutas registradas

🎨 Frontend Agent:
  ✅ Vista index con lista por grupo
  ✅ Formulario para marcar asistencias
  ✅ Estados: presente (verde), ausente (rojo), justificado (amarillo)
  ✅ Filtros por fecha y grupo

🧪 Testing Agent:
  ✅ AttendanceTest con 8 tests
  ✅ Factory actualizada
  ✅ Tests de autorización
  ✅ Todos los tests pasan ✓

## Phase 3: Documentation (20 min)
📝 Docs Agent:
  ✅ PROJECT_STATUS.md actualizado
  ✅ SESSION_LOG.md con entrada
  ✅ Commit: "feat(attendance): implementar sistema de asistencias"
```

**Resultado:** Feature completa en 2.5 horas (vs 4-5 horas secuencial)

---

### Ejemplo 2: Fix de Validación (Trivial)

**Request del usuario:**
> "El formulario de Goals no valida que target_date sea futura"

**Claude (Orchestrator) decide:**
- Complejidad: **Trivial** (fix localizado)
- Workflow: **Bug Fix Simple**
- Agentes: Backend solo

**Ejecución:**

```markdown
⚙️ Backend Agent:
  1. Abre StoreGoalRequest.php
  2. Agrega regla: 'target_date' => ['nullable', 'date', 'after:today']
  3. Ejecuta: php artisan test --filter=GoalTest
  4. Test pasa ✓
  5. Ejecuta: vendor/bin/pint --dirty
  6. Commit: "fix(goals): validar que target_date sea fecha futura"

📝 Docs Agent:
  - Actualiza SESSION_LOG.md con fix
```

**Resultado:** Fix completado en 10 minutos

---

### Ejemplo 3: Sprint 6 - Sistema de Pagos (Complejo)

**Request del usuario:**
> "Quiero implementar el Sprint 6: Sistema de Pagos con Stripe"

**Claude (Orchestrator) decide:**
- Complejidad: **Alta** (sistema completo, múltiples módulos)
- Workflow: **Feature Nueva Completa**
- Agentes: Todos (6)

**Ejecución:**

```markdown
## Phase 1: Investigation (Paralelo - 30 min)
🏗️ Arch Agent:
  - Revisa ARCHITECTURE.md y PLAN_DESARROLLO_2025.md
  - Identifica: SubscriptionPlan, Subscription ya diseñadas
  - Nuevas entidades: Payment, PaymentMethod

⚙️ Backend Agent:
  - Verifica Laravel Cashier instalado
  - Revisa SubscriptionController existente
  - Identifica servicios necesarios: PaymentService

🎨 Frontend Agent:
  - Revisa vistas de subscriptions/
  - Componentes disponibles suficientes
  - Necesita: formulario de tarjeta con Stripe Elements

## Phase 2: Design (45 min)
🏗️ Arch Agent crea diseño completo:
  - Tabla payments (transactions log)
  - Integración Stripe Checkout
  - Webhooks para eventos de Stripe
  - Flow: Plan selection → Checkout → Webhook → Activate subscription

**STOP: Usuario aprueba diseño ✓**

## Phase 3: Implementation (Paralelo - 4h)
⚙️ Backend Agent:
  ✅ Migration payments table
  ✅ Payment model
  ✅ PaymentController con métodos: checkout, success, cancel
  ✅ Webhook handler para Stripe events
  ✅ PaymentService para lógica de Stripe
  ✅ Routes configuradas
  ✅ Middleware de verificación de suscripción

🎨 Frontend Agent:
  ✅ Vista subscriptions/checkout.blade.php con Stripe Elements
  ✅ Vista success.blade.php
  ✅ Vista cancel.blade.php
  ✅ Actualizar subscriptions/plans.blade.php con botones de pago
  ✅ Dashboard coach muestra status de suscripción

🧪 Testing Agent:
  ✅ PaymentTest (feature tests)
  ✅ Mock de Stripe API
  ✅ Tests de webhooks
  ✅ Tests de activación de suscripción
  ✅ PaymentServiceTest (unit tests)
  ✅ Todos los tests pasan ✓

## Phase 4: Documentation (30 min)
📝 Docs Agent:
  ✅ PROJECT_STATUS.md: "SPRINT 6 COMPLETADO ✅"
  ✅ ROADMAP.md: Sprint 6 marcado como completado
  ✅ SESSION_LOG.md con entrada detallada
  ✅ Crear docs/PAYMENTS.md con documentación del sistema
  ✅ Commit: "feat(payments): implementar sistema de pagos con Stripe (SPRINT 6)"

## Phase 5: Deploy (1h)
🚀 DevOps Agent:
  ✅ Tests completos pasan
  ✅ npm run build ejecutado
  ✅ Variables de Stripe configuradas en producción
  ✅ Webhooks configurados en Stripe Dashboard
  ✅ Deploy a producción exitoso
  ✅ Smoke test: Purchase flow completo OK
  ✅ Logs sin errores
```

**Resultado:** Sprint completo en 6.5 horas (vs 10-12 horas secuencial)

---

## 📊 Métricas de Eficiencia

| Tipo de Tarea | Secuencial | Multi-Agente | Ahorro |
|---------------|------------|--------------|--------|
| Bug Fix Simple | 30 min | 15 min | 50% |
| CRUD Modular | 4-5 horas | 2.5 horas | 40% |
| Sprint Completo | 10-12 horas | 6-7 horas | 35-40% |

**Beneficios adicionales:**
- ✅ Mayor consistencia (cada agente aplica convenciones)
- ✅ Menos olvidos (Testing y Docs obligatorios)
- ✅ Mejor documentación (Docs Agent automático)
- ✅ Testing completo (Testing Agent especializado)

---

## 🔧 Troubleshooting

### Problema: "No sé qué workflow usar"

**Solución:** Pregunta directamente:
```
"¿Qué nivel de complejidad tiene implementar [feature]? ¿Qué workflow recomiendas?"
```

---

### Problema: "Un agente se quedó bloqueado"

**Solución:** El agente debe reportar el blocker en PLAN.md:
```markdown
**Blockers:**
- Necesito aprobación de usuario sobre X decisión
- Falta información sobre Y requisito
```

El orchestrator pausa y pide input al usuario.

---

### Problema: "Los agentes trabajan duplicado"

**Solución:** PLAN.md coordina el trabajo. Cada agente actualiza su sección:
```markdown
### Backend Agent ⚙️
- [x] Migration creada
- [x] Modelo implementado
- [ ] Controller en progreso ← WORKING HERE

### Frontend Agent 🎨
- [ ] Esperando que Backend termine Controller ← BLOCKED
```

---

## 📚 Recursos

- **Configuración:** `.agents/config.json`
- **Plan de sesión:** `.agents/PLAN.md`
- **Documentación proyecto:** `docs/`
- **Guidelines Laravel:** `CLAUDE.md`

---

**Última actualización:** 2025-01-05
**Versión:** 1.0.0
