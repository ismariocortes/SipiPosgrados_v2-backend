<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Respuestas JSON homogéneas para clientes y documentación (OpenAPI).
 *
 * Convención: `success` indica resultado global; `data` carga útil; `errors` detalle de validación o códigos de negocio.
 */
final class ApiResponse
{
    public static function success(
        mixed $data = null,
        int $status = Response::HTTP_OK,
        ?string $message = null,
        array $meta = [],
    ): JsonResponse {
        $payload = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if ($meta !== []) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }

    public static function error(
        string $message,
        int $status = Response::HTTP_BAD_REQUEST,
        ?array $errors = null,
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors ?? new \stdClass,
        ], $status);
    }

    public static function validationError(array $errors, ?string $message = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message ?? 'Error de validación.',
            'errors' => $errors,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
