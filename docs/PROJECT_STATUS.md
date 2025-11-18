# MiEntreno - Estado del Proyecto

**Fecha de inicio**: Noviembre 2025
**Stack**: Laravel 11.x
**Concepto**: Aplicación de registro y análisis de entrenamientos de running que mezcla el mundo del desarrollo con el running.

---

## Estado Actual (2025-11-18)

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
- `User`: Con relación a Business y campo role
- `Business`: Modelo básico para grupos de entrenamiento

---

## Lo que falta implementar

### 1. Modelos Core de Running
- `Workout` / `Training`: Entrenamientos individuales
- `Race`: Carreras (participadas y futuras)
- `Goal`: Objetivos del corredor
- `TrainingPlan`: Planes de entrenamiento
- `TrainingGroup`: Grupos de entrenamiento dentro de un business
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
