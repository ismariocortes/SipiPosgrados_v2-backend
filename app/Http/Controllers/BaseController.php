<?php

namespace App\Http\Controllers;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Punto común para controladores HTTP: respuestas JSON y códigos HTTP coherentes.
 * La lógica de negocio debe vivir en servicios / acciones; aquí solo orquestación.
 */
abstract class BaseController extends Controller
{
    protected function ok(mixed $data = null, ?string $message = null, int $status = Response::HTTP_OK): JsonResponse
    {
        return ApiResponse::success($data, $status, $message);
    }

    protected function created(mixed $data = null, ?string $message = null): JsonResponse
    {
        return ApiResponse::success($data, Response::HTTP_CREATED, $message);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
