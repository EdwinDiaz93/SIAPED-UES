<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_promocion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('periodo_id')->nullable()->constrained('periodos_evaluacion')->onDelete('set null');
            $table->foreignId('formulario_id')->nullable()->constrained('formularios_consolidados')->onDelete('set null');

            $table->string('categoria_actual', 10);
            $table->string('categoria_solicitada', 10);

            $table->decimal('puntaje_obtenido', 6, 2);
            $table->decimal('puntaje_requerido', 6, 2);

            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');

            $table->foreignId('revisado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('fecha_revision')->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();

            // Un docente solo puede tener una solicitud pendiente a la vez
            $table->unique(['docente_id', 'estado'], 'unique_pendiente');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_promocion');
    }
};
