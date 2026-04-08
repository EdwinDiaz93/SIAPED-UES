<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('periodo_id')->constrained('periodos_evaluacion')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'en_progreso', 'completada'])->default('pendiente');

            // Notas ponderadas de cada evaluador (0-10)
            $table->decimal('nota_estudiante', 5, 2)->nullable();
            $table->decimal('nota_jefe', 5, 2)->nullable();
            $table->decimal('nota_auto', 5, 2)->nullable();

            // Calculados
            $table->decimal('nota_promedio', 5, 2)->nullable(); // promedio de las 3 notas
            $table->decimal('puntaje', 5, 2)->nullable();       // nota_promedio * 0.50

            $table->timestamps();
            $table->unique(['docente_id', 'periodo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones');
    }
};
