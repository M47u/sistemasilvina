<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('casos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_recepcion');
            $table->string('nro_legajo');
            $table->string('nro_expediente');
            $table->foreignId('tipo_expediente_id')->constrained('tipos_expediente');
            $table->string('apellido_nombre');
            $table->string('dni');
            $table->foreignId('localidad_id')->constrained('localidades');
            $table->string('barrio')->nullable();
            $table->string('telefono')->nullable();
            $table->string('denunciado')->nullable();
            $table->text('resumen')->nullable();
            $table->boolean('acepta_atencion')->default(false);
            $table->boolean('servicio_legal')->default(false);
            $table->boolean('servicio_psicologico')->default(false);
            $table->boolean('servicio_social')->default(false);
            $table->boolean('archivado')->default(false);
            $table->text('observaciones')->nullable();
            $table->date('fecha_devolucion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('casos');
    }
};
