# SIPI Posgrados v2 (API)

Sistema de gestión de posgrados desarrollado como **API-first** usando:

- Laravel 11
- PHP 8.3+
- PostgreSQL
- Laravel Sanctum (autenticación por tokens)

---

## 🚀 Estado actual

El sistema cuenta con:

- Registro de usuarios con asignación de folio (UADY / CENEVAL)
- Login con folio + password
- Autenticación con tokens (Sanctum)
- Logout
- Rutas protegidas (`auth:sanctum`)

---

## 🧱 Requisitos

- PHP 8.3+
- Composer
- PostgreSQL

---

## ⚙️ Instalación

```bash
git clone <repo>
cd SipiPosgrados_v2

cp .env.example .env
composer install
php artisan key:generate
```

---

## 🗄️ Base de datos (PostgreSQL)

Configura tu `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sipi_posgrados
DB_USERNAME=tu_usuario
DB_PASSWORD=
```

---

## 🧱 Migraciones

```bash
php artisan migrate
```

---

## 🌱 Datos iniciales (IMPORTANTE)

### Roles

```sql
INSERT INTO roles (id, name, created_at, updated_at) VALUES
(1, 'admin', NOW(), NOW()),
(2, 'coordinador', NOW(), NOW()),
(3, 'aspirante', NOW(), NOW());
```

---

### Folios (ejemplo)

```sql
INSERT INTO folios_uady (value, is_used, created_at, updated_at)
VALUES ('ABC123456', false, NOW(), NOW());
```

---

## ▶️ Ejecutar servidor

```bash
php artisan serve
```

---

# 🔐 Autenticación (API)

## 📌 Registro

```http
POST /api/v1/auth/register
```

```json
{
  "email": "test@mail.com",
  "password": "123456",
  "identity_type": "curp",
  "identity_value": "CURP123456789012",
  "phone": "9991234567",
  "folio_type": "uady"
}
```

---

## 🔑 Login

```http
POST /api/v1/auth/login
```

```json
{
  "folio": "ABC123456",
  "password": "123456"
}
```

---

## 🔐 Obtener usuario autenticado

```http
GET /api/v1/me
Authorization: Bearer {token}
```

---

## 🔓 Logout

```http
POST /api/v1/auth/logout
Authorization: Bearer {token}
```

---

# 🧠 Arquitectura

Estructura basada en capas:

```
app/
 ├── Http/
 │   ├── Controllers/Api/V1
 │   ├── Requests
 ├── Services/
 ├── Repositories/
 ├── Models/
```

---

## 🔥 Puntos clave

- `FolioService` → asignación segura de folios (transacciones + lock)
- `RegisterRequest` → validación centralizada
- `AuthController` → endpoints de autenticación
- Sanctum → manejo de tokens

---

# 🐳 Docker (opcional)

El proyecto incluye configuración para Docker (Laravel Sail), pero:

👉 **NO es necesario para desarrollo actual**

Actualmente se usa:

- PHP local
- PostgreSQL local

---

# 🧪 Pruebas

```bash
php artisan test
```

---

# 🚀 Próximos módulos

- Roles y permisos
- Postulaciones
- Documentos
- Pagos

---

# 📌 Notas

- El campo `folio` es el identificador principal de usuario
- `identity_value` (CURP / pasaporte) es único
- `email` y `phone` son únicos
- Los folios se asignan desde tablas pre-cargadas
