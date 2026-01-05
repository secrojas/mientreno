# 🎯 Session Plan - MiEntreno

**Fecha:** [Fecha de inicio]
**Sprint:** [Número de sprint]
**Objetivo:** [Descripción breve del objetivo principal]

---

## 📋 Current Task

[Descripción de la tarea actual]

---

## Phase 1: Investigation 🔍

**Status:** [ ] Pending / [x] In Progress / [x] Completed

### Architecture Agent 🏗️
- [ ] Revisar ARCHITECTURE.md para contexto
- [ ] Identificar entidades relacionadas
- [ ] Verificar coherencia con multi-tenancy
- [ ] Definir estructura de tabla/JSON

**Findings:**
```
[Notas del agente]
```

---

### Backend Agent ⚙️
- [ ] Verificar modelos relacionados existentes
- [ ] Identificar controllers y services afectados
- [ ] Revisar rutas actuales
- [ ] Validar que no exista funcionalidad similar

**Findings:**
```
[Notas del agente]
```

---

### Frontend Agent 🎨
- [ ] Revisar componentes Blade disponibles
- [ ] Identificar layouts aplicables
- [ ] Verificar convenciones de Tailwind
- [ ] Buscar vistas similares como referencia

**Findings:**
```
[Notas del agente]
```

---

## Phase 2: Design 🎨

**Status:** [ ] Pending / [ ] In Progress / [ ] Completed
**Approval:** [ ] Waiting / [ ] Approved / [ ] Changes Requested

### Architecture Agent 🏗️

#### Database Schema
```sql
CREATE TABLE table_name (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,
    -- Campos adicionales
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Índices
CREATE INDEX idx_business_id ON table_name(business_id);
CREATE INDEX idx_user_id ON table_name(user_id);
```

#### Model Relationships
```php
// En Model principal
public function relatedModel(): BelongsTo
{
    return $this->belongsTo(RelatedModel::class);
}

// Scopes útiles
public function scopeForUser($query, $userId)
{
    return $query->where('user_id', $userId);
}
```

#### Decisions Made
1. [Decisión 1 con justificación]
2. [Decisión 2 con justificación]
3. [Decisión 3 con justificación]

---

## Phase 3: Implementation 🚀

**Status:** [ ] Pending / [ ] In Progress / [ ] Completed

### Backend Agent ⚙️

#### Tasks
- [ ] Crear migración: `php artisan make:migration create_table_name_table`
- [ ] Crear modelo: `php artisan make:model ModelName`
- [ ] Implementar relaciones en modelo
- [ ] Crear controller: `php artisan make:controller ModelNameController`
- [ ] Implementar métodos CRUD (index, create, store, edit, update, destroy)
- [ ] Crear Form Requests: `StoreModelNameRequest`, `UpdateModelNameRequest`
- [ ] Crear Policy: `php artisan make:policy ModelNamePolicy`
- [ ] Registrar rutas en `routes/web.php` (duales: con/sin business)
- [ ] Aplicar middlewares correctos
- [ ] Ejecutar Pint: `vendor/bin/pint --dirty`

**Progress Notes:**
```
[Notas de progreso del backend]
```

**Blockers:**
```
[Si hay algún bloqueador, describirlo aquí]
```

---

### Frontend Agent 🎨

#### Tasks
- [ ] Crear vista index: `resources/views/module/index.blade.php`
  - Lista con componente x-card
  - Filtros si aplica
  - Paginación con appends()
  - Empty state
- [ ] Crear vista create: `resources/views/module/create.blade.php`
  - Formulario con todos los campos
  - Validación client-side (opcional)
  - Botones submit/cancel
- [ ] Crear vista edit: `resources/views/module/edit.blade.php`
  - Formulario pre-cargado
  - Botones update/cancel/delete
- [ ] Actualizar sidebar si es necesario
- [ ] Actualizar dashboard si aplica integración

**Progress Notes:**
```
[Notas de progreso del frontend]
```

**Blockers:**
```
[Si hay algún bloqueador, describirlo aquí]
```

---

### Testing Agent 🧪

#### Tasks
- [ ] Crear Feature Test: `php artisan make:test --phpunit ModelNameTest`
  - test_index_displays_list()
  - test_create_displays_form()
  - test_store_creates_resource()
  - test_edit_displays_form()
  - test_update_modifies_resource()
  - test_destroy_deletes_resource()
  - test_unauthorized_cannot_access()
  - test_user_cannot_modify_others_resources()
  - test_business_isolation()
- [ ] Crear Unit Tests si aplica (services, models)
- [ ] Actualizar/crear Factory: `php artisan make:factory ModelNameFactory`
- [ ] Actualizar Seeder con datos de prueba
- [ ] Ejecutar tests: `php artisan test --filter=ModelName`
- [ ] Validar coverage

**Test Results:**
```
[Resultados de los tests]
```

**Blockers:**
```
[Si hay algún bloqueador, describirlo aquí]
```

---

## Phase 4: Documentation 📝

**Status:** [ ] Pending / [ ] In Progress / [ ] Completed

### Documentation Agent 📝

#### Tasks
- [ ] Actualizar `docs/PROJECT_STATUS.md`
  - Agregar nueva funcionalidad en sección "Lo que ya está implementado"
  - Actualizar estado del sprint
- [ ] Actualizar `docs/ROADMAP.md`
  - Marcar tareas completadas con ✅
  - Actualizar % de progreso
- [ ] Escribir entrada en `docs/SESSION_LOG.md`
  - Número de sesión
  - Fecha y duración
  - Qué se implementó
  - Problemas encontrados
  - Decisiones técnicas
  - Próximos pasos
- [ ] Actualizar `docs/ARCHITECTURE.md` si cambió modelo de datos
- [ ] Crear documentación específica si feature es compleja (opcional)
- [ ] Escribir mensaje de commit descriptivo

**Draft Commit Message:**
```
feat(scope): [descripción en español]

[Cuerpo del commit explicando qué y por qué]

🤖 Generated with [Claude Code](https://claude.com/claude-code)

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>
```

---

## Phase 5: Deployment 🚀 (Opcional)

**Status:** [ ] Pending / [ ] In Progress / [ ] Completed

### DevOps Agent 🚀

#### Pre-Deploy Checklist
- [ ] Todos los tests pasan: `php artisan test`
- [ ] Assets compilados: `npm run build`
- [ ] Pint ejecutado: `vendor/bin/pint --dirty`
- [ ] Migrations revisadas (no destructivas)
- [ ] `.env` variables configuradas en producción

#### Deploy Tasks
- [ ] Commit y push a `main`
- [ ] GitHub Actions ejecuta workflow
- [ ] Validar deploy exitoso
- [ ] Smoke test en producción
- [ ] Revisar logs de errores

**Deploy Notes:**
```
[Notas del deploy]
```

---

## 📊 Summary

### Time Tracking
- **Investigation:** [X horas]
- **Design:** [X horas]
- **Implementation:** [X horas]
- **Testing:** [X horas]
- **Documentation:** [X horas]
- **Total:** [X horas]

### Key Decisions
1. [Decisión importante 1]
2. [Decisión importante 2]
3. [Decisión importante 3]

### Technical Debt
- [Item de deuda técnica si aplica]

### Next Steps
1. [Próximo paso 1]
2. [Próximo paso 2]
3. [Próximo paso 3]

---

## 🔄 Handoff Notes

### Para Backend Agent
```
[Si hay tareas pendientes o información importante]
```

### Para Frontend Agent
```
[Si hay tareas pendientes o información importante]
```

### Para Testing Agent
```
[Si hay tareas pendientes o información importante]
```

### Para Docs Agent
```
[Si hay tareas pendientes o información importante]
```

---

**Última actualización:** [Fecha y hora]
**Orchestrator:** [Nombre del usuario/sesión]
