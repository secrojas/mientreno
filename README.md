# MiEntreno
MiEntreno es una plataforma multi-actividad (multi-tenant por URL) construida con Laravel, pensada para clubes, studios y equipos. Cada actividad tiene su propio registro, login y dashboard). Arquitectura service-repository, Sanctum para API, y Pest para tests.

# MiEntreno

[![Laravel](https://img.shields.io/badge/Laravel-11-red)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777bb3)](https://www.php.net/)
[![Pest](https://img.shields.io/badge/Tests-Pest-9146FF)](https://pestphp.com/)
[![Style](https://img.shields.io/badge/Architecture-Service--Repository-0aa262)](#arquitectura)
[![Multi-tenant](https://img.shields.io/badge/Multi--tenant-Path%20based-0ea5e9)](#multi-actividad--multi-tenant)

**MiEntreno** es una plataforma **multi-actividad** para administrar entrenamientos y comunidades (run clubs, pilates, yoga, etc.).  
Cada actividad vive en su propia URL, con **registro**, **login** y **dashboard** aislados.


> Pensada para escalar con una arquitectura clara (**service-repository**), API segura con **Sanctum** y tests con **Pest**.

---

## 🚀 Características

- **Multi-actividad (multi-tenant por path)**: aislamiento por `/{business:slug}`.
- **Auth scoped por actividad**: el usuario se registra/loguea dentro del negocio actual.
- **API de plataforma (superadmin)**: crear/editar actividades vía API (Sanctum).
- **Arquitectura limpia**: Services + Repositories + Contracts (interfaces).
- **UI neutra**: layout básico con Tailwind listo para personalizar.
- **Tests**: feature + unit con Pest.

---

## 🧭 Multi-actividad / Multi-tenant

- Tabla `businesses` con `slug` **único** (p. ej. `run-club-mdp`).
- Rutas con prefijo `/{business:slug}` y middleware que fija el *tenant actual*.
- Modelos con `business_id` (+ trait `Tenantable`) para scoping automático.
- Registro/login **dentro** del negocio actual.

---

## 🧱 Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Auth**: Laravel Breeze (Blade + Tailwind) y **Sanctum** (API tokens)
- **Tests**: Pest
- **DB**: MySQL o PostgreSQL
- **Front**: Tailwind (puede reemplazarse por Bootstrap si se prefiere)

---

## 🛠️ Requisitos

- PHP 8.2+, Composer 2.x
- Node 18/20, NPM
- MySQL 8+ o PostgreSQL 13+
- Extensiones típicas de Laravel (mbstring, pdo, etc.)

---

## ⚡ Quick Start

```bash
# 1) Clonar
git clone https://github.com/<tu-usuario>/mientreno.git
cd mientreno

# 2) Dependencias
composer install
npm install && npm run build

# 3) Env & Key
cp .env.example .env
php artisan key:generate

# 4) Configurar DB en .env y migrar
php artisan migrate --seed

# 5) Iniciar servidor
php artisan serve


