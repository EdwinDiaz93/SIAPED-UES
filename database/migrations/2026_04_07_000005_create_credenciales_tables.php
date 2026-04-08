<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Aspecto 3: Capacitación Didáctica-Pedagógica ─────────────────────
        Schema::create('credenciales_capacitacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipo', ['curso', 'diplomado_maestria']);
            $table->string('nombre');
            $table->string('institucion')->nullable();
            $table->unsignedSmallInteger('horas')->nullable(); // requerido solo para cursos
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('puntaje', 4, 2)->default(0);
            $table->timestamps();
        });

        // ── Aspecto 4: Proyección Social ─────────────────────────────────────
        Schema::create('credenciales_proyeccion_social', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('users')->onDelete('cascade');
            $table->string('nombre');
            $table->enum('responsabilidad', ['formulador', 'ejecutor', 'coordinador']);
            $table->enum('cobertura', ['local', 'regional', 'nacional']);
            $table->enum('duracion', ['lte3meses', '3a6meses', 'gt6meses']);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('puntaje', 4, 2)->default(0);
            $table->timestamps();
        });

        // ── Aspecto 5: Especialización ───────────────────────────────────────
        Schema::create('credenciales_especializacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipo', ['phd', 'maestria', 'curso']);
            $table->string('titulo');
            $table->string('institucion')->nullable();
            $table->unsignedSmallInteger('horas')->nullable(); // requerido para cursos
            $table->date('fecha');
            $table->decimal('puntaje', 5, 2)->default(0);
            $table->timestamps();
        });

        // ── Aspecto 6: Investigación y Publicaciones ─────────────────────────
        Schema::create('credenciales_investigacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipo', ['proyecto', 'publicacion', 'red', 'patente']);

            // Proyecto
            $table->enum('financiamiento', ['propio', 'institucional', 'externo'])->nullable();
            $table->enum('participacion', ['colaborador', 'investigador', 'coordinador'])->nullable();
            $table->enum('duracion_proyecto', ['lt1anio', '1a2anios', 'gt2anios'])->nullable();

            // Publicación
            $table->enum('tipo_publicacion', ['libro', 'capitulo', 'articulo_indexado', 'articulo_no_indexado'])->nullable();

            // Datos comunes
            $table->string('titulo');
            $table->date('fecha');
            $table->decimal('puntaje', 4, 2)->default(0);
            $table->timestamps();
        });

        // ── Aspecto 7: Seguimiento Curricular ────────────────────────────────
        Schema::create('credenciales_seguimiento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipo', ['grado_adicional', 'curso', 'coordinacion_comision', 'idioma']);
            $table->string('descripcion');
            $table->unsignedSmallInteger('horas')->nullable(); // solo para cursos
            $table->date('fecha');
            $table->decimal('puntaje', 4, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credenciales_seguimiento');
        Schema::dropIfExists('credenciales_investigacion');
        Schema::dropIfExists('credenciales_especializacion');
        Schema::dropIfExists('credenciales_proyeccion_social');
        Schema::dropIfExists('credenciales_capacitacion');
    }
};
