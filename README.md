# Sipi Posgrados (base API)

Aplicación **API-first** sobre **Laravel 11**, **PHP 8.3+**, **PostgreSQL**, **Redis** (caché y colas), con estructura **por dominios/módulos** preparada para evolución a microservicios.

## Requisitos

- PHP 8.3+, Composer 2, Node (assets), PostgreSQL 14+, Redis 7+ (o Docker).

## Arranque local

```bash
cp .env.example .env
composer install
php artisan key:generate
```

Publicar migraciones y configuración de **Sanctum** (tokens API / SPA):

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

## Docker

Con el daemon de Docker en ejecución:

```bash
docker compose build
docker compose up -d
```

En `.env` para contenedores, usar `DB_HOST=postgres`, `REDIS_HOST=redis`, `APP_URL=http://localhost:8080` (o el puerto definido en `APP_PORT`).

Dentro del contenedor `app`: `composer install`, `php artisan key:generate`, `php artisan migrate`.

## Estructura relevante

- `app/Support/ApiResponse.php` — formato JSON estándar.
- `app/Support/ApiExceptionResponder.php` — errores JSON en rutas `api/*`.
- `app/Http/Controllers/BaseController.php` — respuestas HTTP comunes.
- `app/Services/BaseService.php`, `app/Repositories/*` — capa aplicación y datos.
- `routes/api.php` + `routes/api/v1/routes.php` — versionado `/api/v1/`.
- `app/Modules/*` — módulos de negocio (vacíos en el arranque).
- `docker/` — PHP-FPM y Nginx.

Convenciones de nombres: `docs/NAMING_CONVENTIONS.md`.

## Pruebas

```bash
php artisan test
```

## JWT (opcional)

Alternativa a tokens Sanctum: instalar un paquete JWT (p. ej. `php-open-source-saver/jwt-auth`), definir guard `api` en `config/auth.php` y middleware en `routes/api/v1/routes.php`. Variables de ejemplo en `.env.example`.
