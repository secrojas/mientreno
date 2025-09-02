# mientreno
MiEntreno es una plataforma multi-actividad (multi-tenant por URL) construida con Laravel, pensada para clubes, studios y equipos. Cada actividad tiene su propio registro, login y dashboard). Arquitectura service-repository, Sanctum para API, y Pest para tests.

# MiEntreno

[![Laravel](https://img.shields.io/badge/Laravel-11-red)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777bb3)](https://www.php.net/)
[![Pest](https://img.shields.io/badge/Tests-Pest-9146FF)](https://pestphp.com/)
[![Style](https://img.shields.io/badge/Architecture-Service--Repository-0aa262)](#arquitectura)
[![Multi-tenant](https://img.shields.io/badge/Multi--tenant-Path%20based-0ea5e9)](#multi-actividad--multi-tenant)

**MiEntreno** es una plataforma **multi-actividad** para administrar entrenamientos y comunidades (run clubs, pilates, yoga, etc.).  
Cada actividad vive en su propia URL, con **registro**, **login** y **dashboard** aislados:

