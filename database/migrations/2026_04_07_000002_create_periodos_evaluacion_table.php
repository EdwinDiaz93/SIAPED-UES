<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodos_evaluacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('anio');
            $table->enum('ciclo', ['I', 'II']);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('estado', ['pendiente', 'activo', 'cerrado'])->default('pendiente');
            $table->text('descripcion')->nullable();
            $table->timestamps();

            // Solo puede existir un periodo por año/ciclo
            $table->unique(['anio', 'ciclo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodos_evaluacion');
    }
};
