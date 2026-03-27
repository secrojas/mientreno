# MiEntreno

[![Laravel](https://img.shields.io/badge/Laravel-11-red)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777bb3)](https://www.php.net/)
[![Running](https://img.shields.io/badge/Running-Training%20Logs-2DE38E)](#)
[![Dev](https://img.shields.io/badge/Dev%20Friendly-Code%20%2B%20Run-FF3B5C)](#)

**MiEntreno** es una plataforma de registro y análisis de entrenamientos de running donde se mezcla el mundo del desarrollo con el deporte. Como un dashboard de developer, pero para tus kilómetros.

> **Entrená como corrés. Registrá como programás.**

Diseñada para corredores individuales y grupos de entrenamiento, con soporte multi-tenant, métricas automáticas y panel para coaches.

---

## 🏃 Características

### Para Corredores
- **Registro de entrenamientos**: Distancia, tiempo, pace, tipo, dificultad y notas
- **Gestión de carreras**: Próximas y pasadas, con tiempos objetivo y reales
- **Objetivos personales**: Metas de distancia, pace, frecuencia o carreras específicas
- **Métricas automáticas**: Totalizadores semanales/mensuales, pace promedio, racha
- **Dashboard personalizado**: Vista clara de tu progreso y actividad reciente

### Para Coaches
- **Panel de alumnos**: Vista de entrenamientos y métricas de cada corredor
- **Gestión de grupos**: Crear y administrar grupos de entrenamiento
- **Asistencias**: Registro de asistencia a entrenamientos grupales
- **Análisis comparativo**: Métricas agregadas de grupos y alumnos

### Técnicas
- **Multi-tenant**: Corredores individuales o grupos de entrenamiento aislados
- **Roles y permisos**: Runner, Coach, Admin con diferentes niveles de acceso
- **API REST**: Endpoints seguros con Laravel Sanctum (futuro)
- **Diseño dev-friendly**: UI oscura con estética de código

---

## 🧭 Arquitectura Multi-tenant

MiEntreno soporta dos modos de uso:

### Usuario Individual
- Corredor que usa la app solo para sí mismo
- Sin pertenencia a ningún grupo
- Acceso directo: `/dashboard`, `/workouts`, `/races`

### Grupos de Entrenamiento (Business)
- Equipos de running que comparten la plataforma
- Cada grupo tiene su propio slug: `/{business-slug}/...`
- Coaches con acceso a métricas de alumnos
- Entrenamientos grupales y asistencias

**Implementación técnica**:
- Tabla `businesses` con `slug` único
- Usuarios con `business_id` nullable
- Middleware para aislar datos por business
- Email único por business (no globalmente)

---

## 🧱 Stack Tecnológico

- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: Blade templates + CSS Vanilla (custom properties)
- **Base de Datos**: MySQL 8+
- **Autenticación**: Sistema custom multi-tenant
- **Diseño**: Dark theme, Space Grotesk + Inter fonts
- **Futuro**: Laravel Sanctum para API, Chart.js para gráficos

---

## 🛠️ Instalación

### Requisitos
- PHP 8.2+
- Composer 2.x
- MySQL 8+
- Node.js 18+ (para assets)
- Laragon/XAMPP/Valet (recomendado para desarrollo)

### Setup

```bash
# 1. Clonar repositorio
git clone https://github.com/secrojas/mientreno.git
cd mientreno

# 2. Instalar dependencias
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
DB_DATABASE=mientreno
DB_USERNAME=root
DB_PASSWORD=

# 5. Crear base de datos (MySQL)
# Usar HeidiSQL, phpMyAdmin o:
mysql -u root -e "CREATE DATABASE mientreno"

# 6. Ejecutar migraciones
php artisan migrate

# 7. (Opcional) Datos de prueba
php artisan db:seed

# 8. Iniciar servidor
php artisan serve
# Visitar: http://localhost:8000
```

---

## 📚 Documentación

El proyecto cuenta con documentación completa en la carpeta `docs/`:

- **[docs/README.md](docs/README.md)**: Índice de documentación y guía de inicio
- **[docs/PROJECT_STATUS.md](docs/PROJECT_STATUS.md)**: Estado actual del proyecto
- **[docs/ARCHITECTURE.md](docs/ARCHITECTURE.md)**: Arquitectura completa y modelo de datos
- **[docs/ROADMAP.md](docs/ROADMAP.md)**: Plan de desarrollo en 8 fases
- **[docs/SESSION_LOG.md](docs/SESSION_LOG.md)**: Bitácora de sesiones de desarrollo
- **[docs/RECOMENDACIONES.md](docs/RECOMENDACIONES.md)**: Mejores prácticas y recomendaciones
- **[.github/DEVELOPMENT_GUIDE.md](.github/DEVELOPMENT_GUIDE.md)**: Guía rápida de desarrollo

### Para desarrolladores nuevos

1. Lee `docs/README.md` primero
2. Revisa `docs/PROJECT_STATUS.md` para ver dónde estamos
3. Consulta `docs/ARCHITECTURE.md` antes de crear nuevas features
4. Sigue el plan en `docs/ROADMAP.md`

---

## 🚧 Estado del Proyecto

**Versión actual**: 0.2.0 (MVP Core Features)
**Fase actual**: ✅ Fase 2 completada - Races & Goals
**Última actualización**: 2025-12-12

### ✅ Implementado (Fase 1 + Fase 2)
- ✅ Sistema multi-tenant con businesses
- ✅ Autenticación personalizada con invitaciones
- ✅ **Workouts CRUD completo** con filtros y búsqueda
- ✅ **Races CRUD completo** (carreras próximas y pasadas)
- ✅ **Goals CRUD completo** (4 tipos: race, distance, pace, frequency)
- ✅ Componentes Blade reutilizables (card, metric-card, button)
- ✅ MetricsService para cálculos y estadísticas
- ✅ GoalProgressService con cálculo automático de progreso
- ✅ Dashboard funcional con datos reales
- ✅ Vinculación workouts → races
- ✅ UX mejorada: formularios dinámicos sin JSON manual
- ✅ Cálculo automático de progreso basado en entrenamientos

### 🚀 Próximamente (Fase 3-8)
- Panel de coach para gestión de alumnos
- Grupos de entrenamiento
- Training plans (planes de entrenamiento)
- Analytics y gráficos avanzados
- Exportación de datos
- Integraciones (Strava, GPS watches)

Ver [ROADMAP.md](docs/ROADMAP.md) para el plan completo.

---

## 🎯 Modelo de Datos

### Entidades principales

```
Business (Grupos de entrenamiento)
  └── Users (Corredores y coaches)
      ├── Workouts (Entrenamientos)
      ├── Races (Carreras)
      ├── Goals (Objetivos)
      └── TrainingGroups (Miembro de grupos)

TrainingGroup
  ├── Coach (User)
  ├── Members (Users)
  └── Attendances (Asistencias)
```

Ver [ARCHITECTURE.md](docs/ARCHITECTURE.md) para detalles completos.

---

## 🧪 Testing

```bash
# Ejecutar todos los tests
php artisan test

# Test específico
php artisan test --filter WorkoutTest

# Con coverage
php artisan test --coverage
```

---

## 🤝 Contribución

Este proyecto está en desarrollo activo. Contribuciones son bienvenidas.

### Proceso
1. Fork del repositorio
2. Crear branch: `git checkout -b feature/nueva-funcionalidad`
3. Commit cambios: `git commit -m 'feat: agregar nueva funcionalidad'`
4. Push: `git push origin feature/nueva-funcionalidad`
5. Crear Pull Request

### Convención de commits
```
feat(scope): descripción
fix(scope): descripción
docs: descripción
refactor(scope): descripción
test(scope): descripción
```

### Documentación
**Importante**: Actualizar `docs/SESSION_LOG.md` al final de cada sesión de desarrollo.

---

## 📝 Licencia

[Por definir por el momento, pero probablemente MIT o GPL]

---

## 👤 Autor

**Sebastián Rojas**
- Website: [srojasweb.dev](https://srojasweb.dev)
- GitHub: [@secrojas](https://github.com/secrojas)

---

## 🙏 Agradecimientos

Proyecto que nace de la pasión por el running y el desarrollo de software.

**MiEntreno**: Donde los kilómetros se convierten en datos, y los datos en progreso.

