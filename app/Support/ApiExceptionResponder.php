<?php

namespace App\Support;

use App\Exceptions\ApiException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Mapea excepciones de dominio/framework a {@see ApiResponse} sin lógica de negocio.
 */
final class ApiExceptionResponder
{
    public static function toResponse(Throwable $e): JsonResponse
    {
        if ($e instanceof ValidationException) {
            return ApiResponse::validationError(
                $e->errors(),
                $e->getMessage() !== '' ? $e->getMessage() : null,
            );
        }

        if ($e instanceof AuthenticationException) {
            return ApiResponse::error('No autenticado.', Response::HTTP_UNAUTHORIZED);
        }

        if ($e instanceof AuthorizationException) {
            return ApiResponse::error(
                $e->getMessage() !== '' ? $e->getMessage() : 'No autorizado.',
                Response::HTTP_FORBIDDEN,
            );
        }

        if ($e instanceof ModelNotFoundException) {
            return ApiResponse::error('Recurso no encontrado.', Response::HTTP_NOT_FOUND);
        }

        if ($e instanceof ApiException) {
            return ApiResponse::error($e->getMessage(), $e->getStatusCode());
        }

        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
            $text = Response::$statusTexts[$status] ?? 'Error';

            return ApiResponse::error(
                $e->getMessage() !== '' ? $e->getMessage() : $text,
                $status,
            );
        }

        if (app()->hasDebugModeEnabled()) {
            return ApiResponse::error(
                'Error interno.',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                [
                    'exception' => $e::class,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
            );
        }

        return ApiResponse::error('Error interno.', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
