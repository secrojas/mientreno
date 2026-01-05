# 🚀 Quick Start - Sistema Multi-Agente

Guía rápida para empezar a usar el sistema multi-agente en MiEntreno.

---

## 🎯 En 3 Pasos

### 1. Identifica la Complejidad

| Tipo | Indicadores | Workflow |
|------|-------------|----------|
| **Trivial** ⚡ | - Fix pequeño<br>- Typo<br>- Update doc<br>- Cambio en 1-2 archivos | Single Agent |
| **Moderada** 🔧 | - CRUD completo<br>- Feature modular<br>- 3-5 archivos afectados | 2-3 Agents Paralelo |
| **Compleja** 🚀 | - Sprint completo<br>- Sistema nuevo<br>- Múltiples módulos<br>- 10+ archivos | Full Multi-Agent |

---

### 2. Usa el Comando Correcto

#### Tarea Trivial
```
"[Agente] + [tarea específica]"
```

**Ejemplos:**
```
"Backend Agent: agregar validación de email único en StoreUserRequest"

"Frontend Agent: corregir spacing en formulario de workouts"

"Docs Agent: actualizar SESSION_LOG con sesión de hoy"
```

---

#### Tarea Moderada
```
"Implementar [feature] usando [agentes] en paralelo"
```

**Ejemplos:**
```
"Implementar CRUD de Attendances usando Backend, Frontend y Testing agents en paralelo"

"Agregar sistema de notificaciones simple con Backend y Frontend agents"
```

---

#### Tarea Compleja (Sprint)
```
"Iniciar Sprint [N] con sistema multi-agente completo:
- Objetivo: [descripción breve]
- Features: [lista]
- Usar PLAN.md para tracking"
```

**Ejemplos:**
```
"Iniciar Sprint 6 (Sistema de Pagos) con sistema multi-agente completo:
- Objetivo: Integrar Stripe para procesar pagos de suscripciones
- Features: Checkout, webhooks, activación automática
- Usar PLAN.md para tracking"
```

---

### 3. Sigue el PLAN.md

Durante la ejecución, revisa `.agents/PLAN.md` para:
- ✅ Ver progreso de cada agente
- 📝 Leer decisiones técnicas
- ⚠️ Identificar blockers
- 🔄 Aprobar pasos cuando se requiera

---

## 🤖 Los 6 Agentes

| Agente | Emoji | Usa cuando necesites... |
|--------|-------|-------------------------|
| **Architecture** | 🏗️ | Diseñar estructura de datos, relaciones, decisiones arquitectónicas |
| **Backend** | ⚙️ | Implementar lógica Laravel (models, controllers, services, policies) |
| **Frontend** | 🎨 | Crear vistas Blade, formularios, componentes, diseño Tailwind |
| **Testing** | 🧪 | Escribir tests PHPUnit, validar lógica, coverage |
| **Documentation** | 📝 | Actualizar docs, session logs, commits descriptivos |
| **DevOps** | 🚀 | Deploy, CI/CD, troubleshooting producción |

---

## 📋 Cheat Sheet

### Comandos Comunes

#### CRUD Completo
```
"Implementar CRUD de [Entity] con workflow modular:
1. Arch Agent: diseño rápido de tabla
2. Backend + Frontend + Testing: implementación paralela
3. Docs Agent: actualizar PROJECT_STATUS y SESSION_LOG"
```

#### Bug Fix
```
"Backend Agent: corregir [descripción del bug] en [archivo]"
```

#### Nueva Vista
```
"Frontend Agent: crear vista [nombre] usando componentes x-card y x-button"
```

#### Tests
```
"Testing Agent: crear feature tests para [Controller] con coverage completo"
```

#### Actualizar Docs
```
"Docs Agent: actualizar SESSION_LOG con entrada de sesión de hoy"
```

#### Deploy
```
"DevOps Agent: deploy a producción siguiendo checklist completo"
```

---

## 🎯 Ejemplo Real: CRUD de Attendances

**Tu request:**
```
"Implementar CRUD de Attendances para que coaches marquen asistencias a entrenamientos grupales"
```

**Claude responde:**
```
Voy a implementar el CRUD de Attendances con workflow modular:

Complejidad: Moderada
Agentes: Architecture, Backend, Frontend, Testing, Documentation

Fase 1: Quick Design (Arch Agent)
  - Diseñando tabla attendances...
  - Relaciones: training_group_id, user_id, date, status

Fase 2: Implementation (Paralelo)
  Backend Agent: migrations, models, controllers...
  Frontend Agent: vistas index, create...
  Testing Agent: feature tests...

Fase 3: Documentation
  Docs Agent: actualizando PROJECT_STATUS.md...
```

**Tú observas:** PLAN.md se actualiza en tiempo real con progreso

**Resultado:** CRUD completo en 2-3 horas

---

## ⚡ Tips Pro

1. **Deja que Claude decida complejidad**
   ```
   "¿Qué workflow recomiendas para [feature]?"
   ```

2. **Aprueba diseños antes de implementar**
   - Claude te mostrará el diseño del Arch Agent
   - Apruébalo antes de que los agentes implementen

3. **Revisa PLAN.md durante sprints largos**
   - Es tu fuente de verdad
   - Muestra decisiones y progreso

4. **Para features urgentes, usa menos agentes**
   ```
   "Backend Agent: implementar [feature] sin pasar por Arch Agent (es urgente)"
   ```

5. **Combina agentes manualmente si prefieres**
   ```
   "Backend Agent: implementar modelo y controller
   Luego Frontend Agent: crear vistas
   Luego Testing Agent: agregar tests"
   ```

---

## 🔥 Ejemplos Rápidos

### Agregar Campo a Modelo
```
"Backend Agent: agregar campo 'phone' a users table con migration"
```

### Crear Componente Blade
```
"Frontend Agent: crear componente x-badge con variants (success, warning, danger)"
```

### Fix de Validación
```
"Backend Agent: agregar validación de longitud mínima 8 caracteres a password en StoreUserRequest"
```

### Actualizar Dashboard
```
"Frontend Agent: agregar card de 'Próximo Entrenamiento' al dashboard coach"
```

### Documentar Sprint
```
"Docs Agent: marcar Sprint 6 como completado en PROJECT_STATUS.md y ROADMAP.md"
```

### Deploy
```
"DevOps Agent: hacer deploy del Sprint 6 a producción"
```

---

## ❓ ¿Dudas?

**Pregunta directamente:**
```
"¿Cómo uso el sistema multi-agente para [tu caso]?"
"¿Qué agente debería usar para [tarea]?"
"¿Es esto trivial, moderado o complejo?"
```

**Claude te guiará** 🤖

---

## 📚 Más Info

- **Guía completa:** `.agents/README.md`
- **Configuración:** `.agents/config.json`
- **Template de plan:** `.agents/PLAN.md`

---

**Última actualización:** 2025-01-05
