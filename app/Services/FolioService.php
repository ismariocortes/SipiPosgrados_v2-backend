<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class FolioService
{
    public function assign(string $type): string
    {
        $table = $type === 'ceneval' ? 'folios_ceneval' : 'folios_uady';

        return DB::transaction(function () use ($table) {

            $folio = DB::table($table)
                ->where('is_used', false)
                ->lockForUpdate()
                ->first();

            if (!$folio) {
                throw new \Exception("No hay folios disponibles en {$table}");
            }

            DB::table($table)
                ->where('id', $folio->id)
                ->update([
                    'is_used' => true,
                    'used_at' => now()
                ]);

            return $folio->value;
        });
    }
}