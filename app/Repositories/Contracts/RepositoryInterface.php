<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Contrato mínimo para acceso a datos; las implementaciones concretas usan Eloquent u otro driver.
 *
 * @template TModel of Model
 */
interface RepositoryInterface
{
    /** @return TModel|null */
    public function find(int|string $id): ?Model;

    /**
     * @return Collection<int, TModel>
     */
    public function all(): Collection;
}
