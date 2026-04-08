<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluacion_labor_academica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluacion_id')->constrained('evaluaciones')->onDelete('cascade');
            $table->enum('tipo', ['estudiante', 'jefe', 'auto']);

            // El evaluador que llenó el cuestionario (null = promedio de estudiantes ingresado manualmente)
            $table->foreignId('evaluador_id')->nullable()->constrained('users')->onDelete('set null');

            // Puntajes individuales por criterio (JSON)
            // Estructura: {"criterio_1": 8, "criterio_2": 7, ...}
            $table->json('criterios');

            // Nota ponderada resultante (0-10)
            $table->decimal('nota_ponderada', 5, 2);

            $table->timestamps();
            $table->unique(['evaluacion_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluacion_labor_academica');
    }
};
