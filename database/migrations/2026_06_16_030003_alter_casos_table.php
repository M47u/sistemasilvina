<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            // Nueva FK a personas (nullable: se rellena durante la migración del Excel)
            $table->foreignId('persona_id')->nullable()->after('id')->constrained('personas')->nullOnDelete();

            // Nuevos campos operativos
            $table->string('via_acceso')->nullable()->after('tipo_expediente_id');
            $table->boolean('urgente')->default(false)->after('via_acceso');
            $table->foreignId('creado_por')->nullable()->after('fecha_devolucion')->constrained('users')->nullOnDelete();

            // nro_expediente pasa a ser nullable (hay casos sin número formal)
            $table->string('nro_expediente')->nullable()->change();

            // Los campos de persona se vuelven nullable para convivir con el script de migración
            $table->string('apellido_nombre')->nullable()->change();
            $table->string('dni')->nullable()->change();
            $table->foreignId('localidad_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('persona_id');
            $table->dropConstrainedForeignId('creado_por');
            $table->dropColumn(['via_acceso', 'urgente']);

            $table->string('nro_expediente')->nullable(false)->change();
            $table->string('apellido_nombre')->nullable(false)->change();
            $table->string('dni')->nullable(false)->change();
            $table->foreignId('localidad_id')->nullable(false)->change();
        });
    }
};
