<?php

namespace App\DTOs;

/**
 * Objetos de transferencia inmutables entre capas (HTTP ↔ servicio ↔ persistencia).
 * Cada DTO concreto implementa `fromArray` / `toArray` según sus campos.
 */
abstract class BaseDto
{
    abstract public static function fromArray(array $data): static;

    abstract public function toArray(): array;
}
