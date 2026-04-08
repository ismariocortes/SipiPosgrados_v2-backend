<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('user_status_id')->nullable()->after('role_id')->constrained('user_statuses');
        });

        $withFolioId = DB::table('user_statuses')->where('code', 'with_folio')->value('id');
        DB::table('users')->update(['user_status_id' => $withFolioId]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('user_status_id')->nullable(false)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        /*
         * No usar ->change() sobre enum en PostgreSQL: doctrine genera SQL inválido.
         * En SQLite, ->change() con unique() intenta recrear un índice que ya existe.
         * Solo necesitamos permitir NULL en folio y folio_type.
         */
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE users ALTER COLUMN folio DROP NOT NULL');
            DB::statement('ALTER TABLE users ALTER COLUMN folio_type DROP NOT NULL');
        } elseif ($driver === 'sqlite') {
            /*
             * SQLite (según versión del motor) puede no soportar DROP NOT NULL vía SQL crudo.
             * No repetir ->unique() en change(): el índice ya existe y Laravel intentaría recrearlo.
             */
            Schema::table('users', function (Blueprint $table) {
                $table->char('folio', 9)->nullable()->change();
                $table->string('folio_type')->nullable()->change();
            });
        } elseif (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE users MODIFY folio CHAR(9) NULL');
            DB::statement("ALTER TABLE users MODIFY folio_type ENUM('ceneval','uady') NULL");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->char('folio', 9)->nullable()->unique()->change();
                $table->enum('folio_type', ['ceneval', 'uady'])->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE users ALTER COLUMN folio SET NOT NULL');
            DB::statement('ALTER TABLE users ALTER COLUMN folio_type SET NOT NULL');
        } elseif ($driver === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->char('folio', 9)->nullable(false)->change();
                $table->string('folio_type')->nullable(false)->change();
            });
        } elseif (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE users MODIFY folio CHAR(9) NOT NULL');
            DB::statement("ALTER TABLE users MODIFY folio_type ENUM('ceneval','uady') NOT NULL");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->char('folio', 9)->nullable(false)->unique()->change();
                $table->enum('folio_type', ['ceneval', 'uady'])->nullable(false)->change();
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('active');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['user_status_id']);
            $table->dropColumn('user_status_id');
        });
    }
};
