# API — Referencia para frontend (auth y registro rápido)

Documento orientado al equipo de frontend (proyecto separado). Backend: Laravel 11, Sanctum, prefijo `/api`.

---

## Base URL y prefijo

- Las rutas HTTP de la API van bajo el prefijo **`/api`** (configuración por defecto de Laravel).
- Versión actual: **`/api/v1/...`**

Ejemplo: `https://tu-dominio.com/api/v1/auth/login`

- **`Content-Type`**: `application/json` en cuerpos JSON.

**Convención en este documento:** salvo que se indique otra cosa, una ruta escrita como `/auth/login` debe entenderse como **`/api/v1/auth/login`** (mismo prefijo **`/api/v1`** en todos los endpoints de esta guía).

---

## Convención de respuestas (importante)

Hay **dos estilos** según el caso:

| Caso | Formato |
|------|--------|
| **Éxito** en `register` / `login` / `logout` | JSON **plano**: `message` + datos (`user`, `token`, etc.). **No** incluye `success: true`. |
| **Error de validación** (422) | `{ "success": false, "message": "...", "errors": { "campo": ["..."] } }` |

El frontend debe tratar **422** leyendo `errors` (objeto: nombre de campo → array de mensajes).

Otros errores de API (`401` en rutas protegidas, etc.) pueden seguir el formato `success` / `message` según el manejador global de excepciones; los controladores de auth en éxito **no** usan el envoltorio `success` / `data`.

---

## 1. Registro rápido

**`POST /api/v1/auth/register`**

### Cuerpo (JSON)

| Campo | Tipo | Obligatorio | Notas |
|--------|------|-------------|--------|
| `identity_type` | string | Sí | Solo: **`curp`** o **`passport`** (minúsculas, como en el enum). |
| `identity_value` | string | Sí | **Normalización (CURP y pasaporte):** el backend pasa a **mayúsculas** y **elimina espacios** (y en la práctica cualquier espacio en blanco) antes de validar. Luego aplica las reglas específicas de `curp` o `passport`. |
| `email` | string | Sí | Email válido, máx. 255 caracteres. **Único** en BD. |
| `phone` | string | Sí | Solo dígitos; el backend **quita** todo lo que no sea número. Debe quedar **10 dígitos**. **Único** en BD. |

### Reglas extra por tipo de identidad (después de normalizar)

- **`curp`**: exactamente **18** caracteres y formato tipo CURP (regex en servidor).
- **`passport`**: **1–50** caracteres, solo **A–Z y 0–9** (tras la misma normalización de `identity_value`: mayúsculas y sin espacios).

### Comportamiento UX recomendado

- Teléfono: mostrar o aceptar máscara, pero el valor enviado debe poder reducirse a **10 dígitos** (el servidor descarta no numéricos).
- CURP y pasaporte: el usuario puede escribir minúsculas o con espacios; el servidor unifica como arriba.

### Respuesta exitosa — **201 Created**

```json
{
  "message": "Usuario registrado correctamente",
  "user": {
    "id": 1,
    "email": "usuario@dominio.com",
    "folio": null,
    "user_status": {
      "id": 1,
      "code": "quick_registration"
    }
  }
}
```

- **`folio`** será `null` hasta un flujo futuro de asignación de folio.
- **`user_status.code`**: en registro rápido siempre **`quick_registration`** (mientras no cambie la lógica).

### Token de sesión tras el registro

- **El registro no devuelve `token`.** El flujo previsto es: registro exitoso → el usuario **inicia sesión** con `POST /api/v1/auth/login` (`email` + contraseña) para obtener el `token` de Sanctum.
- Si en el futuro el backend ofreciera token en el mismo `register`, habría que **actualizar este documento** y el contrato explícitamente; hoy **no** aplica.

### Contraseña

- La genera el **servidor**; **no** se devuelve en la respuesta JSON.
- En **entorno local** (`APP_ENV=local`) el backend puede escribir credenciales en el **log del servidor** (solo para pruebas del equipo backend).
- En el resto de entornos, el acceso real dependerá del **correo de confirmación** cuando exista (pendiente de implementación en backend).

### Errores típicos

- **422**: validación (`success: false`, `errors` por campo). Ejemplos: email/teléfono/identificador ya registrado (`unique`), CURP inválido, teléfono distinto de 10 dígitos, `identity_type` inválido.

#### Ejemplos de cuerpo **422** (estructura real)

Las claves de `errors` coinciden con los nombres de campo enviados: `identity_type`, `identity_value`, `email`, `phone`. Los textos del array pueden variar según idioma/reglas de Laravel; lo estable para el front son las **claves**.

**Email duplicado (`unique`):**

```json
{
  "success": false,
  "message": "Error de validación.",
  "errors": {
    "email": [
      "The email has already been taken."
    ]
  }
}
```

**CURP inválido (formato o longitud tras normalizar):**

```json
{
  "success": false,
  "message": "Error de validación.",
  "errors": {
    "identity_value": [
      "The identity value field must be 18 characters.",
      "The identity value field format is invalid."
    ]
  }
}
```

*(Puede devolverse uno o varios mensajes por el mismo campo según falle una u otra regla.)*

---

## 2. Login

**`POST /api/v1/auth/login`**

El acceso es por **correo y contraseña** (no por folio).

### Cuerpo (JSON)

| Campo | Obligatorio |
|--------|-------------|
| `email` | Sí |
| `password` | Sí |

### Respuesta exitosa — **200 OK**

```json
{
  "message": "Login exitoso",
  "token": "<token_plano_de_Sanctum>",
  "user": {
    "id": 1,
    "folio": null,
    "email": "usuario@dominio.com",
    "user_status": {
      "id": 1,
      "code": "quick_registration"
    }
  }
}
```

- Guardar **`token`** y enviarlo en peticiones autenticadas (ver sección siguiente).

### Error — **401 Unauthorized**

Respuesta **del endpoint de login** (credenciales incorrectas o usuario inexistente):

```json
{
  "message": "Credenciales incorrectas"
}
```

- Es un JSON **mínimo** con solo `message` (sin `success` ni `errors`). Otros endpoints o el manejador global podrían formatear errores distinto en casos distintos; para **login**, el contrato actual es el anterior.

---

## 3. Autenticación en peticiones posteriores (Sanctum)

Para rutas protegidas con `auth:sanctum`:

**Cabecera**

```http
Authorization: Bearer <token>
Accept: application/json
```

El token es el string devuelto en `login` (token de Sanctum “personal access token”).

---

## 4. Otras rutas útiles

Todas estas rutas usan la **misma base** que el resto del documento: prefijo **`/api/v1`** + la ruta indicada en la segunda columna = **URL completa** (ejemplo: `GET https://tu-dominio.com/api/v1/me`).

| Método | URL completa | Auth | Descripción |
|--------|----------------|------|-------------|
| `GET` | `/api/v1/me` | Bearer | Devuelve el modelo usuario autenticado (JSON tal como lo serializa Laravel). |
| `POST` | `/api/v1/auth/logout` | Bearer | Invalida el token actual. |
| `POST` | `/api/v1/auth/logout-all` | Bearer | Invalida todos los tokens del usuario. |

---

## 5. Códigos de `user_status.code` (referencia)

Definidos en backend (tabla `user_statuses`); útiles para flujos en el front:

| `code` | Significado (nombre en BD) |
|--------|----------------------------|
| `quick_registration` | Registro rápido |
| `with_folio` | Con folio |
| `profile_complete` | Información completa |

Tras registro rápido el usuario queda en **`quick_registration`**.

---

## 6. Resumen para implementación en el front

1. Formulario registro: **4 campos** → `identity_type`, `identity_value`, `email`, `phone` (sin contraseña).
2. Tras registro: **no** hay `token` ni contraseña en la respuesta; el usuario debe **hacer login** para obtener el `token` (salvo cambio futuro documentado).
3. Tras registro: mostrar mensaje de éxito; en producción la contraseña la recibirá por correo cuando exista ese flujo.
4. Login: **`email` + `password`**; guardar **`token`** (localStorage / memoria segura según criterio del front).
5. Llamadas autenticadas: **`Authorization: Bearer`**.
6. Errores 422: leer **`errors`** con la forma `{ "campo": ["mensaje"] }` (ver ejemplos arriba).
7. **`folio`** en usuario puede ser **`null`** hasta procesos futuros del backend.

---

*Última actualización alineada con el backend del repositorio; ante cambios de contrato, conviene versionar este documento o generar OpenAPI.*
