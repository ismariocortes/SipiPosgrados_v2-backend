<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catálogo de estatus del ciclo de vida del aspirante (extensible).
     */
    public function up(): void
    {
        Schema::create('user_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->timestamps();
        });

        $now = now();

        DB::table('user_statuses')->insert([
            [
                'code' => 'quick_registration',
                'name' => 'Registro rápido',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'with_folio',
                'name' => 'Con folio',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'profile_complete',
                'name' => 'Información completa',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('user_statuses');
    }
};
