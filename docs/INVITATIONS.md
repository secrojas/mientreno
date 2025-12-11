# Sistema de Invitaciones - MiEntreno

## Descripción

MiEntreno soporta dos tipos de usuarios:

1. **Usuarios Individuales**: Corredores que se registran sin pertenecer a ningún grupo (`business_id = null`)
2. **Usuarios en un Business**: Miembros de un grupo de entrenamiento que se registran mediante un link de invitación

## Flujos de Registro

### Registro Individual

1. El usuario accede a `/register`
2. Completa el formulario (nombre, email, password, rol)
3. Se crea con `business_id = null`
4. Puede usar la app de forma individual

### Registro con Invitación

1. Un coach/admin genera un link de invitación para su business
2. Comparte el link con el nuevo miembro
3. El usuario accede al link (ej: `/register?invitation=TOKEN`)
4. Ve un mensaje indicando a qué grupo se está uniendo
5. Completa el formulario
6. Se crea automáticamente vinculado al business

## Generar Links de Invitación

### Opción 1: Comando Artisan (Recomendado)

```bash
php artisan invitation:generate {business_slug}
```

**Ejemplo:**
```bash
php artisan invitation:generate demo
```

**Output:**
```
✅ Token de invitación generado para: Demo Business

Link de invitación:
http://localhost/register?invitation=YnVzaW5lc3M6MQ==

Comparte este link con nuevos miembros para que se registren automáticamente en tu grupo.
```

### Opción 2: Desde Código PHP

```php
use App\Http\Controllers\Auth\v1\RegisterController;

$businessId = 1; // ID del business
$token = RegisterController::generateInvitationToken($businessId);
$url = url("/register?invitation={$token}");

// Ahora puedes enviar $url por email, WhatsApp, etc.
```

## Formato del Token

Los tokens son simples y seguros:
- Base64 encode de: `business:{business_id}`
- Ejemplo: `business:1` → `YnVzaW5lc3M6MQ==`

**Ventajas:**
- Fácil de generar y decodificar
- No expone el business_id directamente
- Liviano (no requiere tabla de tokens)
- No expira (puede reutilizarse)

## Casos de Uso

### Caso 1: Grupo de Running
Un coach crea su business "Running Team BA" y quiere que sus alumnos se registren:

```bash
php artisan invitation:generate running-team-ba
```

Comparte el link por WhatsApp al grupo de alumnos. Todos se registran automáticamente en el business.

### Caso 2: Usuario Individual
Un corredor accede directamente a `/register` sin invitación. Se registra como usuario individual y usa la app para sus entrenamientos personales.

### Caso 3: Migración de Individual a Business
Un usuario individual puede posteriormente ser agregado a un business por un admin (funcionalidad futura).

## Validaciones

- El token debe ser válido (decodificable)
- El business_id debe existir
- El email debe ser único globalmente (no puede haber dos usuarios con el mismo email, aunque estén en diferentes business)

## Seguridad

- Los tokens no expiran por diseño (son reutilizables)
- Un token comprometido solo permite registros en ese business específico
- Para invalidar un token, se requeriría cambiar el business_id (no implementado aún)

## Roadmap Futuro

**Mejoras posibles:**
1. Tokens con expiración
2. Límite de usos por token
3. Tokens de un solo uso
4. Panel de admin para generar y gestionar invitaciones desde la UI
5. Envío automático de invitaciones por email
6. Invitaciones con rol predefinido (invitar como "runner" o "coach")

---

**Última actualización**: 2025-12-11
