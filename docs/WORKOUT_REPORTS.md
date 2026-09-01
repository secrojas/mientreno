# Sistema de Reportes de Entrenamientos

## Visión General

Sistema para generar reportes semanales y mensuales de entrenamientos con posibilidad de exportación, pensado principalmente para compartir progreso con entrenadores.

---

## Objetivos

### Principales
1. **Vista de Resumen Semanal**: Métricas y detalles de la semana actual y semanas anteriores
2. **Vista de Resumen Mensual**: Métricas y detalles del mes actual y meses anteriores
3. **Navegación Temporal**: Moverse fácilmente entre semanas/meses
4. **Exportación**: Generar PDF descargable para compartir

### Secundarios
- ✅ Comparativas semana a semana / mes a mes (Fase 1)
- ✅ Insights automáticos (mejoras, tendencias) (Fase 1)
- ✅ Compartir vía link con expiración (Fase 3)
- Historial de reportes generados (Fase 6)
- Gráficos y visualizaciones (Fase 4)

---

## Análisis: ¿Qué Debe Incluir un Reporte?

### Información Esencial para Entrenador

**Métricas Cuantitativas:**
- Total de kilómetros
- Total de tiempo entrenado
- Número de sesiones (planificadas vs completadas vs saltadas)
- Pace promedio
- Desnivel acumulado
- FC promedio (si tiene datos)

**Distribución de Entrenamientos:**
- Por tipo (easy run, intervals, tempo, long run, etc.)
- Gráfico de pastel o barras con distribución
- Tabla con desglose

**Cumplimiento del Plan:**
- % de adherencia (completados / planificados)
- Días entrenados vs días planificados
- Entrenamientos saltados con razones

**Progreso Respecto a Objetivos:**
- Estado de goals activos
- Avance hacia carreras próximas
- Comparativa con período anterior

**Detalles de Sesiones:**
- Lista de todos los workouts con:
  - Fecha
  - Tipo
  - Distancia
  - Duración
  - Pace
  - Dificultad percibida
  - Notas

**Insights (opcional pero valioso):**
- Mejor/peor entrenamiento de la semana
- Días de mayor volumen
- Tendencias (mejorando pace, aumentando volumen, etc.)

---

## Propuesta de Diseño

### 1. Vista de Reportes (/reports)

**URL Structure:**
```
/reports                           → Vista principal (default: semana actual)
/reports/weekly                    → Semana actual
/reports/weekly/{year}/{week}     → Semana específica
/reports/monthly                   → Mes actual
/reports/monthly/{year}/{month}   → Mes específico
```

**Layout Principal:**

```
┌─────────────────────────────────────────────────────┐
│  [← Anterior]  Semana 50, 2025  [Siguiente →]      │
│                                                      │
│  Selector: ○ Semanal  ● Mensual                    │
│                                                      │
│  [📥 Exportar PDF]  [📧 Compartir] (futuro)        │
├─────────────────────────────────────────────────────┤
│                                                      │
│  ┌──────────────────────────────────────────────┐  │
│  │  RESUMEN GENERAL                             │  │
│  │  ────────────────                            │  │
│  │  📏 150.2 km    ⏱️ 12h 45m    📊 8 sesiones  │  │
│  │  ⚡ 5:04 /km    📈 1,240m D+   ❤️ 158 bpm    │  │
│  └──────────────────────────────────────────────┘  │
│                                                      │
│  ┌──────────────────────────────────────────────┐  │
│  │  CUMPLIMIENTO DEL PLAN                       │  │
│  │  ──────────────────                          │  │
│  │  ████████░░ 80% (8 de 10 completados)        │  │
│  │  2 saltados: Lluvia (1), Lesión menor (1)    │  │
│  └──────────────────────────────────────────────┘  │
│                                                      │
│  ┌──────────────────────────────────────────────┐  │
│  │  DISTRIBUCIÓN POR TIPO                        │  │
│  │  ──────────────────                          │  │
│  │  [Gráfico de barras o pastel]                │  │
│  │  Easy Run: 3 (45km)                          │  │
│  │  Long Run: 2 (40km)                          │  │
│  │  Intervals: 2 (20km)                         │  │
│  │  Tempo: 1 (15km)                             │  │
│  └──────────────────────────────────────────────┘  │
│                                                      │
│  ┌──────────────────────────────────────────────┐  │
│  │  COMPARATIVA                                  │  │
│  │  ───────────                                 │  │
│  │  Esta semana vs Semana anterior:             │  │
│  │  Km: +15.2 (+11%) ↗️                         │  │
│  │  Tiempo: +45min (+6%) ↗️                     │  │
│  │  Pace: -0:12 /km (mejora) ✅                 │  │
│  └──────────────────────────────────────────────┘  │
│                                                      │
│  ┌──────────────────────────────────────────────┐  │
│  │  OBJETIVOS EN PROGRESO                       │  │
│  │  ──────────────────                          │  │
│  │  [Lista de goals activos con progreso]       │  │
│  └──────────────────────────────────────────────┘  │
│                                                      │
│  ┌──────────────────────────────────────────────┐  │
│  │  DETALLE DE ENTRENAMIENTOS                    │  │
│  │  ──────────────────────                      │  │
│  │  [Tabla con todos los workouts]              │  │
│  └──────────────────────────────────────────────┘  │
│                                                      │
└─────────────────────────────────────────────────────┘
```

### 2. Exportación PDF

**Librería Sugerida:** Laravel DomPDF o Laravel Snappy (wkhtmltopdf)

**Estructura del PDF:**
```
┌────────────────────────────────────────┐
│  [LOGO MIENTRENO]                      │
│                                        │
│  REPORTE SEMANAL                       │
│  Semana 50, 2025 (11/12 - 17/12)      │
│  Atleta: Juan Pérez                    │
│  ────────────────────────────────────  │
│                                        │
│  RESUMEN GENERAL                       │
│  [Tabla con métricas]                  │
│                                        │
│  CUMPLIMIENTO                          │
│  [Barra de progreso + estadísticas]    │
│                                        │
│  DISTRIBUCIÓN POR TIPO                 │
│  [Gráfico + tabla]                     │
│                                        │
│  COMPARATIVA                           │
│  [Tabla comparativa]                   │
│                                        │
│  DETALLE DE ENTRENAMIENTOS             │
│  [Tabla completa]                      │
│                                        │
│  NOTAS DESTACADAS                      │
│  [Mejores momentos / observaciones]    │
│                                        │
│  ────────────────────────────────────  │
│  Generado: 12/12/2025 20:30            │
│  mientreno.app                         │
└────────────────────────────────────────┘
```

---

## Plan de Implementación

### ✅ FASE 1 - Core Report Views (COMPLETADA 2025-12-15)

**Backend:**
- [x] Crear `ReportController` con métodos:
  - `index()` - Redirect a weekly
  - `weekly($year = null, $week = null)`
  - `monthly($year = null, $month = null)`
  - `exportWeeklyPDF($year, $week)`
  - `exportMonthlyPDF($year, $month)`

- [x] Crear `ReportService` con métodos:
  - `getWeeklyReport(User $user, $year, $week)` - Reporte semanal completo
  - `getMonthlyReport(User $user, $year, $month)` - Reporte mensual completo
  - `calculateSummary($workouts)` - Métricas del período
  - `getWorkoutDistribution($workouts)` - Distribución por tipo con %
  - `getComparison($current, $previous)` - Comparativas período a período
  - `calculateDiff($current, $previous, $type)` - Cálculo de diferencias
  - `calculatePaceDiff($current, $previous)` - Diferencias de pace (lógica invertida)
  - `getInsights($workouts, $user)` - 5 tipos de insights automáticos
  - `calculatePeriodStreak($workouts)` - Racha de días consecutivos

- [x] MetricsService ya existente aprovechado para formatters

**Frontend:**
- [x] Crear `resources/views/reports/weekly.blade.php`:
  - Vista de resumen semanal completa
  - Navegación anterior/siguiente funcional
  - Métricas principales en grid
  - Comparativas visuales
  - Distribución por tipo
  - Insights automáticos
  - Tabla detallada de entrenamientos

- [x] Crear `resources/views/reports/monthly.blade.php`:
  - Vista de resumen mensual completa
  - Similar estructura que weekly
  - Métricas adicionales (FC, desnivel) si disponibles

- [x] Crear componentes reutilizables:
  - `<x-report-card>`: Card para secciones del reporte
  - `<x-metric-comparison>`: Mostrar comparativas con flechas y tendencias
  - `<x-workout-table>`: Tabla de workouts formateada con soporte para notas

- [x] Agregar link "Reportes" en sidebar del dashboard

**Routes implementadas:**
```php
Route::middleware('auth')->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/weekly', [ReportController::class, 'weekly'])->name('weekly');
    Route::get('/weekly/{year}/{week}', [ReportController::class, 'weekly'])->name('weekly.period');
    Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
    Route::get('/monthly/{year}/{month}', [ReportController::class, 'monthly'])->name('monthly.period');
});
```

**Tiempo real:** ~3 horas ✅

---

### ✅ FASE 2.5 - Rediseño Visual PDFs (COMPLETADA 2026-03-27)

**Motivación:** Los PDFs originales eran funcionales pero visualmente pobres. Se rediseñaron desde cero con criterio de diseño profesional.

**Diseño aplicado:**
- Header dark (`#0A0B0F`) con barra de acento roja (`#FF3B5C`) y logotipo MI·ENTRENO
- Hero section: KM total en `34pt` verde brillante (`#2DE38E`), badge de tendencia con colores semánticos
- Distribución por tipo: barras CSS proporcionales con colores por tipo de entrenamiento
- Tabla de comparativa: header dark, filas alternadas light/gris
- Insights en grid 2 columnas (PDF semanal)
- Página 2 con mini-header oscuro y tabla completa de entrenamientos

**Restricciones de DomPDF documentadas:**
- No soporta `flexbox` ni CSS Grid → usar `display: table` / `display: table-cell`
- Fuente usada: `DejaVu Sans` (bundled en DomPDF) → soporte Unicode completo (●, ○, ↑, ↓)
- HTML entities dentro de `{{ }}` Blade son escapadas (& → &amp;) → usar UTF-8 directo

**Bug crítico resuelto — dificultad con entidades HTML:**
- ❌ `{{ str_repeat('&#9679;', $filled) }}` → renderiza `&#9679;&#9679;` como texto literal
- ✅ `{{ str_repeat('●', $filled) }}` / `{{ str_repeat('○', $empty) }}` → UTF-8 directo
- Mismo problema con flechas: `{{ '&#8593; Mejora' }}` → literales. Fix: `{{ '↑ Mejora' }}`

**Optimización de espacio (fit en páginas):**
- Semanal: target 1 página (con hasta ~7 workouts + notas truncadas)
- Mensual: target 2 páginas (página 1 = stats/comparativa/distribución, página 2 = tabla)
- Reducción de paddings: header `18px` → `12px`, hero `18px` → `12px`, KM `44-48pt` → `34pt`
- Notas truncadas: `mb_strimwidth($workout->notes, 0, 120, '…')`

**Archivos modificados:**
- `resources/views/reports/pdf/weekly.blade.php` — rediseño completo
- `resources/views/reports/pdf/monthly.blade.php` — rediseño completo

---

### ✅ FASE 2 - Exportación PDF (COMPLETADA 2025-12-15)

**Setup:**
- [x] Instalar librería PDF DomPDF:
  ```bash
  composer require barryvdh/laravel-dompdf
  ```
- [x] Versión instalada: v3.1.1
- [x] Configuración automática con Laravel

**Backend:**
- [x] Crear templates PDF en `resources/views/reports/pdf/`:
  - `weekly.blade.php` - Template semanal con estilos inline
  - `monthly.blade.php` - Template mensual con estilos inline

- [x] Implementar métodos de exportación en `ReportController`:
  ```php
  public function exportWeeklyPDF($year, $week)
  {
      $user = Auth::user();
      $report = $this->reportService->getWeeklyReport($user, $year, $week);
      $pdf = Pdf::loadView('reports.pdf.weekly', compact('report'));
      $pdf->setPaper('a4', 'portrait');
      return $pdf->download("reporte-semanal-{$year}-semana-{$week}.pdf");
  }

  public function exportMonthlyPDF($year, $month)
  {
      $user = Auth::user();
      $report = $this->reportService->getMonthlyReport($user, $year, $month);
      $pdf = Pdf::loadView('reports.pdf.monthly', compact('report'));
      $pdf->setPaper('a4', 'portrait');
      $monthName = Carbon::createFromDate($year, $month, 1)->locale('es')->monthName;
      return $pdf->download("reporte-mensual-{$monthName}-{$year}.pdf");
  }
  ```

- [x] Estilos inline optimizados para DomPDF
- [x] Layout profesional con header, logo, métricas, tablas y footer
- [x] Page break antes de tabla de entrenamientos detallados

**Frontend:**
- [x] Agregar botón "📥 Exportar PDF" en vista semanal con gradiente verde
- [x] Agregar botón "📥 Exportar PDF" en vista mensual con gradiente verde
- [x] Se abre en nueva pestaña (target="_blank")
- [x] Descarga automática del archivo

**Routes implementadas:**
```php
Route::get('/reports/weekly/{year}/{week}/pdf', [ReportController::class, 'exportWeeklyPDF'])
    ->name('reports.weekly.pdf');
Route::get('/reports/monthly/{year}/{month}/pdf', [ReportController::class, 'exportMonthlyPDF'])
    ->name('reports.monthly.pdf');
```

**Nombres de archivos generados:**
- `reporte-semanal-{year}-semana-{week}.pdf`
- `reporte-mensual-{mes}-{year}.pdf`

**Tiempo real:** ~2 horas ✅

---

### ✅ FASE 3 - Links Compartibles (COMPLETADA 2025-12-15)

**Propósito:** Permitir compartir reportes de forma pública mediante links temporales con expiración automática, ideal para enviar a entrenadores.

**Base de Datos:**
- [x] Migración `create_report_shares_table`:
  - Campos: `user_id`, `report_type` (weekly/monthly), `year`, `period`, `token` (32 chars único)
  - `expires_at` (timestamp), `view_count`, `last_viewed_at`
  - Índices optimizados: `(token, expires_at)` y `(user_id, report_type, year, period)`

**Backend:**
- [x] Crear modelo `ReportShare` con métodos:
  ```php
  // Genera nuevo share o retorna existente si válido
  public static function createShare(int $userId, string $reportType, int $year, int $period, int $hoursValid = 24): self

  // Busca share válido (no expirado)
  public static function findValidByToken(string $token): ?self

  // Incrementa contador de vistas
  public function incrementViews(): void

  // Genera URL completa del share
  public function getShareUrl(): string

  // Limpieza de shares expirados (para comando/job)
  public static function cleanupExpired(): int
  ```

- [x] Scopes en ReportShare:
  - `valid()` - shares no expirados
  - `expired()` - shares vencidos

- [x] Métodos en `ReportController`:
  ```php
  // Genera link compartible semanal (retorna JSON)
  public function shareWeekly(int $year, int $week)

  // Genera link compartible mensual (retorna JSON)
  public function shareMonthly(int $year, int $month)

  // Muestra reporte público desde token (sin auth)
  public function showShared(string $token)
  ```

**Frontend:**
- [x] Crear componente `<x-public-layout>`:
  - Layout sin sidebar ni dashboard elements
  - Mantiene estética de la app (dark theme, colores, tipografía)
  - Header con logo y título del reporte
  - Footer con branding "mientreno.app"

- [x] Crear vistas públicas:
  - `resources/views/reports/public/weekly.blade.php` - Reporte semanal público
  - `resources/views/reports/public/monthly.blade.php` - Reporte mensual público
  - Mismo contenido que vistas privadas (métricas, comparativas, insights, detalle)
  - Aviso destacado con: usuario que compartió, fecha, expiración, contador de vistas

- [x] Botón "🔗 Compartir" en vistas privadas:
  - Diseño con gradiente fuscia (color primario de la app)
  - Ubicado junto a botones "Ver Mes" y "Exportar PDF"
  - Hace POST a endpoint de sharing vía fetch

- [x] Modal JavaScript para mostrar link:
  - Diseño coherente con app (dark theme, border fuscia)
  - Muestra URL completa y fecha de expiración
  - Botón "📋 Copiar Link" con feedback visual
  - Funcionalidad copy-to-clipboard
  - Cierre con botón o click en overlay

**Routes implementadas:**
```php
// Protegidas (require auth)
Route::post('/reports/weekly/{year}/{week}/share', [ReportController::class, 'shareWeekly'])
    ->name('reports.weekly.share');
Route::post('/reports/monthly/{year}/{month}/share', [ReportController::class, 'shareMonthly'])
    ->name('reports.monthly.share');

// Pública (sin auth)
Route::get('/share/{token}', [ReportController::class, 'showShared'])
    ->name('reports.shared');
```

**Características Implementadas:**
- ✅ Tokens únicos de 32 caracteres
- ✅ Expiración automática en 24 horas
- ✅ Prevención de duplicados (retorna share existente si válido)
- ✅ Tracking de vistas con timestamp
- ✅ Validación de expiración en cada acceso
- ✅ URLs amigables: `/share/{token}`
- ✅ Modal con copy-to-clipboard
- ✅ Diseño responsive y profesional
- ✅ Sin necesidad de login para acceder al reporte compartido

**Testing Realizado:**
- ✅ Creación de shares exitosa
- ✅ Prevención de duplicados funcional
- ✅ Incremento de view_count operativo
- ✅ Lookup por token exitoso
- ✅ Compilación de vistas sin errores
- ✅ Rutas registradas correctamente

**Tiempo real:** ~3 horas ✅

---

### FASE 4 - Gráficos y Visualizaciones ⏸️

**Librería:** Chart.js (ya conocido en web) o Laravel Charts

**Implementaciones:**
- [ ] Gráfico de distribución por tipo (donut chart)
- [ ] Gráfico de volumen semanal (bar chart)
- [ ] Gráfico de evolución de pace (line chart)
- [ ] Gráfico de cumplimiento (gauge/progress)

**En HTML:**
```blade
<canvas id="typeDistributionChart"></canvas>
<script>
    new Chart(ctx, {
        type: 'doughnut',
        data: {!! json_encode($chartData) !!}
    });
</script>
```

**En PDF:**
- Opción 1: Generar imagen del chart con Chart.js server-side (complejo)
- Opción 2: Usar tablas visuales con CSS (más simple)
- Opción 3: Librería PHP para gráficos (jpgraph, etc.)

**Recomendación:** Empezar con tablas visuales CSS para PDF, gráficos Chart.js solo para web

**Estimación:** ~2 horas

---

### FASE 5 - Comparativas e Insights Avanzados ⏸️

**Comparativas Automáticas:**
- [ ] Semana actual vs semana anterior
- [ ] Mes actual vs mes anterior
- [ ] Mes actual vs mismo mes año anterior (si hay datos)

**Cálculos:**
```php
// ReportService
public function getComparison($current, $previous)
{
    return [
        'distance' => [
            'current' => $current['total_distance'],
            'previous' => $previous['total_distance'],
            'diff' => $current['total_distance'] - $previous['total_distance'],
            'diff_percentage' => $this->calculatePercentage(...),
            'trend' => 'up' | 'down' | 'stable',
        ],
        'duration' => [...],
        'avg_pace' => [...],
        'sessions' => [...],
    ];
}
```

**Insights Automáticos:**
- [ ] "Mejor semana del mes" (mayor volumen)
- [ ] "Pace más rápido del período"
- [ ] "Mayor racha de días consecutivos"
- [ ] "Tipo de entreno más frecuente"
- [ ] Detección de tendencias (mejorando, estable, bajando)

**Vista:**
```blade
<div class="insights">
    <h3>🎯 Insights</h3>
    <ul>
        @foreach($insights as $insight)
            <li>{{ $insight->icon }} {{ $insight->message }}</li>
        @endforeach
    </ul>
</div>
```

**Ejemplos:**
- "🔥 Has corrido 5 días consecutivos, ¡tu mejor racha del mes!"
- "⚡ Tu pace mejoró 0:15 /km respecto a la semana pasada"
- "📈 Aumentaste el volumen un 12% este mes"
- "💪 Completaste el 90% de tus entrenamientos planificados"

**Estimación:** ~2.5 horas

---

### FASE 6 - UX Enhancements ⏸️

**Navegación Mejorada:**
- [ ] Dropdown para seleccionar semana/mes rápido
- [ ] Calendario visual para seleccionar período
- [ ] Breadcrumbs: Dashboard > Reportes > Semana 50

**Filtros Adicionales:**
- [ ] Por tipo de entrenamiento
- [ ] Solo completados / incluir planificados
- [ ] Rango de fechas custom

**Acciones Rápidas:**
- [ ] "Comparar con semana pasada" (botón)
- [ ] "Ver mes completo" desde vista semanal
- [ ] "Enviar por email" (futuro, requiere integración)

**Historial de Reportes:**
- [ ] Lista de reportes generados recientemente
- [ ] Re-descargar PDF generado anteriormente
- [ ] Marcadores/favoritos de reportes importantes

**Responsive:**
- [ ] Vista móvil optimizada
- [ ] Gráficos adaptables
- [ ] Tablas scrolleables en mobile

**Estimación:** ~2 horas

---

## Estructura de Datos

### ReportService - Estructura de Retorno

```php
// getWeeklyReport() retorna:
[
    'period' => [
        'type' => 'weekly',
        'year' => 2025,
        'week' => 50,
        'start_date' => '2025-12-08',
        'end_date' => '2025-12-14',
        'label' => 'Semana 50, 2025',
    ],

    'summary' => [
        'total_distance' => 150.2,
        'total_duration' => 45900, // segundos
        'total_sessions' => 8,
        'avg_pace' => 304, // seg/km
        'avg_heart_rate' => 158,
        'elevation_gain' => 1240,
    ],

    'compliance' => [
        'planned' => 10,
        'completed' => 8,
        'skipped' => 2,
        'percentage' => 80,
        'skipped_reasons' => [
            ['date' => '2025-12-10', 'reason' => 'Lluvia'],
            ['date' => '2025-12-12', 'reason' => 'Lesión menor'],
        ],
    ],

    'distribution' => [
        'easy_run' => ['count' => 3, 'distance' => 45, 'percentage' => 30],
        'long_run' => ['count' => 2, 'distance' => 40, 'percentage' => 26.7],
        'intervals' => ['count' => 2, 'distance' => 20, 'percentage' => 13.3],
        'tempo' => ['count' => 1, 'distance' => 15, 'percentage' => 10],
        // ...
    ],

    'comparison' => [
        'previous_period' => [...], // Semana anterior
        'diff' => [
            'distance' => ['value' => 15.2, 'percentage' => 11, 'trend' => 'up'],
            'duration' => ['value' => 2700, 'percentage' => 6, 'trend' => 'up'],
            'pace' => ['value' => -12, 'percentage' => -4, 'trend' => 'up'], // -12 seg = mejora
            'sessions' => ['value' => 1, 'percentage' => 14, 'trend' => 'up'],
        ],
    ],

    'goals_progress' => [
        // Array de goals activos con su progreso en este período
    ],

    'workouts' => [
        // Collection de workouts del período con todas sus propiedades
    ],

    'insights' => [
        ['icon' => '🔥', 'message' => 'Tu mejor racha del mes: 5 días consecutivos'],
        ['icon' => '⚡', 'message' => 'Pace mejoró 0:15 /km vs semana pasada'],
        // ...
    ],
]
```

---

## Consideraciones Técnicas

### Cálculo de Semanas

**ISO 8601** (estándar internacional):
- Primera semana del año: la que contiene el primer jueves
- Semanas van de lunes a domingo
- PHP: `date('W')` usa ISO 8601

```php
// Carbon helpers
$weekNumber = now()->week; // o ->weekOfYear
$weekStart = now()->startOfWeek(); // Lunes
$weekEnd = now()->endOfWeek(); // Domingo
```

### Optimizaciones

**Caching:**
```php
// Cachear reportes generados (1 hora de TTL)
$cacheKey = "report.weekly.{$userId}.{$year}.{$week}";
$report = Cache::remember($cacheKey, 3600, function() {
    return $this->reportService->getWeeklyReport(...);
});
```

**Invalidar cache cuando:**
- Se crea/edita/elimina un workout del período
- Se actualiza un goal vinculado

**Eager Loading:**
```php
$workouts = $user->workouts()
    ->with('race', 'user') // Evitar N+1
    ->whereBetween('date', [$start, $end])
    ->get();
```

### Performance PDF

**Consideraciones:**
- PDF generación puede ser lenta (2-5 segundos)
- Mostrar loading spinner al usuario
- Considerar queue jobs para reportes grandes
- Limitar número de gráficos en PDF

```php
// Opción: Queue job para PDF
dispatch(new GenerateReportPDF($user, $year, $week));
```

---

## Testing

### Test Cases Importantes

**ReportService:**
- [ ] `test_weekly_report_calculates_correct_metrics()`
- [ ] `test_monthly_report_includes_all_workouts()`
- [ ] `test_comparison_shows_correct_trends()`
- [ ] `test_insights_are_generated()`
- [ ] `test_distribution_percentages_sum_100()`

**ReportController:**
- [ ] `test_weekly_view_loads_correctly()`
- [ ] `test_navigation_between_weeks()`
- [ ] `test_pdf_generation_returns_file()`
- [ ] `test_unauthorized_user_cannot_access()`

**Integración:**
- [ ] `test_report_updates_when_workout_added()`
- [ ] `test_cache_invalidation_works()`

---

## Rutas Completas

```php
// routes/web.php
Route::middleware('auth')->prefix('reports')->name('reports.')->group(function () {
    // Vista principal (redirect a weekly current)
    Route::get('/', [ReportController::class, 'index'])->name('index');

    // Reportes semanales
    Route::get('/weekly', [ReportController::class, 'weekly'])->name('weekly');
    Route::get('/weekly/{year}/{week}', [ReportController::class, 'weekly'])->name('weekly.period');
    Route::get('/weekly/{year}/{week}/pdf', [ReportController::class, 'exportWeeklyPDF'])->name('weekly.pdf');

    // Reportes mensuales
    Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
    Route::get('/monthly/{year}/{month}', [ReportController::class, 'monthly'])->name('monthly.period');
    Route::get('/monthly/{year}/{month}/pdf', [ReportController::class, 'exportMonthlyPDF'])->name('monthly.pdf');

    // Comparativas (opcional, futuro)
    Route::get('/compare', [ReportController::class, 'compare'])->name('compare');
});
```

---

## Links en la App

**Sidebar:**
```blade
<a href="{{ route('reports.index') }}"
   class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
    <svg>...</svg>
    Reportes
</a>
```

**Dashboard:**
```blade
<x-card title="Reportes">
    <a href="{{ route('reports.weekly') }}">Ver reporte semanal</a>
    <a href="{{ route('reports.monthly') }}">Ver reporte mensual</a>
</x-card>
```

---

## Mockup de Vista (Detallado)

```blade
{{-- resources/views/reports/weekly.blade.php --}}
<x-app-layout>
    <div style="max-width:1200px;margin:0 auto;">

        {{-- Header con navegación --}}
        <div class="report-header">
            <div class="period-navigation">
                <a href="{{ route('reports.weekly.period', [$prevYear, $prevWeek]) }}" class="btn-ghost">
                    ← Semana anterior
                </a>

                <h1>{{ $report['period']['label'] }}</h1>
                <p class="subtitle">
                    {{ $report['period']['start_date']->format('d/m') }} -
                    {{ $report['period']['end_date']->format('d/m/Y') }}
                </p>

                @if(!$isCurrentWeek)
                    <a href="{{ route('reports.weekly.period', [$nextYear, $nextWeek]) }}" class="btn-ghost">
                        Semana siguiente →
                    </a>
                @endif
            </div>

            <div class="actions">
                <a href="{{ route('reports.monthly') }}" class="btn-ghost">
                    Ver mes completo
                </a>
                <a href="{{ route('reports.weekly.pdf', [$year, $week]) }}" class="btn-primary" target="_blank">
                    📥 Exportar PDF
                </a>
            </div>
        </div>

        {{-- Resumen General --}}
        <div class="metrics-grid">
            <x-metric-card label="Kilómetros" :value="$report['summary']['total_distance']" subtitle="km totales" />
            <x-metric-card label="Tiempo" :value="$report['summary']['formatted_duration']" subtitle="en movimiento" />
            <x-metric-card label="Sesiones" :value="$report['summary']['total_sessions']" subtitle="entrenamientos" />
            <x-metric-card label="Pace Promedio" :value="$report['summary']['formatted_pace']" subtitle="min/km" />
        </div>

        {{-- Cumplimiento --}}
        <x-report-card title="Cumplimiento del Plan">
            <div class="compliance-stats">
                <div class="progress-bar">
                    <div class="fill" style="width:{{ $report['compliance']['percentage'] }}%"></div>
                </div>
                <p>{{ $report['compliance']['completed'] }} de {{ $report['compliance']['planned'] }} completados ({{ $report['compliance']['percentage'] }}%)</p>

                @if(count($report['compliance']['skipped_reasons']) > 0)
                    <div class="skipped-list">
                        <strong>Entrenamientos saltados:</strong>
                        <ul>
                            @foreach($report['compliance']['skipped_reasons'] as $skip)
                                <li>{{ $skip['date'] }}: {{ $skip['reason'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </x-report-card>

        {{-- Distribución por tipo --}}
        <x-report-card title="Distribución por Tipo de Entrenamiento">
            <canvas id="distributionChart"></canvas>
            <table class="distribution-table">
                @foreach($report['distribution'] as $type => $data)
                    <tr>
                        <td>{{ $typeLabels[$type] }}</td>
                        <td>{{ $data['count'] }} sesiones</td>
                        <td>{{ $data['distance'] }} km</td>
                        <td>{{ $data['percentage'] }}%</td>
                    </tr>
                @endforeach
            </table>
        </x-report-card>

        {{-- Comparativa --}}
        <x-report-card title="Comparativa con Semana Anterior">
            <div class="comparison-grid">
                <x-metric-comparison
                    label="Distancia"
                    :current="$report['summary']['total_distance']"
                    :previous="$report['comparison']['previous_period']['total_distance']"
                    :diff="$report['comparison']['diff']['distance']"
                />
                {{-- Más comparativas... --}}
            </div>
        </x-report-card>

        {{-- Insights --}}
        @if(count($report['insights']) > 0)
            <x-report-card title="Insights">
                <ul class="insights-list">
                    @foreach($report['insights'] as $insight)
                        <li>{{ $insight['icon'] }} {{ $insight['message'] }}</li>
                    @endforeach
                </ul>
            </x-report-card>
        @endif

        {{-- Detalle de entrenamientos --}}
        <x-report-card title="Detalle de Entrenamientos">
            <x-workout-table :workouts="$report['workouts']" />
        </x-report-card>

    </div>
</x-app-layout>
```

---

## Próximos Pasos

1. **Revisar y aprobar** este documento
2. **Ajustar** lo que sea necesario
3. **Priorizar fases** (empezar por Fase 1)
4. **Estimar tiempo** total del proyecto
5. **Crear issues/tasks** en GitHub (opcional)
6. **Arrancar desarrollo** cuando estés listo

---

## Notas Finales

- Este sistema es extensible: se puede agregar reporte anual, por carrera, etc.
- PDF es clave para compartir con entrenador offline
- Insights automáticos agregan valor y engagement
- Comparativas ayudan a ver progreso real
- Cachear reportes mejora performance significativamente

---

## Estado Actual

**✅ Fase 1 - Core Report Views: COMPLETADA** (2025-12-15)
**✅ Fase 2 - Exportación PDF: COMPLETADA** (2025-12-15)
**✅ Fase 2.5 - Rediseño Visual PDFs: COMPLETADA** (2026-03-27)
**✅ Fase 3 - Links Compartibles: COMPLETADA** (2025-12-15)
**⏸️ Fase 4 - Gráficos y Visualizaciones: Pendiente** (~2 horas estimadas)
**⏸️ Fase 5 - Comparativas Avanzadas: Pendiente** (~2.5 horas estimadas)
**⏸️ Fase 6 - UX Enhancements: Pendiente** (~2 horas estimadas)

**Progreso:** ~10 de 14.5 horas completadas

---

**Documento creado**: 2025-12-12
**Última actualización**: 2026-03-27
**Estado**: Fases 1, 2, 2.5 y 3 completadas - PDFs con diseño profesional, corrección de bugs Unicode, optimización de espacio
