<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add nro_legajo to personas
        Schema::table('personas', function (Blueprint $table) {
            $table->string('nro_legajo', 50)->nullable()->after('id');
        });

        // 2. Copy nro_legajo from casos (non-stubs) to personas
        DB::statement("
            UPDATE personas p
            INNER JOIN (
                SELECT persona_id, MIN(nro_legajo) AS nro_legajo
                FROM casos
                WHERE persona_id IS NOT NULL
                  AND nro_legajo NOT LIKE 'STUB-%'
                  AND nro_legajo IS NOT NULL
                GROUP BY persona_id
            ) c ON c.persona_id = p.id
            SET p.nro_legajo = c.nro_legajo
        ");

        // 3. Drop FK on caso_profesional.caso_id before renaming tables
        Schema::table('caso_profesional', function (Blueprint $table) {
            $table->dropForeign(['caso_id']);
        });

        // 4. Rename casos → expedientes
        Schema::rename('casos', 'expedientes');

        // 5. Drop nro_legajo from expedientes (now lives on personas)
        Schema::table('expedientes', function (Blueprint $table) {
            $table->dropColumn('nro_legajo');
        });

        // 6. Rename pivot table
        Schema::rename('caso_profesional', 'expediente_profesional');

        // 7. Rename caso_id → expediente_id
        Schema::table('expediente_profesional', function (Blueprint $table) {
            $table->renameColumn('caso_id', 'expediente_id');
        });

        // 8. Re-add FK with correct reference
        Schema::table('expediente_profesional', function (Blueprint $table) {
            $table->foreign('expediente_id')->references('id')->on('expedientes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('expediente_profesional', function (Blueprint $table) {
            $table->dropForeign(['expediente_id']);
        });

        Schema::table('expediente_profesional', function (Blueprint $table) {
            $table->renameColumn('expediente_id', 'caso_id');
        });

        Schema::rename('expediente_profesional', 'caso_profesional');

        Schema::table('expedientes', function (Blueprint $table) {
            $table->string('nro_legajo', 50)->nullable()->after('persona_id');
        });

        DB::statement("
            UPDATE expedientes e
            INNER JOIN personas p ON p.id = e.persona_id
            SET e.nro_legajo = p.nro_legajo
            WHERE p.nro_legajo IS NOT NULL
        ");

        Schema::rename('expedientes', 'casos');

        Schema::table('caso_profesional', function (Blueprint $table) {
            $table->foreign('caso_id')->references('id')->on('casos')->cascadeOnDelete();
        });

        Schema::table('personas', function (Blueprint $table) {
            $table->dropColumn('nro_legajo');
        });
    }
};
