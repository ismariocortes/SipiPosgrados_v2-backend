<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Errores HTTP explícitos de la API (reglas de negocio, conflictos, etc.).
 */
class ApiException extends HttpException
{
    public function __construct(
        string $message = '',
        int $statusCode = 400,
        ?\Throwable $previous = null,
        array $headers = [],
        int $code = 0,
    ) {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
