# Convenciones de nombres (Sipi Posgrados)

## Namespaces y carpetas

- `App\Http\Controllers\Api\V{n}` — controladores HTTP de la versión `n` de la API.
- `App\Modules\{Dominio}` — código de dominio por módulo (eventualmente extraíble a paquetes o microservicios).
- `App\Services\{Contexto}` — casos de uso; un servicio por flujo o agregado coherente.
- `App\Repositories\{Modelo}` — persistencia; implementan `App\Repositories\Contracts\RepositoryInterface`.
- `App\DTOs\{Contexto}` — objetos inmutables entre capas (`fromArray` / `toArray`).
- `App\Http\Requests\{Recurso}{Accion}Request` — p. ej. `StoreProgramRequest`.

## Archivos y clases

- **Controladores**: sufijo `Controller` (`ProgramController`).
- **Requests**: sufijo `Request` y verbo de acción cuando aplique (`UpdateApplicationRequest`).
- **Policies**: `{Modelo}Policy`.
- **Excepciones de negocio**: `{Contexto}Exception` o uso de `App\Exceptions\ApiException` con código HTTP explícito.

## Rutas

- Prefijo global: `/api` (bootstrap) + versión en `routes/api.php`: `/api/v1/...`.
- Nombres de ruta: `api.v1.{recurso}.{acción}` (registrar al definir rutas reales).

## Base de datos

- Tablas en plural, `snake_case` (`programs`, `application_documents`).
- Claves foráneas: `{tabla_singular}_id` (`program_id`).

## Respuestas JSON

- Usar siempre `App\Support\ApiResponse` o los helpers de `BaseController` para mantener el contrato estable.
